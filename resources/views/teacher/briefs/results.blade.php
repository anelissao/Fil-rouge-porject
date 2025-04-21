@extends('layouts.app')

@section('title', 'Results for ' . $brief->title)

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Results & Analysis</h1>
            <p class="page-subtitle">{{ $brief->title }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('teacher.briefs.show', $brief->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Brief
            </a>
            <a href="{{ route('teacher.briefs.submissions', $brief->id) }}" class="btn btn-outline">
                <i class="fas fa-upload"></i> View Submissions
            </a>
        </div>
    </div>

    <div class="results-container">
        <!-- Overview Cards -->
        <div class="overview-grid">
            <div class="overview-card">
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-content">
                    <h3 class="card-value">{{ $brief->assigned_students_count ?? 0 }}</h3>
                    <p class="card-label">Total Students</p>
                </div>
            </div>
            <div class="overview-card">
                <div class="card-icon">
                    <i class="fas fa-upload"></i>
                </div>
                <div class="card-content">
                    <h3 class="card-value">{{ $totalSubmissions ?? 0 }}</h3>
                    <p class="card-label">Submissions</p>
                </div>
            </div>
            <div class="overview-card">
                <div class="card-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="card-content">
                    <h3 class="card-value">{{ $completedEvaluations ?? 0 }}</h3>
                    <p class="card-label">Evaluations</p>
                </div>
            </div>
            <div class="overview-card">
                <div class="card-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="card-content">
                    <h3 class="card-value">
                        @if(isset($brief->assigned_students_count) && $brief->assigned_students_count > 0)
                            {{ round(($totalSubmissions / $brief->assigned_students_count) * 100) }}%
                        @else
                            0%
                        @endif
                    </h3>
                    <p class="card-label">Completion Rate</p>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-grid">
            <div class="chart-card">
                <h3 class="chart-title">Submission Timeline</h3>
                <div class="chart-subtitle">When students submitted their work</div>
                <div class="chart-container" style="height: 300px;">
                    <div class="chart-placeholder">
                        <i class="fas fa-chart-line"></i>
                        <p>Submission data will be visualized here</p>
                    </div>
                </div>
            </div>
            <div class="chart-card">
                <h3 class="chart-title">Evaluation Results</h3>
                <div class="chart-subtitle">Distribution of evaluation scores</div>
                <div class="chart-container" style="height: 300px;">
                    <div class="chart-placeholder">
                        <i class="fas fa-chart-bar"></i>
                        <p>Evaluation data will be visualized here</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Skills Performance -->
        <div class="card">
            <h3 class="card-title">Skills Performance</h3>
            <div class="card-subtitle">How students performed across different skills</div>
            
            @if(isset($skillsPerformance) && count($skillsPerformance) > 0)
                <div class="skills-list">
                    @foreach($skillsPerformance as $skill)
                        <div class="skill-item">
                            <div class="skill-info">
                                <div class="skill-name">{{ $skill['name'] }}</div>
                                <div class="skill-score">{{ $skill['score'] }}%</div>
                            </div>
                            <div class="skill-bar">
                                <div class="skill-progress" style="width: {{ $skill['score'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-data">
                    <i class="fas fa-chart-pie"></i>
                    <p>No skills data available yet. Skills will appear here once evaluations are completed.</p>
                </div>
            @endif
        </div>

        <!-- Criteria Performance -->
        <div class="card">
            <h3 class="card-title">Criteria Performance</h3>
            <div class="card-subtitle">How students performed on each evaluation criterion</div>
            
            @if(isset($criteriaPerformance) && count($criteriaPerformance) > 0)
                <div class="criteria-list">
                    @foreach($criteriaPerformance as $criterion)
                        <div class="criterion-item">
                            <div class="criterion-header">
                                <div class="criterion-title">{{ $criterion['title'] }}</div>
                                <div class="criterion-score">{{ $criterion['pass_rate'] }}% success</div>
                            </div>
                            <div class="criterion-bar">
                                <div class="criterion-progress" style="width: {{ $criterion['pass_rate'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-data">
                    <i class="fas fa-clipboard-list"></i>
                    <p>No criteria performance data available yet. Data will appear here once evaluations are completed.</p>
                </div>
            @endif
        </div>

        <!-- Student Performance Table -->
        <div class="card">
            <h3 class="card-title">Student Performance</h3>
            <div class="card-subtitle">Individual student results</div>
            
            @if(isset($studentPerformance) && count($studentPerformance) > 0)
                <div class="table-container">
                    <table class="performance-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Submission Status</th>
                                <th>Submission Date</th>
                                <th>Evaluations</th>
                                <th>Overall Score</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentPerformance as $student)
                                <tr>
                                    <td>
                                        <div class="student-cell">
                                            <div class="student-avatar">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div class="student-info">
                                                <div class="student-name">{{ $student['name'] }}</div>
                                                <div class="student-email">{{ $student['email'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($student['has_submission'])
                                            <span class="badge success">Submitted</span>
                                        @else
                                            <span class="badge danger">Missing</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($student['submission_date'])
                                            {{ $student['submission_date'] }}
                                            @if($student['is_late'])
                                                <span class="badge warning">Late</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $student['evaluations_count'] ?? 0 }}</td>
                                    <td>
                                        @if(isset($student['score']))
                                            <div class="score-pill" style="background-color: {{ $student['score'] >= 70 ? '#10B981' : ($student['score'] >= 50 ? '#F59E0B' : '#EF4444') }}">
                                                {{ $student['score'] }}%
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            @if($student['has_submission'])
                                                <a href="#" class="btn-action view">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                            @if($student['evaluations_count'] > 0)
                                                <a href="#" class="btn-action report">
                                                    <i class="fas fa-file-alt"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-data">
                    <i class="fas fa-user-graduate"></i>
                    <p>No student performance data available yet. Data will appear once students submit their work and evaluations are completed.</p>
                </div>
            @endif
        </div>

        <!-- Export Options -->
        <div class="export-container">
            <h3 class="export-title">Export Data</h3>
            <div class="export-buttons">
                <a href="#" class="btn btn-outline">
                    <i class="fas fa-file-csv"></i> Export as CSV
                </a>
                <a href="#" class="btn btn-outline">
                    <i class="fas fa-file-pdf"></i> Export as PDF
                </a>
                <a href="#" class="btn btn-outline">
                    <i class="fas fa-print"></i> Print Report
                </a>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
        color: var(--secondary-color);
    }

    .page-subtitle {
        color: var(--accent-color);
        margin-bottom: 0;
    }

    .page-actions {
        display: flex;
        gap: 0.75rem;
    }

    /* Overview Cards */
    .overview-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .overview-card {
        background-color: var(--highlight-color);
        border-radius: 0.5rem;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-icon {
        width: 3rem;
        height: 3rem;
        background-color: rgba(37, 99, 235, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
    }

    .card-icon i {
        font-size: 1.25rem;
        color: var(--primary-color);
    }

    .card-content {
        flex: 1;
    }

    .card-value {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
        color: var(--secondary-color);
    }

    .card-label {
        color: var(--accent-color);
        font-size: 0.875rem;
        margin: 0;
    }

    /* Charts Grid */
    .charts-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .chart-card {
        background-color: var(--highlight-color);
        border-radius: 0.5rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .chart-title {
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
        color: var(--secondary-color);
    }

    .chart-subtitle {
        color: var(--accent-color);
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
    }

    .chart-container {
        position: relative;
    }

    .chart-placeholder {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: var(--accent-color);
        text-align: center;
    }

    .chart-placeholder i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: rgba(37, 99, 235, 0.2);
    }

    /* General Card Style */
    .card {
        background-color: var(--highlight-color);
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-title {
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
        color: var(--secondary-color);
    }

    .card-subtitle {
        color: var(--accent-color);
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
    }

    /* Skills Performance */
    .skills-list, .criteria-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .skill-item, .criterion-item {
        padding-bottom: 1rem;
    }

    .skill-info, .criterion-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .skill-name, .criterion-title {
        color: var(--secondary-color);
        font-size: 0.95rem;
    }

    .skill-score, .criterion-score {
        color: var(--accent-color);
        font-size: 0.875rem;
    }

    .skill-bar, .criterion-bar {
        height: 0.5rem;
        background-color: rgba(255, 255, 255, 0.05);
        border-radius: 0.25rem;
        overflow: hidden;
    }

    .skill-progress, .criterion-progress {
        height: 100%;
        background-color: var(--primary-color);
        border-radius: 0.25rem;
    }

    /* Empty State */
    .empty-data {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem 0;
        color: var(--accent-color);
        text-align: center;
    }

    .empty-data i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: rgba(37, 99, 235, 0.2);
    }

    /* Student Performance Table */
    .table-container {
        overflow-x: auto;
    }

    .performance-table {
        width: 100%;
        border-collapse: collapse;
    }

    .performance-table th,
    .performance-table td {
        padding: 1rem;
        text-align: left;
    }

    .performance-table th {
        color: var(--secondary-color);
        font-weight: 600;
        font-size: 0.95rem;
        white-space: nowrap;
        border-bottom: 1px solid rgba(229, 231, 235, 0.1);
    }

    .performance-table tr {
        border-bottom: 1px solid rgba(229, 231, 235, 0.1);
    }

    .performance-table tr:last-child {
        border-bottom: none;
    }

    .performance-table tr:hover {
        background-color: rgba(255, 255, 255, 0.03);
    }

    /* Student Cell */
    .student-cell {
        display: flex;
        align-items: center;
    }

    .student-avatar {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        background-color: rgba(37, 99, 235, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        color: var(--primary-color);
    }

    .student-info {
        display: flex;
        flex-direction: column;
    }

    .student-name {
        color: var(--secondary-color);
        font-size: 0.95rem;
        margin-bottom: 0.25rem;
    }

    .student-email {
        color: var(--accent-color);
        font-size: 0.75rem;
    }

    /* Badges */
    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .badge.success {
        background-color: rgba(16, 185, 129, 0.1);
        color: #10B981;
    }

    .badge.warning {
        background-color: rgba(245, 158, 11, 0.1);
        color: #F59E0B;
    }

    .badge.danger {
        background-color: rgba(239, 68, 68, 0.1);
        color: #EF4444;
    }

    /* Score Pill */
    .score-pill {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: white;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-action {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--secondary-color);
        transition: background-color 0.3s, color 0.3s;
    }

    .btn-action:hover {
        color: white;
    }

    .btn-action.view {
        background-color: rgba(37, 99, 235, 0.1);
    }

    .btn-action.view:hover {
        background-color: #2563EB;
    }

    .btn-action.report {
        background-color: rgba(16, 185, 129, 0.1);
    }

    .btn-action.report:hover {
        background-color: #10B981;
    }

    /* Export Section */
    .export-container {
        margin-top: 2rem;
    }

    .export-title {
        font-size: 1rem;
        color: var(--secondary-color);
        margin-bottom: 1rem;
    }

    .export-buttons {
        display: flex;
        gap: 1rem;
    }

    /* Responsive Styles */
    @media (max-width: 1024px) {
        .overview-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .charts-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .page-actions {
            margin-top: 1rem;
            width: 100%;
        }

        .overview-grid {
            grid-template-columns: 1fr;
        }

        .export-buttons {
            flex-direction: column;
        }
    }

    @media (max-width: 576px) {
        .performance-table th:nth-child(3),
        .performance-table td:nth-child(3),
        .performance-table th:nth-child(4),
        .performance-table td:nth-child(4) {
            display: none;
        }
    }
</style>
@endsection 