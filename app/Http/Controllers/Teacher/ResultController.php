<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brief;
use App\Models\Evaluation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        
        // Total statistics
        $totalBriefs = $briefs->count();
        $totalSubmissions = $briefs->sum('submissions_count');
        $totalEvaluations = $completionRate->completed;
        
        return view('teacher.results.index', compact(
            'briefs', 
            'completionPercentage',
            'totalBriefs',
            'totalSubmissions',
            'totalEvaluations'
        ));
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
        
        // Calculate statistics
        $totalSubmissions = $brief->submissions->count();
        $evaluatedSubmissions = $brief->submissions->filter(function($submission) {
            return $submission->evaluations->where('status', 'completed')->count() > 0;
        })->count();
        
        $completionRate = $totalSubmissions > 0 
            ? round(($evaluatedSubmissions / $totalSubmissions) * 100) 
            : 0;
            
        // Calculate average scores per criterion
        $criteriaScores = [];
        $criteriaData = [];
        
        foreach ($brief->submissions as $submission) {
            foreach ($submission->evaluations as $evaluation) {
                if ($evaluation->status === 'completed') {
                    foreach ($evaluation->answers as $answer) {
                        $criterionTitle = $answer->criterion->title;
                        
                        if (!isset($criteriaScores[$criterionTitle])) {
                            $criteriaScores[$criterionTitle] = [];
                            $criteriaData[$criterionTitle] = [
                                'title' => $criterionTitle,
                                'scores' => [],
                                'average' => 0,
                                'min' => 10,
                                'max' => 0
                            ];
                        }
                        
                        $criteriaScores[$criterionTitle][] = $answer->score;
                        $criteriaData[$criterionTitle]['scores'][] = $answer->score;
                        
                        // Update min/max
                        $criteriaData[$criterionTitle]['min'] = min($criteriaData[$criterionTitle]['min'], $answer->score);
                        $criteriaData[$criterionTitle]['max'] = max($criteriaData[$criterionTitle]['max'], $answer->score);
                    }
                }
            }
        }
        
        // Calculate averages
        foreach ($criteriaData as $key => $data) {
            if (count($data['scores']) > 0) {
                $criteriaData[$key]['average'] = array_sum($data['scores']) / count($data['scores']);
            }
        }
        
        // Sort by average score (descending)
        usort($criteriaData, function($a, $b) {
            return $b['average'] <=> $a['average'];
        });
        
        // Get student performance data
        $studentPerformance = [];
        
        foreach ($brief->submissions as $submission) {
            $student = $submission->student;
            $evaluations = $submission->evaluations->where('status', 'completed');
            
            if (!isset($studentPerformance[$student->id])) {
                $studentPerformance[$student->id] = [
                    'student' => $student,
                    'submission_date' => $submission->created_at,
                    'evaluations' => $evaluations->count(),
                    'average_score' => 0
                ];
            }
            
            // Calculate average score across all completed evaluations
            $totalScore = 0;
            $answerCount = 0;
            
            foreach ($evaluations as $evaluation) {
                foreach ($evaluation->answers as $answer) {
                    $totalScore += $answer->score;
                    $answerCount++;
                }
            }
            
            if ($answerCount > 0) {
                $studentPerformance[$student->id]['average_score'] = $totalScore / $answerCount;
            }
        }
        
        // Convert to array and sort by average score (descending)
        $studentPerformance = array_values($studentPerformance);
        usort($studentPerformance, function($a, $b) {
            return $b['average_score'] <=> $a['average_score'];
        });
        
        return view('teacher.results.show', compact(
            'brief',
            'totalSubmissions',
            'evaluatedSubmissions',
            'completionRate',
            'criteriaData',
            'studentPerformance'
        ));
    }
    
    /**
     * Export the results for a specific brief.
     *
     * @param  \App\Models\Brief  $brief
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function export(Brief $brief, Request $request)
    {
        // Check if brief belongs to teacher
        $teacher = Auth::user();
        if ($brief->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get format or default to CSV
        $format = $request->input('format', 'csv');
        $includeComments = $request->boolean('include_comments', true);
        $anonymize = $request->boolean('anonymize', false);
        
        // Validate format
        if (!in_array($format, ['csv', 'excel', 'pdf'])) {
            return redirect()->back()->with('error', 'Invalid export format.');
        }
        
        // Load submissions with evaluations
        $brief->load([
            'submissions.student', 
            'submissions.evaluations.evaluator',
            'submissions.evaluations.answers.criterion'
        ]);
        
        // Prepare filename
        $filename = Str::slug($brief->title) . '-results-' . date('Y-m-d') . '.' . $format;
        
        // Build export data
        $exportData = [];
        
        // Header row
        $headers = ['Student', 'Submission Date', 'Evaluator', 'Overall Score', 'Status'];
        
        // Add criteria headers if there are evaluations
        $criteria = [];
        foreach ($brief->submissions as $submission) {
            foreach ($submission->evaluations as $evaluation) {
                foreach ($evaluation->answers as $answer) {
                    if (!in_array($answer->criterion->title, $criteria)) {
                        $criteria[] = $answer->criterion->title;
                    }
                }
            }
        }
        
        // Append criteria to headers
        foreach ($criteria as $criterion) {
            $headers[] = $criterion . ' Score';
        }
        
        // Add comments header if including comments
        if ($includeComments) {
            $headers[] = 'Overall Comments';
            foreach ($criteria as $criterion) {
                $headers[] = $criterion . ' Comments';
            }
        }
        
        // Add headers to export data
        $exportData[] = $headers;
        
        // Add rows for each submission and evaluation
        foreach ($brief->submissions as $submission) {
            // Get student name/identifier
            $studentName = $anonymize ? 'Student ' . $submission->id : $submission->student->username;
            
            foreach ($submission->evaluations as $evaluation) {
                $row = [];
                
                // Basic info
                $row[] = $studentName;
                $row[] = $submission->created_at->format('Y-m-d H:i:s');
                $row[] = $evaluation->evaluator->username;
                
                // Calculate overall score
                $overallScore = $evaluation->answers->avg('score');
                $row[] = number_format($overallScore, 1);
                $row[] = ucfirst($evaluation->status);
                
                // Add scores for each criterion
                $criteriaScores = [];
                $criteriaComments = [];
                
                foreach ($evaluation->answers as $answer) {
                    $criteriaScores[$answer->criterion->title] = $answer->score;
                    $criteriaComments[$answer->criterion->title] = $answer->comment;
                }
                
                // Add scores to row
                foreach ($criteria as $criterion) {
                    $row[] = isset($criteriaScores[$criterion]) ? $criteriaScores[$criterion] : 'N/A';
                }
                
                // Add comments if including them
                if ($includeComments) {
                    $row[] = $evaluation->overall_comment ?? 'No comments';
                    
                    // Add criterion comments
                    foreach ($criteria as $criterion) {
                        $row[] = isset($criteriaComments[$criterion]) ? $criteriaComments[$criterion] : '';
                    }
                }
                
                $exportData[] = $row;
            }
        }
        
        // Export based on format
        switch ($format) {
            case 'csv':
                return $this->exportCsv($exportData, $filename);
            case 'excel':
                return $this->exportExcel($exportData, $filename);
            case 'pdf':
                return $this->exportPdf($exportData, $filename, $brief);
        }
    }
    
    /**
     * Export data as CSV.
     */
    private function exportCsv($data, $filename)
    {
        $handle = fopen('php://temp', 'r+');
        
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    
    /**
     * Export data as Excel.
     */
    private function exportExcel($data, $filename)
    {
        // This would typically use a library like PhpSpreadsheet
        // For simplicity, we'll use CSV as a fallback
        return $this->exportCsv($data, str_replace('excel', 'csv', $filename));
    }
    
    /**
     * Export data as PDF.
     */
    private function exportPdf($data, $filename, $brief)
    {
        // This would typically use a library like DomPDF
        // For simplicity, we'll use CSV as a fallback
        return $this->exportCsv($data, str_replace('pdf', 'csv', $filename));
    }
} 