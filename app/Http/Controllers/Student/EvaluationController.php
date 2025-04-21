<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Brief;
use App\Models\Submission;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;
use App\Models\Feedback;
use Carbon\Carbon;

class EvaluationController extends Controller
{
    /**
     * Display a listing of evaluations assigned to the student.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $student = Auth::user();
        
        if (!$student->isStudent()) {
            return redirect('/')->with('error', 'You must be a student to access this page.');
        }
        
        // Evaluations the student needs to perform
        $assignedEvaluations = Evaluation::where('evaluator_id', $student->id)
            ->with([
                'submission.brief:id,title',
                'submission.student:id,username,first_name,last_name',
            ])
            ->orderBy('status')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'assigned_page');
        
        // Evaluations received on the student's submissions
        $receivedEvaluations = Evaluation::whereHas('submission', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->with([
                'evaluator:id,username,first_name,last_name',
                'submission.brief:id,title',
                'feedback',
            ])
            ->orderBy('status')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'received_page');
        
        return view('student.evaluations.index', compact('assignedEvaluations', 'receivedEvaluations'));
    }
    
    /**
     * Show the form for completing an evaluation.
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
        
        $evaluation = Evaluation::where('id', $id)
            ->where('evaluator_id', $student->id)
            ->where('status', '!=', 'completed')
            ->with([
                'submission.brief.criteria',
                'submission.student:id,username,first_name,last_name',
                'answers',
            ])
            ->firstOrFail();
        
        // Load the answers into a collection indexed by criteria_id for easier access
        $existingAnswers = $evaluation->answers->keyBy('brief_criteria_id');
        
        return view('student.evaluations.edit', compact('evaluation', 'existingAnswers'));
    }
    
    /**
     * Update the specified evaluation with evaluation answers.
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
        
        $evaluation = Evaluation::where('id', $id)
            ->where('evaluator_id', $student->id)
            ->where('status', '!=', 'completed')
            ->with('submission.brief.criteria')
            ->firstOrFail();
        
        // Build validation rules based on brief criteria
        $rules = [];
        foreach ($evaluation->submission->brief->criteria as $criterion) {
            $rules["criteria.{$criterion->id}.valid"] = ['required', 'boolean'];
            $rules["criteria.{$criterion->id}.comment"] = ['nullable', 'string', 'max:1000'];
        }
        
        $rules['overall_comment'] = ['required', 'string', 'min:10', 'max:1000'];
        
        $validated = $request->validate($rules);
        
        // Start a database transaction
        \DB::beginTransaction();
        
        try {
            // Update the evaluation
            $evaluation->overall_comment = $validated['overall_comment'];
            $evaluation->status = 'completed';
            $evaluation->completed_at = Carbon::now();
            $evaluation->save();
            
            // Save each answer
            foreach ($validated['criteria'] as $criterionId => $data) {
                // Check if an answer for this criterion already exists
                $answer = EvaluationAnswer::firstOrNew([
                    'evaluation_id' => $evaluation->id,
                    'brief_criteria_id' => $criterionId
                ]);
                
                $answer->is_valid = $data['valid'];
                $answer->comment = $data['comment'] ?? null;
                $answer->save();
            }
            
            \DB::commit();
            
            return redirect()->route('student.evaluations.index')
                ->with('success', 'Evaluation completed successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            
            return back()->withInput()->with('error', 'An error occurred while saving your evaluation. Please try again.');
        }
    }
    
    /**
     * Display detailed evaluation results.
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
        
        // Allow viewing evaluations assigned to the student or evaluations of their submissions
        $evaluation = Evaluation::where(function ($query) use ($student, $id) {
                $query->where('id', $id)
                    ->where(function ($q) use ($student) {
                        $q->where('evaluator_id', $student->id)
                            ->orWhereHas('submission', function ($sq) use ($student) {
                                $sq->where('student_id', $student->id);
                            });
                    });
            })
            ->with([
                'evaluator:id,username,first_name,last_name',
                'submission.student:id,username,first_name,last_name',
                'submission.brief.criteria',
                'answers',
                'feedback',
            ])
            ->firstOrFail();
        
        return view('student.evaluations.show', compact('evaluation'));
    }
    
    /**
     * Store feedback for an evaluation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function feedback(Request $request, $id)
    {
        $student = Auth::user();
        
        if (!$student->isStudent()) {
            return redirect('/')->with('error', 'You must be a student to access this page.');
        }
        
        // Ensure the evaluation is for one of the student's submissions
        $evaluation = Evaluation::whereHas('submission', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->where('id', $id)
            ->where('status', 'completed')
            ->firstOrFail();
        
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:10', 'max:500'],
        ]);
        
        // Check if feedback already exists
        $feedback = Feedback::firstOrNew([
            'evaluation_id' => $evaluation->id,
        ]);
        
        $feedback->rating = $validated['rating'];
        $feedback->comment = $validated['comment'];
        $feedback->save();
        
        return redirect()->route('student.evaluations.show', $evaluation->id)
            ->with('success', 'Thank you for your feedback on this evaluation.');
    }
} 