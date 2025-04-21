<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Brief;
use App\Models\Submission;
use App\Models\Evaluation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\EvaluationAssigned;

class AssignmentController extends Controller
{
    /**
     * Display the assignment management page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $teacher = Auth::user();
        
        if (!$teacher->isTeacher()) {
            return redirect('/')->with('error', 'You must be a teacher to access this page.');
        }
        
        // Get all briefs created by this teacher
        $briefs = Brief::where('teacher_id', $teacher->id)
                      ->withCount(['submissions', 'evaluations'])
                      ->orderBy('created_at', 'desc')
                      ->get();
        
        return view('teacher.assignments.index', compact('briefs'));
    }
    
    /**
     * Show the assignments for a specific brief.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $teacher = Auth::user();
        
        if (!$teacher->isTeacher()) {
            return redirect('/')->with('error', 'You must be a teacher to access this page.');
        }
        
        // Get the brief with its submissions and evaluations
        $brief = Brief::where('teacher_id', $teacher->id)
                    ->with(['submissions.evaluations', 'submissions.user'])
                    ->findOrFail($id);
        
        // Get all students who submitted work for this brief
        $submitters = $brief->submissions()->with('user')->get()->pluck('user');
        
        // Get all students who could evaluate (excluding those who didn't submit)
        $allStudents = User::where('role', 'student')
                          ->whereIn('id', $brief->submissions()->pluck('user_id'))
                          ->get();
        
        return view('teacher.assignments.show', compact('brief', 'submitters', 'allStudents'));
    }
    
    /**
     * Show the form for creating manual assignments.
     *
     * @param  int  $briefId
     * @return \Illuminate\View\View
     */
    public function create($briefId)
    {
        $teacher = Auth::user();
        
        if (!$teacher->isTeacher()) {
            return redirect('/')->with('error', 'You must be a teacher to access this page.');
        }
        
        // Get the brief
        $brief = Brief::where('teacher_id', $teacher->id)->findOrFail($briefId);
        
        // Get all submissions for this brief
        $submissions = Submission::where('brief_id', $briefId)
                               ->with('user', 'evaluations.evaluator')
                               ->get();
        
        // Get all potential evaluators (students who submitted to this brief)
        $students = User::whereHas('submissions', function($query) use ($briefId) {
                           $query->where('brief_id', $briefId);
                       })
                       ->where('role', 'student')
                       ->get();
        
        return view('teacher.assignments.create', compact('brief', 'submissions', 'students'));
    }
    
    /**
     * Store manually created evaluation assignments.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $teacher = Auth::user();
        
        if (!$teacher->isTeacher()) {
            return redirect('/')->with('error', 'You must be a teacher to access this page.');
        }
        
        $validated = $request->validate([
            'brief_id' => ['required', 'exists:briefs,id'],
            'assignments' => ['required', 'array'],
            'assignments.*.submission_id' => ['required', 'exists:submissions,id'],
            'assignments.*.evaluator_id' => ['required', 'exists:users,id'],
            'due_date' => ['nullable', 'date', 'after:today'],
        ]);
        
        $brief = Brief::where('teacher_id', $teacher->id)->findOrFail($validated['brief_id']);
        
        DB::beginTransaction();
        
        try {
            foreach ($validated['assignments'] as $assignment) {
                $submission = Submission::findOrFail($assignment['submission_id']);
                $evaluator = User::findOrFail($assignment['evaluator_id']);
                
                // Skip if the evaluator is the same as the submitter
                if ($submission->user_id == $evaluator->id) {
                    continue;
                }
                
                // Check if this assignment already exists
                $existingEvaluation = Evaluation::where('submission_id', $submission->id)
                                           ->where('evaluator_id', $evaluator->id)
                                           ->first();
                
                if (!$existingEvaluation) {
                    $evaluation = new Evaluation([
                        'submission_id' => $submission->id,
                        'evaluator_id' => $evaluator->id,
                        'status' => 'pending',
                        'assigned_at' => Carbon::now(),
                        'due_date' => $validated['due_date'] ?? null,
                    ]);
                    
                    $evaluation->save();
                    
                    // Send notification to student
                    try {
                        Notification::send($evaluator, new EvaluationAssigned($evaluation));
                    } catch (\Exception $e) {
                        // Log notification error but don't fail the request
                        \Log::error('Failed to send evaluation assignment notification: ' . $e->getMessage());
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('teacher.assignments.show', $brief->id)
                           ->with('success', 'Evaluation assignments created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'An error occurred while creating assignments: ' . $e->getMessage());
        }
    }
    
    /**
     * Create random assignments for a brief.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function random(Request $request)
    {
        $teacher = Auth::user();
        
        if (!$teacher->isTeacher()) {
            return redirect('/')->with('error', 'You must be a teacher to access this page.');
        }
        
        $validated = $request->validate([
            'brief_id' => ['required', 'exists:briefs,id'],
            'evaluations_per_submission' => ['required', 'integer', 'min:1', 'max:5'],
            'due_date' => ['nullable', 'date', 'after:today'],
        ]);
        
        $brief = Brief::where('teacher_id', $teacher->id)->findOrFail($validated['brief_id']);
        
        // Get all submissions for this brief
        $submissions = Submission::where('brief_id', $brief->id)->get();
        
        if ($submissions->count() < 2) {
            return back()->with('error', 'At least 2 submissions are required for random assignments.');
        }
        
        // Get all student IDs who submitted to this brief
        $studentIds = $submissions->pluck('user_id')->toArray();
        
        // Number of evaluations per submission
        $evaluationsPerSubmission = min($validated['evaluations_per_submission'], count($studentIds) - 1);
        
        DB::beginTransaction();
        
        try {
            foreach ($submissions as $submission) {
                // Get existing evaluator IDs for this submission
                $existingEvaluatorIds = $submission->evaluations()->pluck('evaluator_id')->toArray();
                
                // Get potential evaluators (excluding the submitter and existing evaluators)
                $potentialEvaluatorIds = array_diff($studentIds, [$submission->user_id], $existingEvaluatorIds);
                
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
                        'assigned_at' => Carbon::now(),
                        'due_date' => $validated['due_date'] ?? null,
                    ]);
                    
                    $evaluation->save();
                    
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
            
            return redirect()->route('teacher.assignments.show', $brief->id)
                           ->with('success', 'Random evaluation assignments created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'An error occurred while creating random assignments: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete an evaluation assignment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $teacher = Auth::user();
        
        if (!$teacher->isTeacher()) {
            return redirect('/')->with('error', 'You must be a teacher to access this page.');
        }
        
        $evaluation = Evaluation::with('submission.brief')
                             ->findOrFail($id);
        
        // Make sure the brief belongs to this teacher
        if ($evaluation->submission->brief->teacher_id != $teacher->id) {
            return redirect()->back()->with('error', 'You do not have permission to delete this evaluation.');
        }
        
        // Only allow deletion of pending or in-progress evaluations
        if ($evaluation->status == 'completed') {
            return redirect()->back()->with('error', 'Completed evaluations cannot be deleted.');
        }
        
        $briefId = $evaluation->submission->brief_id;
        
        $evaluation->delete();
        
        return redirect()->route('teacher.assignments.show', $briefId)
                       ->with('success', 'Evaluation assignment deleted successfully.');
    }
    
    /**
     * Reassign an evaluation to a different student.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reassign(Request $request, $id)
    {
        $teacher = Auth::user();
        
        if (!$teacher->isTeacher()) {
            return redirect('/')->with('error', 'You must be a teacher to access this page.');
        }
        
        $validated = $request->validate([
            'new_evaluator_id' => ['required', 'exists:users,id'],
        ]);
        
        $evaluation = Evaluation::with('submission.brief', 'submission.user')
                             ->findOrFail($id);
        
        // Make sure the brief belongs to this teacher
        if ($evaluation->submission->brief->teacher_id != $teacher->id) {
            return redirect()->back()->with('error', 'You do not have permission to reassign this evaluation.');
        }
        
        // Only allow reassignment of pending or in-progress evaluations
        if ($evaluation->status == 'completed') {
            return redirect()->back()->with('error', 'Completed evaluations cannot be reassigned.');
        }
        
        // Make sure the new evaluator is not the submitter
        if ($evaluation->submission->user_id == $validated['new_evaluator_id']) {
            return redirect()->back()->with('error', 'Students cannot evaluate their own submissions.');
        }
        
        // Update the evaluator
        $oldEvaluatorId = $evaluation->evaluator_id;
        $evaluation->evaluator_id = $validated['new_evaluator_id'];
        $evaluation->status = 'pending'; // Reset status
        $evaluation->assigned_at = Carbon::now();
        $evaluation->viewed_at = null;
        $evaluation->last_edited_at = null;
        $evaluation->save();
        
        // Send notification to new evaluator
        $newEvaluator = User::find($validated['new_evaluator_id']);
        try {
            Notification::send($newEvaluator, new EvaluationAssigned($evaluation));
        } catch (\Exception $e) {
            // Log notification error but don't fail the request
            \Log::error('Failed to send evaluation reassignment notification: ' . $e->getMessage());
        }
        
        return redirect()->route('teacher.assignments.show', $evaluation->submission->brief_id)
                       ->with('success', 'Evaluation reassigned successfully.');
    }

    /**
     * Show the form for manually assigning evaluations
     *
     * @return \Illuminate\View\View
     */
    public function manualForm()
    {
        $teacher = Auth::user();
        
        if ($teacher->role !== 'teacher') {
            return redirect('/')->with('error', 'You must be a teacher to access this page.');
        }
        
        // Get briefs created by the teacher
        $briefs = Brief::where('teacher_id', $teacher->id)
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Get potential evaluators (students)
        $students = User::where('role', 'student')
            ->orderBy('username')
            ->get();
            
        return view('teacher.evaluations.assign', compact('briefs', 'students'));
    }
    
    /**
     * Store manually assigned evaluations
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeManual(Request $request)
    {
        $teacher = Auth::user();
        
        if ($teacher->role !== 'teacher') {
            return redirect('/')->with('error', 'You must be a teacher to access this page.');
        }
        
        $validated = $request->validate([
            'brief_id' => ['required', 'exists:briefs,id'],
            'submission_id' => ['required', 'exists:submissions,id'],
            'evaluator_ids' => ['required', 'array', 'min:1'],
            'evaluator_ids.*' => ['exists:users,id'],
            'due_date' => ['nullable', 'date', 'after:today'],
        ]);
        
        // Make sure the brief belongs to the teacher
        $brief = Brief::where('id', $validated['brief_id'])
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();
            
        // Verify the submission is for this brief
        $submission = Submission::where('id', $validated['submission_id'])
            ->where('brief_id', $brief->id)
            ->firstOrFail();
            
        // Get the student who made the submission
        $submitter = $submission->student;
        
        // Ensure the submitter isn't among the evaluators
        $evaluatorIds = collect($validated['evaluator_ids'])->filter(function ($id) use ($submitter) {
            return $id != $submitter->id;
        })->toArray();
        
        if (empty($evaluatorIds)) {
            return back()->with('error', 'You cannot assign the submitter to evaluate their own work. Please choose different evaluators.');
        }
        
        // Calculate due date if provided
        $dueAt = null;
        if (!empty($validated['due_date'])) {
            $dueAt = Carbon::parse($validated['due_date']);
        }
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            $evaluations = [];
            
            // Create evaluations for each evaluator
            foreach ($evaluatorIds as $evaluatorId) {
                // Check if this evaluation already exists
                $existingEvaluation = Evaluation::where('submission_id', $submission->id)
                    ->where('evaluator_id', $evaluatorId)
                    ->first();
                    
                if ($existingEvaluation) {
                    // Update the due date if needed
                    if ($dueAt && $existingEvaluation->status !== 'completed') {
                        $existingEvaluation->due_at = $dueAt;
                        $existingEvaluation->save();
                    }
                    
                    $evaluations[] = $existingEvaluation;
                    continue;
                }
                
                // Create a new evaluation
                $evaluation = new Evaluation([
                    'submission_id' => $submission->id,
                    'evaluator_id' => $evaluatorId,
                    'status' => 'pending',
                    'due_at' => $dueAt,
                ]);
                
                $evaluation->save();
                $evaluations[] = $evaluation;
                
                // Send notification to the evaluator
                $evaluator = User::find($evaluatorId);
                if ($evaluator) {
                    try {
                        Notification::send($evaluator, new EvaluationAssigned($evaluation));
                    } catch (\Exception $e) {
                        // Log notification error but don't fail the transaction
                        \Log::error('Failed to send evaluation notification: ' . $e->getMessage());
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('teacher.evaluations.index')
                ->with('success', 'Evaluations assigned successfully to ' . count($evaluations) . ' students.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while assigning evaluations: ' . $e->getMessage());
        }
    }
    
    /**
     * Show the form for random assignment
     *
     * @return \Illuminate\View\View
     */
    public function randomForm()
    {
        $teacher = Auth::user();
        
        if ($teacher->role !== 'teacher') {
            return redirect('/')->with('error', 'You must be a teacher to access this page.');
        }
        
        // Get briefs created by the teacher that have submissions
        // Modified query to be compatible with PostgreSQL
        $briefs = Brief::where('teacher_id', $teacher->id)
            ->where('status', 'published')
            ->withCount('submissions')
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function($brief) {
                return $brief->submissions_count > 0;
            });
            
        return view('teacher.evaluations.random', compact('briefs'));
    }
    
    /**
     * Assign evaluations randomly
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeRandom(Request $request)
    {
        $teacher = Auth::user();
        
        if ($teacher->role !== 'teacher') {
            return redirect('/')->with('error', 'You must be a teacher to access this page.');
        }
        
        $validated = $request->validate([
            'brief_id' => ['required', 'exists:briefs,id'],
            'evaluations_per_submission' => ['required', 'integer', 'min:1', 'max:5'],
            'due_date' => ['nullable', 'date', 'after:today'],
        ]);
        
        // Make sure the brief belongs to the teacher
        $brief = Brief::where('id', $validated['brief_id'])
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();
            
        // Get all submissions for this brief
        $submissions = Submission::where('brief_id', $brief->id)
            ->with('student')
            ->get();
            
        if ($submissions->count() < 2) {
            return back()->with('error', 'This brief needs at least 2 submissions for peer evaluation.');
        }
        
        // Get all students who have submitted
        $submitters = $submissions->pluck('student')->keyBy('id');
        
        // Calculate due date if provided
        $dueAt = null;
        if (!empty($validated['due_date'])) {
            $dueAt = Carbon::parse($validated['due_date']);
        }
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            $evaluationCount = 0;
            
            // For each submission, assign random evaluators
            foreach ($submissions as $submission) {
                // Get potential evaluators (exclude the submitter)
                $potentialEvaluators = $submitters->except($submission->student_id)->values();
                
                // If we don't have enough students for the requested number of evaluations per submission
                $evaluationsPerSubmission = min($validated['evaluations_per_submission'], $potentialEvaluators->count());
                
                if ($evaluationsPerSubmission <= 0) {
                    continue;
                }
                
                // Shuffle potential evaluators and take the required number
                $selectedEvaluators = $potentialEvaluators->shuffle()->take($evaluationsPerSubmission);
                
                // Create evaluations for selected evaluators
                foreach ($selectedEvaluators as $evaluator) {
                    // Check if this evaluation already exists
                    $existingEvaluation = Evaluation::where('submission_id', $submission->id)
                        ->where('evaluator_id', $evaluator->id)
                        ->first();
                        
                    if ($existingEvaluation) {
                        // Update the due date if needed
                        if ($dueAt && $existingEvaluation->status !== 'completed') {
                            $existingEvaluation->due_at = $dueAt;
                            $existingEvaluation->save();
                        }
                        
                        $evaluationCount++;
                        continue;
                    }
                    
                    // Create a new evaluation
                    $evaluation = new Evaluation([
                        'submission_id' => $submission->id,
                        'evaluator_id' => $evaluator->id,
                        'status' => 'pending',
                        'due_at' => $dueAt,
                    ]);
                    
                    $evaluation->save();
                    $evaluationCount++;
                    
                    // Send notification to the evaluator
                    try {
                        Notification::send($evaluator, new EvaluationAssigned($evaluation));
                    } catch (\Exception $e) {
                        // Log notification error but don't fail the transaction
                        \Log::error('Failed to send evaluation notification: ' . $e->getMessage());
                    }
                }
            }
            
            DB::commit();
            
            if ($evaluationCount > 0) {
                return redirect()->route('teacher.evaluations.index')
                    ->with('success', 'Successfully assigned ' . $evaluationCount . ' evaluations randomly.');
            } else {
                return back()->with('warning', 'No new evaluations were assigned. All possible assignments already exist.');
            }
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while assigning evaluations: ' . $e->getMessage());
        }
    }
    
    /**
     * Cancel an evaluation
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel($id)
    {
        $teacher = Auth::user();
        
        if ($teacher->role !== 'teacher') {
            return redirect('/')->with('error', 'You must be a teacher to access this page.');
        }
        
        // Get the evaluation and ensure it belongs to a brief created by this teacher
        $evaluation = Evaluation::whereHas('submission.brief', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->where('id', $id)
            ->firstOrFail();
            
        // Only allow canceling pending or in_progress evaluations
        if ($evaluation->status === 'completed') {
            return back()->with('error', 'Completed evaluations cannot be canceled.');
        }
        
        // Delete the evaluation and any related answers
        DB::beginTransaction();
        
        try {
            // Delete answers first (foreign key constraints)
            $evaluation->answers()->delete();
            
            // Delete the evaluation
            $evaluation->delete();
            
            DB::commit();
            
            return redirect()->route('teacher.evaluations.index')
                ->with('success', 'Evaluation has been canceled.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while canceling the evaluation: ' . $e->getMessage());
        }
    }
} 