<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brief;
use App\Models\Submission;
use App\Models\Evaluation;
use App\Models\User;
use App\Models\Criterion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResultsController extends Controller
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
     * Display the teacher's results overview
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $teacher = Auth::user();
        
        // Get all briefs created by the teacher
        $briefs = Brief::where('teacher_id', $teacher->id)
            ->withCount(['submissions', 'submissions as evaluated_count' => function ($query) {
                $query->whereHas('evaluations');
            }])
            ->latest()
            ->get();
            
        // Calculate completion percentage for each brief
        foreach ($briefs as $brief) {
            $brief->completion_percentage = $brief->submissions_count > 0 
                ? round(($brief->evaluated_count / $brief->submissions_count) * 100) 
                : 0;
        }
        
        // Summary cards data
        $totalBriefs = $briefs->count();
        $totalSubmissions = Submission::whereHas('brief', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->count();
        $totalEvaluations = Evaluation::whereHas('submission.brief', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->count();
        $completionRate = $totalSubmissions > 0 
            ? round(($totalEvaluations / $totalSubmissions) * 100) 
            : 0;
            
        return view('teacher.results.index', compact(
            'briefs', 
            'totalBriefs', 
            'totalSubmissions', 
            'totalEvaluations', 
            'completionRate'
        ));
    }
    
    /**
     * Show detailed results for a specific brief
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $teacher = Auth::user();
        $brief = Brief::where('teacher_id', $teacher->id)->findOrFail($id);
        
        // Get all submissions for this brief with their evaluations
        $submissions = Submission::with(['user', 'evaluations.evaluator', 'evaluations.criteriaScores.criterion'])
            ->where('brief_id', $brief->id)
            ->get();
            
        // Overall statistics
        $totalSubmissions = $submissions->count();
        $evaluatedSubmissions = $submissions->filter(function($submission) {
            return $submission->evaluations->isNotEmpty();
        })->count();
        
        $averageScore = 0;
        $scoreDistribution = [
            'excellent' => 0,  // 90-100%
            'good' => 0,       // 75-89%
            'average' => 0,    // 60-74%
            'belowAverage' => 0, // 50-59%
            'failing' => 0     // 0-49%
        ];
        
        // Criteria performance
        $criteria = Criterion::whereHas('brief', function($query) use ($brief) {
            $query->where('id', $brief->id);
        })->get();
        
        $criteriaPerformance = [];
        foreach ($criteria as $criterion) {
            $criteriaPerformance[$criterion->id] = [
                'name' => $criterion->name,
                'description' => $criterion->description,
                'weight' => $criterion->weight,
                'averageScore' => 0,
                'totalScores' => 0,
                'count' => 0
            ];
        }
        
        // Student performance
        $studentPerformance = [];
        
        // Calculate statistics
        foreach ($submissions as $submission) {
            $submissionEvaluations = $submission->evaluations;
            
            if ($submissionEvaluations->isEmpty()) {
                continue;
            }
            
            // Calculate average score for this submission
            $totalSubmissionScore = 0;
            $totalWeight = 0;
            
            foreach ($submissionEvaluations as $evaluation) {
                foreach ($evaluation->criteriaScores as $criteriaScore) {
                    $criterionId = $criteriaScore->criterion_id;
                    $weight = $criteriaScore->criterion->weight;
                    $score = $criteriaScore->score;
                    
                    // Update criteria performance
                    if (isset($criteriaPerformance[$criterionId])) {
                        $criteriaPerformance[$criterionId]['totalScores'] += $score;
                        $criteriaPerformance[$criterionId]['count']++;
                    }
                    
                    // Add to submission score
                    $totalSubmissionScore += $score * $weight;
                    $totalWeight += $weight;
                }
            }
            
            $submissionAverageScore = $totalWeight > 0 ? ($totalSubmissionScore / $totalWeight) : 0;
            $percentageScore = $submissionAverageScore * 100;
            
            // Add to average score
            $averageScore += $submissionAverageScore;
            
            // Update score distribution
            if ($percentageScore >= 90) {
                $scoreDistribution['excellent']++;
            } elseif ($percentageScore >= 75) {
                $scoreDistribution['good']++;
            } elseif ($percentageScore >= 60) {
                $scoreDistribution['average']++;
            } elseif ($percentageScore >= 50) {
                $scoreDistribution['belowAverage']++;
            } else {
                $scoreDistribution['failing']++;
            }
            
            // Update student performance
            $studentPerformance[$submission->user->id] = [
                'name' => $submission->user->name,
                'score' => round($percentageScore, 1),
                'submission_id' => $submission->id
            ];
        }
        
        // Calculate final averages
        if ($evaluatedSubmissions > 0) {
            $averageScore = round(($averageScore / $evaluatedSubmissions) * 100, 1);
            
            foreach ($criteriaPerformance as $criterionId => $data) {
                if ($data['count'] > 0) {
                    $criteriaPerformance[$criterionId]['averageScore'] = 
                        round(($data['totalScores'] / $data['count']) * 100, 1);
                }
            }
        }
        
        return view('teacher.results.show', compact(
            'brief',
            'submissions',
            'totalSubmissions',
            'evaluatedSubmissions',
            'averageScore',
            'scoreDistribution',
            'criteriaPerformance',
            'studentPerformance'
        ));
    }
    
    /**
     * Export results for a specific brief
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function export(Request $request, $id)
    {
        $teacher = Auth::user();
        $brief = Brief::where('teacher_id', $teacher->id)->findOrFail($id);
        
        // Validate export options
        $validated = $request->validate([
            'format' => 'required|in:csv,pdf,excel',
            'include_comments' => 'boolean',
            'anonymize' => 'boolean',
        ]);
        
        // TODO: Implement export logic based on the validated options
        // This would generate and download the appropriate file format
        
        return redirect()->route('teacher.results.show', $brief->id)
            ->with('success', 'Results exported successfully');
    }
} 