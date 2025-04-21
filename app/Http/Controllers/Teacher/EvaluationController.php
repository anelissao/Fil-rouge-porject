<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
} 