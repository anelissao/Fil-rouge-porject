<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Evaluation;
use App\Models\Submission;
use App\Models\EvaluationAnswer;
use App\Models\Feedback;
use Carbon\Carbon;

class EvaluationController extends Controller
{
    /**
     * Display a listing of the evaluations assigned to the student.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $student = Auth::user();
        
        if (!$student->isStudent()) {
            return redirect('/')->with('error', 'You must be a student to access this page.');
        }
        
        // Get evaluations assigned to the student
        $evaluations = Evaluation::where('evaluator_id', $student->id)
            ->with(['submission.brief', 'submission.student'])
            ->orderBy('status')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        // Calculate if evaluations are overdue
        $now = Carbon::now();
        foreach ($evaluations as $evaluation) {
            $evaluation->is_overdue = false;
            if (!empty($evaluation->due_at) && $evaluation->status !== 'completed') {
                $evaluation->is_overdue = Carbon::parse($evaluation->due_at)->isPast();
            }
        }
        
        // Get evaluations assigned to the student (duplicate of above, kept for view compatibility)
        $assignedEvaluations = Evaluation::where('evaluator_id', $student->id)
            ->where('status', '!=', 'completed')
            ->with(['submission.brief', 'submission.student'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'assigned_page');
            
        // Get evaluations received on the student's submissions
        $receivedEvaluations = Evaluation::whereHas('submission', function($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->with(['submission.brief', 'evaluator'])
            ->orderBy('status')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'received_page');
        
        return view('student.evaluations.index', compact('evaluations', 'assignedEvaluations', 'receivedEvaluations'));
    }
    
    /**
     * Show the form for creating/editing an evaluation.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $student = Auth::user();
        
        if (!$student->isStudent()) {
            return redirect('/')->with('error', 'You must be a student to access this page.');
        }
        
        // Get the evaluation and check if it belongs to the student
        $evaluation = Evaluation::where('id', $id)
            ->where('evaluator_id', $student->id)
            ->with(['submission.brief.criteria', 'submission.student:id,username,first_name,last_name'])
            ->firstOrFail();
            
        // Don't allow editing completed evaluations
        if ($evaluation->status === 'completed') {
            return redirect()->route('evaluations.show', $evaluation->id)
                ->with('error', 'This evaluation has already been completed and cannot be edited.');
        }
        
        // Get existing answers (if any)
        $existingAnswers = $evaluation->answers()
            ->get()
            ->keyBy('brief_criteria_id');
            
        return view('student.evaluations.edit', compact('evaluation', 'existingAnswers'));
    }
    
    /**
     * Update the evaluation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $student = Auth::user();
        
        if (!$student->isStudent()) {
            return redirect('/')->with('error', 'You must be a student to access this page.');
        }
        
        // Get the evaluation and check if it belongs to the student
        $evaluation = Evaluation::where('id', $id)
            ->where('evaluator_id', $student->id)
            ->firstOrFail();
            
        // Don't allow editing completed evaluations
        if ($evaluation->status === 'completed') {
            return redirect()->route('evaluations.show', $evaluation->id)
                ->with('error', 'This evaluation has already been completed and cannot be edited.');
        }
        
        // Validate request data
        $rules = [
            'overall_feedback' => ['nullable', 'string', 'max:5000'],
            'is_complete' => ['nullable', 'boolean'],
        ];
        
        // Add validation rules for each criterion
        foreach ($evaluation->submission->brief->criteria as $criterion) {
            $rules["criteria.{$criterion->id}.valid"] = ['required', 'boolean'];
            $rules["criteria.{$criterion->id}.comment"] = ['nullable', 'string', 'max:1000'];
        }
        
        $validated = $request->validate($rules);
        
        // Begin database transaction
        \DB::beginTransaction();
        
        try {
            // Update or create answers for each criterion
            foreach ($validated['criteria'] as $criterionId => $data) {
                // Check if an answer for this criterion already exists
                $answer = EvaluationAnswer::updateOrCreate(
                    [
                        'evaluation_id' => $evaluation->id,
                        'brief_criteria_id' => $criterionId,
                    ],
                    [
                        'is_valid' => $data['valid'],
                        'comment' => $data['comment'] ?? null,
                    ]
                );
            }
            
            // Update the evaluation status if the form is being submitted (not saved as draft)
            if (isset($validated['is_complete']) && $validated['is_complete']) {
                $evaluation->status = 'completed';
                $evaluation->completed_at = Carbon::now();
                
                // Create overall feedback
                if (!empty($validated['overall_feedback'])) {
                    Feedback::updateOrCreate(
                        [
                            'evaluation_id' => $evaluation->id,
                        ],
                        [
                            'content' => $validated['overall_feedback'],
                            'created_by' => $student->id,
                        ]
                    );
                }
            }
            
            // Always update updated_at
            $evaluation->touch();
            $evaluation->save();
            
            \DB::commit();
            
            if (isset($validated['is_complete']) && $validated['is_complete']) {
                return redirect()->route('evaluations.show', $evaluation->id)
                    ->with('success', 'Evaluation has been completed successfully!');
            } else {
                return redirect()->route('evaluations.edit', $evaluation->id)
                    ->with('success', 'Your progress has been saved.');
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withInput()->with('error', 'An error occurred while saving the evaluation. Please try again.');
        }
    }
    
    /**
     * Display the specified evaluation.
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
        
        // Get the evaluation and check if it's related to the student (either as evaluator or submission owner)
        $evaluation = Evaluation::where(function ($query) use ($student, $id) {
                $query->where('id', $id)
                      ->where('evaluator_id', $student->id);
            })
            ->orWhereHas('submission', function ($query) use ($student, $id) {
                $query->where('evaluation_id', $id)
                      ->where('student_id', $student->id);
            })
            ->with([
                'submission.brief',
                'submission.student:id,username,first_name,last_name',
                'evaluator:id,username,first_name,last_name',
                'answers.criterion',
                'feedback'
            ])
            ->firstOrFail();
            
        // Fetch the feedback
        $feedback = $evaluation->feedback()->first();
        
        return view('student.evaluations.show', compact('evaluation', 'feedback'));
    }
} 