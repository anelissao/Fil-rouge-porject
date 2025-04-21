<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Brief;
use App\Models\Submission;
use Carbon\Carbon;

class SubmissionController extends Controller
{
    /**
     * Display a listing of the student's submissions.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $student = Auth::user();
        
        if (!$student->isStudent()) {
            return redirect('/')->with('error', 'You must be a student to access this page.');
        }
        
        $submissions = Submission::where('student_id', $student->id)
            ->with(['brief', 'brief.teacher:id,username,first_name,last_name'])
            ->withCount(['evaluations as total_evaluations'])
            ->withCount(['evaluations as completed_evaluations' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->latest()
            ->paginate(10);
        
        return view('student.submissions.index', compact('submissions'));
    }
    
    /**
     * Show the form for creating a new submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $student = Auth::user();
        
        if (!$student->isStudent()) {
            return redirect('/')->with('error', 'You must be a student to access this page.');
        }
        
        $briefId = $request->input('brief_id');
        
        if (!$briefId) {
            // If no brief_id provided, show a list of active briefs to choose from
            $briefs = Brief::where('status', 'published')
                ->where('deadline', '>=', Carbon::now())
                ->whereDoesntHave('submissions', function ($query) use ($student) {
                    $query->where('student_id', $student->id);
                })
                ->with('teacher:id,username,first_name,last_name')
                ->orderBy('deadline')
                ->get();
                
            return view('student.submissions.select_brief', compact('briefs'));
        }
        
        // Load the brief and show submission form
        $brief = Brief::with(['criteria', 'teacher:id,username,first_name,last_name'])
            ->findOrFail($briefId);
            
        // Check if this brief is active and the student hasn't already submitted
        if ($brief->status !== 'published' || $brief->deadline < Carbon::now()) {
            return redirect()->route('student.submissions.index')
                ->with('error', 'This brief is no longer accepting submissions.');
        }
        
        $hasSubmitted = $brief->submissions()
            ->where('student_id', $student->id)
            ->exists();
            
        if ($hasSubmitted) {
            return redirect()->route('student.submissions.index')
                ->with('error', 'You have already submitted to this brief.');
        }
        
        return view('student.submissions.create', compact('brief'));
    }
    
    /**
     * Store a newly created submission in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $student = Auth::user();
        
        if (!$student->isStudent()) {
            return redirect('/')->with('error', 'You must be a student to access this page.');
        }
        
        $validated = $request->validate([
            'brief_id' => ['required', 'exists:briefs,id'],
            'content' => ['required_without:file', 'nullable', 'string'],
            'file' => ['required_without:content', 'nullable', 'file', 'max:10240'], // 10MB max
        ]);
        
        // Check if the brief is active
        $brief = Brief::findOrFail($validated['brief_id']);
        
        if ($brief->status !== 'published' || $brief->deadline < Carbon::now()) {
            return redirect()->route('student.submissions.index')
                ->with('error', 'This brief is no longer accepting submissions.');
        }
        
        // Check if student has already submitted
        $hasSubmitted = $brief->submissions()
            ->where('student_id', $student->id)
            ->exists();
            
        if ($hasSubmitted) {
            return redirect()->route('student.submissions.index')
                ->with('error', 'You have already submitted to this brief.');
        }
        
        // Handle file upload
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('submissions/' . $student->id, 'public');
        }
        
        // Create submission
        $submission = new Submission([
            'brief_id' => $validated['brief_id'],
            'student_id' => $student->id,
            'content' => $validated['content'] ?? null,
            'file_path' => $filePath,
            'submission_date' => Carbon::now(),
            'status' => 'submitted',
        ]);
        
        $submission->save();
        
        return redirect()->route('student.submissions.show', $submission->id)
            ->with('success', 'Your submission has been received successfully.');
    }
    
    /**
     * Display the specified submission.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $student = Auth::user();
        
        if (!$student->isStudent()) {
            return redirect('/')->with('error', 'You must be a student to access this page.');
        }
        
        $submission = Submission::where('id', $id)
            ->where('student_id', $student->id)
            ->with(['brief', 'brief.criteria', 'brief.teacher:id,username,first_name,last_name'])
            ->withCount(['evaluations as total_evaluations'])
            ->withCount(['evaluations as completed_evaluations' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->firstOrFail();
            
        $evaluations = $submission->evaluations()
            ->with(['evaluator:id,username,first_name,last_name', 'answers'])
            ->get();
            
        return view('student.submissions.show', compact('submission', 'evaluations'));
    }
    
    /**
     * Download the submission file.
     *
     * @param  int  $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($id)
    {
        $student = Auth::user();
        
        if (!$student->isStudent()) {
            return redirect('/')->with('error', 'You must be a student to access this page.');
        }
        
        $submission = Submission::where('id', $id)
            ->where(function ($query) use ($student) {
                // Allow download for the owner or evaluators
                $query->where('student_id', $student->id)
                    ->orWhereHas('evaluations', function ($query) use ($student) {
                        $query->where('evaluator_id', $student->id);
                    });
            })
            ->firstOrFail();
            
        if (!$submission->file_path) {
            return back()->with('error', 'This submission does not have an attached file.');
        }
        
        return Storage::disk('public')->download($submission->file_path);
    }
} 