<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Brief;
use App\Models\Submission;
use App\Models\Evaluation;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the student dashboard with all relevant statistics.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get total number of submissions made by the student
        $totalSubmissions = Submission::where('student_id', $user->id)->count();
        
        // Get pending submissions (briefs where the student hasn't submitted yet)
        $pendingSubmissions = Brief::where('status', 'published')
            ->where('deadline', '>=', now())
            ->whereDoesntHave('submissions', function($query) use ($user) {
                $query->where('student_id', $user->id);
            })
            ->count();
        
        // Get evaluations that the student needs to complete for other students
        $pendingEvaluations = Evaluation::where('evaluator_id', $user->id)
            ->where('status', '!=', 'completed')
            ->count();
        
        // Get evaluations received for the student's submissions
        $receivedEvaluations = Evaluation::whereHas('submission', function($query) use ($user) {
                $query->where('student_id', $user->id);
            })
            ->where('status', 'completed')
            ->count();
        
        // Get active briefs for the student
        $activeBriefs = Brief::where('status', 'published')
            ->where('deadline', '>=', now())
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function($brief) use ($user) {
                $brief->hasSubmitted = $brief->submissions()
                    ->where('student_id', $user->id)
                    ->exists();
                return $brief;
            });
        
        // Get evaluations that the student needs to complete
        $evaluations = Evaluation::where('evaluator_id', $user->id)
            ->where('status', '!=', 'completed')
            ->with(['submission.student', 'submission.brief'])
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function($evaluation) {
                $evaluation->is_overdue = $evaluation->due_at && $evaluation->due_at->isPast();
                return $evaluation;
            });
        
        // Get evaluations that the student has received
        $receivedEvaluationsList = Evaluation::whereHas('submission', function($query) use ($user) {
                $query->where('student_id', $user->id);
            })
            ->where('status', 'completed')
            ->with(['evaluator', 'submission.brief'])
            ->latest('completed_at')
            ->limit(5)
            ->get();
        
        return view('student.dashboard', compact(
            'totalSubmissions',
            'pendingSubmissions',
            'pendingEvaluations',
            'receivedEvaluations',
            'activeBriefs',
            'evaluations',
            'receivedEvaluationsList'
        ));
    }
} 