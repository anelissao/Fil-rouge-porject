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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // No middleware here
    }
    
    /**
     * Display the teacher dashboard with relevant statistics and data.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Check if user is logged in and is a teacher
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        if ($user->role !== 'teacher') {
            return redirect('/')->with('error', 'You must be a teacher to access this page.');
        }
        
        $teacher = $user;
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
        ->distinct('user_id')
        ->count('user_id');
        
        // Get active briefs (published and not past end_date)
        $activeBriefs = Brief::where('teacher_id', $teacherId)
            ->wherePublished(true)
            ->where('end_date', '>=', Carbon::now())
            ->withCount('submissions')
            ->orderBy('end_date')
            ->take(5)
            ->get();
        
        // Get recent submissions
        $recentSubmissions = Submission::whereHas('brief', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })
        ->with(['user:id,username,first_name,last_name', 'brief:id,title'])
        ->latest()
        ->take(5)
        ->get();
        
        // Get pending evaluations
        $evaluations = Evaluation::whereHas('submission.brief', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })
        ->where('status', '!=', 'completed')
        ->with(['evaluator:id,username,first_name,last_name', 'submission.user:id,username,first_name,last_name'])
        ->latest()
        ->take(5)
        ->get()
        ->map(function($evaluation) {
            $evaluation->is_overdue = $evaluation->due_date && $evaluation->due_date->isPast();
            return $evaluation;
        });
        
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