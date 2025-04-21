<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\EvaluationAssigned;
use App\Models\Brief;
use Illuminate\Support\Facades\Notification;

class EvaluationController extends Controller
{
    /**
     * Display a listing of the evaluations.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $teacher = Auth::user();
        $evaluations = Evaluation::whereHas('submission.brief', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })
        ->with(['evaluator:id,username,first_name,last_name', 'submission.student:id,username,first_name,last_name', 'submission.brief:id,title'])
        ->latest()
        ->paginate(15);
        
        return view('teacher.evaluations.index', compact('evaluations'));
    }
    
    /**
     * Show the form for assigning evaluations.
     *
     * @return \Illuminate\View\View
     */
    public function assignForm()
    {
        $teacher = Auth::user();
        
        // Get submissions without evaluations
        $submissions = Submission::whereHas('brief', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })
        ->whereDoesntHave('evaluations')
        ->with(['student:id,username,first_name,last_name', 'brief:id,title'])
        ->get();
        
        // Get potential evaluators (students)
        $evaluators = User::where('role', 'student')->get();
        
        return view('teacher.evaluations.assign', compact('submissions', 'evaluators'));
    }
    
    /**
     * Store a newly created evaluation in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'submission_id' => 'required|exists:submissions,id',
            'evaluator_id' => 'required|exists:users,id'
        ]);
        
        // Check if submission belongs to teacher's brief
        $teacher = Auth::user();
        $submission = Submission::findOrFail($validated['submission_id']);
        
        if ($submission->brief->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Create evaluation
        Evaluation::create([
            'submission_id' => $validated['submission_id'],
            'evaluator_id' => $validated['evaluator_id'],
            'status' => 'pending'
        ]);
        
        return redirect()->route('teacher.evaluations.index')
            ->with('success', 'Evaluation assigned successfully.');
    }

    /**
     * Show the form for assigning random evaluations.
     *
     * @return \Illuminate\View\View
     */
    public function randomForm()
    {
        $teacher = Auth::user();
        
        // Get briefs with submissions
        $briefs = Brief::where('teacher_id', $teacher->id)
            ->withCount('submissions')
            ->having('submissions_count', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('teacher.evaluations.random', compact('briefs'));
    }

    /**
     * Assign evaluations randomly.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignRandom(Request $request)
    {
        $validated = $request->validate([
            'brief_id' => 'required|exists:briefs,id',
            'evaluations_per_submission' => 'required|integer|min:1|max:5',
            'due_date' => 'nullable|date|after:today',
        ]);
        
        $teacher = Auth::user();
        $brief = Brief::where('teacher_id', $teacher->id)->findOrFail($validated['brief_id']);
        
        // Get all submissions for this brief
        $submissions = Submission::where('brief_id', $brief->id)->get();
        
        if ($submissions->count() < 2) {
            return back()->with('error', 'At least 2 submissions are required for random assignments.');
        }
        
        // Get all student IDs who submitted to this brief
        $studentIds = $submissions->pluck('student_id')->toArray();
        
        // Number of evaluations per submission
        $evaluationsPerSubmission = min($validated['evaluations_per_submission'], count($studentIds) - 1);
        
        DB::beginTransaction();
        
        try {
            $assignmentCount = 0;
            
            foreach ($submissions as $submission) {
                // Get existing evaluator IDs for this submission
                $existingEvaluatorIds = Evaluation::where('submission_id', $submission->id)
                    ->pluck('evaluator_id')
                    ->toArray();
                
                // Get potential evaluators (excluding the submitter and existing evaluators)
                $potentialEvaluatorIds = array_diff($studentIds, [$submission->student_id], $existingEvaluatorIds);
                
                // Skip if no potential evaluators are available
                if (empty($potentialEvaluatorIds)) {
                    continue;
                }
                
                // Shuffle the evaluator IDs to randomize
                shuffle($potentialEvaluatorIds);
                
                // Take only the number of evaluators needed
                $selectedEvaluatorIds = array_slice($potentialEvaluatorIds, 0, $evaluationsPerSubmission);
                
                // Create evaluations for each selected evaluator
                foreach ($selectedEvaluatorIds as $evaluatorId) {
                    $evaluation = new Evaluation([
                        'submission_id' => $submission->id,
                        'evaluator_id' => $evaluatorId,
                        'status' => 'pending',
                        'due_date' => $validated['due_date'] ?? null,
                    ]);
                    
                    $evaluation->save();
                    $assignmentCount++;
                    
                    // Send notification to student
                    $evaluator = User::find($evaluatorId);
                    try {
                        Notification::send($evaluator, new EvaluationAssigned($evaluation));
                    } catch (\Exception $e) {
                        // Log notification error but don't fail the request
                        \Log::error('Failed to send evaluation assignment notification: ' . $e->getMessage());
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('teacher.evaluations.index')
                ->with('success', $assignmentCount . ' random evaluation assignments created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'An error occurred while creating random assignments: ' . $e->getMessage());
        }
    }

    /**
     * Show evaluation details for a specific student.
     *
     * @param  int  $id
     * @param  int  $student_id
     * @return \Illuminate\View\View
     */
    public function showStudentEvaluation($id, $student_id)
    {
        $teacher = Auth::user();
        
        // Get the evaluation and ensure it belongs to a brief created by this teacher
        $evaluation = Evaluation::whereHas('submission.brief', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })
        ->with(['evaluator:id,username,first_name,last_name', 
                'submission.student:id,username,first_name,last_name', 
                'submission.brief:id,title,description',
                'answers.criterion'])
        ->where('id', $id)
        ->firstOrFail();
        
        // Check if the student is either the evaluator or the submission's student
        if ($evaluation->evaluator_id != $student_id && $evaluation->submission->student_id != $student_id) {
            return redirect()->back()->with('error', 'This student is not associated with this evaluation.');
        }
        
        // Check if this is the evaluator's view or the student being evaluated
        $isEvaluator = $evaluation->evaluator_id == $student_id;
        
        return view('teacher.evaluations.student_view', compact('evaluation', 'isEvaluator'));
    }

    /**
     * Show the details of a specific evaluation.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $teacher = Auth::user();
        
        // Get the evaluation and ensure it belongs to a brief created by this teacher
        $evaluation = Evaluation::whereHas('submission.brief', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })
        ->with([
            'evaluator:id,username,first_name,last_name', 
            'submission.student:id,username,first_name,last_name', 
            'submission.brief:id,title,description',
            'answers.criterion'
        ])
        ->findOrFail($id);
        
        // Get other students who could be assigned as evaluators
        $potentialEvaluators = User::where('role', 'student')
            ->where('id', '!=', $evaluation->submission->student_id)
            ->where('id', '!=', $evaluation->evaluator_id)
            ->get();
        
        return view('teacher.evaluations.show', compact('evaluation', 'potentialEvaluators'));
    }
} 