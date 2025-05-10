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

class DashboardController extends Controller
{
    /**
     * Display the teacher dashboard with relevant statistics and data.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Check if user is a teacher
        if (Auth::user()->role !== 'teacher') {
            return redirect('/')->with('error', 'You must be a teacher to access this page.');
        }
        
        $teacher = Auth::user();
        $teacherId = $teacher->id;
        
        // Get statistics
        $totalBriefs = Brief::where('teacher_id', $teacherId)->count();
        
        $totalSubmissions = Submission::whereHas('brief', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->count();
        
        $pendingEvaluations = Evaluation::whereHas('submission.brief', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->where('status', '!=', 'completed')->count();
        
        // Count active students (students who have submitted to teacher's briefs)
        $activeStudents = Submission::whereHas('brief', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })
        ->distinct('student_id')
        ->count('student_id');
        
        // Get active briefs (published and not past deadline)
        $activeBriefs = Brief::where('teacher_id', $teacherId)
            ->where('status', 'published')
            ->where(function($query) {
                $query->where('deadline', '>=', Carbon::now())
                      ->orWhereNull('deadline');
            })
            ->withCount('submissions')
            ->orderBy('deadline')
            ->take(5)
            ->get();
        
        // Get recent submissions
        $recentSubmissions = Submission::whereHas('brief', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })
        ->with(['student:id,username,first_name,last_name', 'brief:id,title'])
        ->latest()
        ->take(5)
        ->get();
        
        // Get pending evaluations
        $evaluations = Evaluation::whereHas('submission.brief', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })
        ->where('status', '!=', 'completed')
        ->with([
            'evaluator:id,username,first_name,last_name', 
            'submission.student:id,username,first_name,last_name',
            'submission.brief:id,title'
        ])
        ->latest()
        ->take(5)
        ->get();
        
        // Calculate overdue status separately to avoid issues with date casting
        $now = Carbon::now();
        foreach ($evaluations as $evaluation) {
            $evaluation->is_overdue = false;
            if (!empty($evaluation->due_at)) {
                $evaluation->is_overdue = Carbon::parse($evaluation->due_at)->isPast();
            }
        }
        
        return view('teacher.dashboard', compact(
            'totalBriefs',
            'totalSubmissions',
            'pendingEvaluations',
            'activeStudents',
            'activeBriefs',
            'recentSubmissions',
            'evaluations'
        ));
    }
} 