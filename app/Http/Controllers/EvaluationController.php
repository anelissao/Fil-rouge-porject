<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EvaluationController extends Controller
{
    /**
     * Display the specified evaluation.
     *
     * @param  \App\Models\Evaluation  $evaluation
     * @return \Illuminate\Http\Response
     */
    public function show(Evaluation $evaluation)
    {
        $user = Auth::user();
        
        // Check if the user has permission to view this evaluation
        if ($user->hasRole('teacher')) {
            // Teachers can view evaluations they are assigned to or evaluations for submissions from their briefs
            $isEvaluator = $evaluation->evaluator_id === $user->id;
            $isTeacherOfBrief = $evaluation->submission->brief->teacher_id === $user->id;
            
            if (!$isEvaluator && !$isTeacherOfBrief) {
                return redirect()->back()->with('error', 'You do not have permission to view this evaluation.');
            }
        } elseif ($user->hasRole('student')) {
            // Students can only view their own evaluations
            if ($evaluation->submission->user_id !== $user->id) {
                return redirect()->back()->with('error', 'You do not have permission to view this evaluation.');
            }
        } else {
            return redirect()->back()->with('error', 'You do not have permission to view this evaluation.');
        }
        
        // Check if evaluation is overdue
        $isOverdue = $this->isOverdue($evaluation);
        
        // Calculate statistics if evaluation is completed
        $statistics = null;
        if ($evaluation->is_completed) {
            $statistics = [
                'totalCriteria' => $evaluation->criteria_count,
                'satisfiedCriteria' => $evaluation->satisfied_criteria_count,
                'percentage' => $evaluation->criteria_count > 0 
                    ? round(($evaluation->satisfied_criteria_count / $evaluation->criteria_count) * 100) 
                    : 0
            ];
        }
        
        return view('evaluations.show', [
            'evaluation' => $evaluation,
            'submission' => $evaluation->submission,
            'brief' => $evaluation->submission->brief,
            'student' => $evaluation->submission->user,
            'evaluator' => $evaluation->evaluator,
            'isOverdue' => $isOverdue,
            'statistics' => $statistics
        ]);
    }
    
    /**
     * Determine if an evaluation is overdue.
     *
     * @param  \App\Models\Evaluation  $evaluation
     * @return bool
     */
    protected function isOverdue(Evaluation $evaluation)
    {
        // If evaluation is already completed, it's not overdue
        if ($evaluation->is_completed) {
            return false;
        }
        
        // If there's no due date, it's not considered overdue
        if (!$evaluation->due_at) {
            return false;
        }
        
        // Check if the due date has passed
        return Carbon::parse($evaluation->due_at)->isPast();
    }
} 