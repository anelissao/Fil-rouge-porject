@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
    <div class="dashboard-header">
        <h1 class="dashboard-title">Teacher Dashboard</h1>
        <p class="dashboard-welcome">Welcome back, {{ auth()->user()->first_name }}!</p>
    </div>

    <!-- Summary Statistics -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value">{{ $totalBriefs ?? 0 }}</h3>
                <p class="stat-label">Total Briefs</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-upload"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value">{{ $totalSubmissions ?? 0 }}</h3>
                <p class="stat-label">Total Submissions</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value">{{ $pendingEvaluations ?? 0 }}</h3>
                <p class="stat-label">Pending Evaluations</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value">{{ $activeStudents ?? 0 }}</h3>
                <p class="stat-label">Active Students</p>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Active Briefs Section -->
        <div class="dashboard-card active-briefs">
            <div class="card-header">
                <h2 class="section-title">Active Briefs</h2>
                <a href="{{ route('teacher.briefs.index') }}" class="btn btn-sm btn-outline">View All</a>
            </div>
            
            <div class="briefs-list">
                @if(isset($activeBriefs) && count($activeBriefs) > 0)
                    @foreach($activeBriefs as $brief)
                        <div class="brief-item">
                            <div class="brief-content">
                                <h3 class="brief-title">{{ $brief->title }}</h3>
                                <div class="brief-meta">
                                    <span class="brief-date">
                                        <i class="fas fa-calendar-alt"></i> Deadline: {{ $brief->deadline->format('M d, Y') }}
                                    </span>
                                    <span class="brief-submissions">
                                        <i class="fas fa-upload"></i> {{ $brief->submissions_count ?? 0 }} Submissions
                                    </span>
                                </div>
                            </div>
                            <div class="brief-status">
                                <span class="status-indicator {{ $brief->isExpired() ? 'expired' : 'active' }}">
                                    {{ $brief->isExpired() ? 'Expired' : 'Active' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="fas fa-file-alt"></i>
                        <p>No active briefs found.</p>
                        <a href="{{ route('teacher.briefs.create') }}" class="btn btn-primary">Create Brief</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Submissions Section -->
        <div class="dashboard-card recent-submissions">
            <div class="card-header">
                <h2 class="section-title">Recent Submissions</h2>
                <a href="{{ route('teacher.submissions.index') }}" class="btn btn-sm btn-outline">View All</a>
            </div>
            
            <div class="submissions-list">
                @if(isset($recentSubmissions) && count($recentSubmissions) > 0)
                    @foreach($recentSubmissions as $submission)
                        <div class="submission-item">
                            <div class="submission-icon">
                                <i class="fas fa-file-upload"></i>
                            </div>
                            <div class="submission-content">
                                <h3 class="submission-title">{{ $submission->student->username }} submitted for {{ $submission->brief->title }}</h3>
                                <div class="submission-meta">
                                    <span class="submission-date">
                                        <i class="fas fa-clock"></i> {{ $submission->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                            <div class="submission-action">
                                <a href="{{ route('teacher.submissions.show', $submission->id) }}" class="btn btn-sm btn-outline">View</a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="fas fa-upload"></i>
                        <p>No recent submissions found.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pending Evaluations Section -->
        <div class="dashboard-card pending-evaluations">
            <div class="card-header">
                <h2 class="section-title">Pending Evaluations</h2>
                <a href="{{ route('teacher.evaluations.index') }}" class="btn btn-sm btn-outline">Manage</a>
            </div>
            
            <div class="evaluations-list">
                @if(isset($evaluations) && count($evaluations) > 0)
                    @foreach($evaluations as $evaluation)
                        <div class="evaluation-item">
                            <div class="evaluation-content">
                                <h3 class="evaluation-title">{{ $evaluation->evaluator->username }} → {{ $evaluation->submission->student->username }}</h3>
                                <div class="evaluation-meta">
                                    <span class="evaluation-brief">{{ $evaluation->submission->brief->title ?? 'Brief' }}</span>
                                    <span class="evaluation-due">{{ $evaluation->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="evaluation-status">
                                Pending
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="fas fa-clipboard-check"></i>
                        <p>No pending evaluations found.</p>
                        <a href="{{ route('teacher.evaluations.assign') }}" class="btn btn-primary">Assign Evaluations</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="dashboard-card quick-actions">
            <div class="card-header">
                <h2 class="section-title">Quick Actions</h2>
            </div>
            
            <div class="actions-grid">
                <a href="{{ route('teacher.briefs.create') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <h3 class="action-title">Create Brief</h3>
                </a>
                <a href="{{ route('teacher.evaluations.assign') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <h3 class="action-title">Assign Evaluations</h3>
                </a>
                <a href="{{ route('teacher.results.index') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3 class="action-title">View Results</h3>
                </a>
                <a href="{{ route('teacher.submissions.index') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-list-alt"></i>
                    </div>
                    <h3 class="action-title">All Submissions</h3>
                </a>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    /* Dashboard Grid Layout */
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .dashboard-card {
        background-color: var(--highlight-color);
        border-radius: 0.5rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .btn-sm {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
    }

    /* Active Briefs Styles */
    .brief-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid rgba(229, 231, 235, 0.1);
    }

    .brief-item:last-child {
        border-bottom: none;
    }

    .brief-title {
        font-size: 1rem;
        margin-bottom: 0.5rem;
        color: var(--secondary-color);
    }

    .brief-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.875rem;
        color: var(--accent-color);
    }

    .status-indicator {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .status-indicator.active {
        background-color: rgba(16, 185, 129, 0.1);
        color: #10B981;
    }

    .status-indicator.expired {
        background-color: rgba(239, 68, 68, 0.1);
        color: #EF4444;
    }

    /* Recent Submissions Styles */
    .submission-item {
        display: flex;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid rgba(229, 231, 235, 0.1);
    }

    .submission-item:last-child {
        border-bottom: none;
    }

    .submission-icon {
        width: 2.5rem;
        height: 2.5rem;
        background-color: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1rem;
        color: var(--secondary-color);
    }

    .submission-content {
        flex: 1;
    }

    .submission-title {
        font-size: 0.95rem;
        margin-bottom: 0.25rem;
        color: var(--secondary-color);
    }

    .submission-meta {
        font-size: 0.875rem;
        color: var(--accent-color);
    }

    /* Pending Evaluations Styles */
    .evaluation-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid rgba(229, 231, 235, 0.1);
    }

    .evaluation-item:last-child {
        border-bottom: none;
    }

    .evaluation-title {
        font-size: 0.95rem;
        margin-bottom: 0.25rem;
        color: var(--secondary-color);
    }

    .evaluation-meta {
        display: flex;
        flex-direction: column;
        font-size: 0.875rem;
        color: var(--accent-color);
    }

    .evaluation-status {
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 500;
        background-color: rgba(16, 185, 129, 0.1);
        color: #10B981;
    }

    .evaluation-status.overdue {
        background-color: rgba(239, 68, 68, 0.1);
        color: #EF4444;
    }

    /* Empty State Styles */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem 0;
        color: var(--accent-color);
        text-align: center;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .empty-state p {
        margin-bottom: 1rem;
    }

    /* Quick Actions Styles */
    .actions-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .action-card {
        background-color: rgba(30, 144, 255, 0.1);
        border-radius: 0.5rem;
        padding: 1.25rem 1rem;
        text-align: center;
        text-decoration: none;
        transition: transform 0.3s, background-color 0.3s;
    }

    .action-card:hover {
        transform: translateY(-5px);
        background-color: var(--primary-color);
    }

    .action-icon {
        font-size: 1.75rem;
        color: var(--primary-color);
        margin-bottom: 0.75rem;
    }

    .action-card:hover .action-icon {
        color: var(--secondary-color);
    }

    .action-title {
        font-size: 0.95rem;
        color: var(--secondary-color);
    }

    /* Responsive Styles */
    @media (max-width: 992px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .brief-meta, .submission-meta {
            flex-direction: column;
        }
        
        .brief-meta span, .submission-meta span {
            margin-bottom: 0.25rem;
        }
        
        .actions-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection 