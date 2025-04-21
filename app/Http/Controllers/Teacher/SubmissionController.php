<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            // Check if the user is a teacher
            if (Auth::user()->role !== 'teacher') {
                return redirect('/')->with('error', 'You must be a teacher to access this page.');
            }
            
            return $next($request);
        });
    }
    
    /**
     * Display a listing of the submissions.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $teacher = Auth::user();
        $submissions = Submission::whereHas('brief', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })
        ->with(['student:id,username,first_name,last_name', 'brief:id,title'])
        ->latest()
        ->paginate(15);
        
        return view('teacher.submissions.index', compact('submissions'));
    }
    
    /**
     * Display the specified submission.
     *
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\View\View
     */
    public function show(Submission $submission)
    {
        // Check if submission belongs to teacher's brief
        $teacher = Auth::user();
        if ($submission->brief->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Load relationships
        $submission->load(['student', 'brief', 'evaluations.evaluator']);
        
        return view('teacher.submissions.show', compact('submission'));
    }
} 