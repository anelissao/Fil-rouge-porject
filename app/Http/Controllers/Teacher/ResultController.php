<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brief;
use App\Models\Evaluation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    /**
     * Display a listing of results and statistics.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $teacher = Auth::user();
        
        // Get briefs with submission and evaluation counts
        $briefs = Brief::where('teacher_id', $teacher->id)
            ->withCount(['submissions', 'submissions as evaluated_count' => function($query) {
                $query->whereHas('evaluations', function($q) {
                    $q->where('status', 'completed');
                });
            }])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get evaluation completion rate
        $completionRate = Evaluation::whereHas('submission.brief', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })
        ->select(
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed')
        )
        ->first();
        
        if ($completionRate->total > 0) {
            $completionPercentage = round(($completionRate->completed / $completionRate->total) * 100);
        } else {
            $completionPercentage = 0;
        }
        
        return view('teacher.results.index', compact('briefs', 'completionPercentage'));
    }
    
    /**
     * Display the specified brief's results.
     *
     * @param  \App\Models\Brief  $brief
     * @return \Illuminate\View\View
     */
    public function show(Brief $brief)
    {
        // Check if brief belongs to teacher
        $teacher = Auth::user();
        if ($brief->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Load submissions with their evaluations
        $brief->load(['submissions.student', 'submissions.evaluations.evaluator']);
        
        return view('teacher.results.show', compact('brief'));
    }
} 