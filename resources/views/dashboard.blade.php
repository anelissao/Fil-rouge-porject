@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="dashboard-header">
        <h1 class="dashboard-title">Dashboard</h1>
        <p class="dashboard-welcome">Welcome back, {{ auth()->user()->first_name }}!</p>
    </div>

    <div class="dashboard-stats">
        @if(auth()->user()->isTeacher())
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
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $totalStudents ?? 0 }}</h3>
                    <p class="stat-label">Students</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-upload"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $totalSubmissions ?? 0 }}</h3>
                    <p class="stat-label">Submissions</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $completedEvaluations ?? 0 }}</h3>
                    <p class="stat-label">Evaluations</p>
                </div>
            </div>
        @elseif(auth()->user()->isStudent())
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $availableBriefs ?? 0 }}</h3>
                    <p class="stat-label">Available Briefs</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-upload"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $mySubmissions ?? 0 }}</h3>
                    <p class="stat-label">My Submissions</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $pendingEvaluations ?? 0 }}</h3>
                    <p class="stat-label">Pending Evaluations</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $avgRating ?? 'N/A' }}</h3>
                    <p class="stat-label">Avg. Rating</p>
                </div>
            </div>
        @elseif(auth()->user()->isAdmin())
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $totalUsers ?? 0 }}</h3>
                    <p class="stat-label">Total Users</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $totalTeachers ?? 0 }}</h3>
                    <p class="stat-label">Teachers</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $totalStudents ?? 0 }}</h3>
                    <p class="stat-label">Students</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $totalBriefs ?? 0 }}</h3>
                    <p class="stat-label">Total Briefs</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Recent Activity Section -->
    <div class="recent-activity">
        <h2 class="section-title">Recent Activity</h2>
        
        <div class="activity-list">
            @if(isset($activities) && count($activities) > 0)
                @foreach($activities as $activity)
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas {{ $activity->icon ?? 'fa-bell' }}"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-detail">
                                {{ $activity->description ?? 'Activity description' }}
                            </div>
                            <div class="activity-time">
                                {{ $activity->created_at ?? now()->format('M d, Y') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <p>No recent activity to display.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h2 class="section-title">Quick Actions</h2>
        
        <div class="actions-grid">
            @if(auth()->user()->isTeacher())
                <a href="{{ route('teacher.briefs.create') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <h3 class="action-title">Create Brief</h3>
                </a>
                <a href="{{ route('teacher.submissions.index') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-list-alt"></i>
                    </div>
                    <h3 class="action-title">View Submissions</h3>
                </a>
                <a href="{{ route('teacher.results.index') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h3 class="action-title">View Results</h3>
                </a>
            @elseif(auth()->user()->isStudent())
                <a href="{{ route('briefs.index') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3 class="action-title">View Briefs</h3>
                </a>
                <a href="{{ route('student.submissions.create') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-upload"></i>
                    </div>
                    <h3 class="action-title">New Submission</h3>
                </a>
                <a href="{{ route('student.evaluations.index') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h3 class="action-title">Do Evaluations</h3>
                </a>
            @elseif(auth()->user()->isAdmin())
                <a href="{{ route('admin.users.index') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <h3 class="action-title">Manage Users</h3>
                </a>
                <a href="{{ route('admin.briefs.index') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <h3 class="action-title">Manage Briefs</h3>
                </a>
                <a href="{{ route('admin.settings') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-cog"></i>
                    </div>
                    <h3 class="action-title">Site Settings</h3>
                </a>
            @endif
        </div>
    </div>
@endsection

@section('styles')
<style>
    /* Dashboard Header Styles */
    .dashboard-header {
        margin-bottom: 2rem;
    }

    .dashboard-title {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        color: var(--secondary-color);
    }

    .dashboard-welcome {
        color: var(--accent-color);
        font-size: 1.1rem;
    }

    /* Dashboard Stats Styles */
    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .stat-card {
        background-color: var(--highlight-color);
        border-radius: 0.5rem;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        width: 3rem;
        height: 3rem;
        background-color: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.2rem;
        color: var(--secondary-color);
    }

    .stat-content {
        flex: 1;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        color: var(--secondary-color);
    }

    .stat-label {
        color: var(--accent-color);
        font-size: 0.9rem;
    }

    /* Recent Activity Styles */
    .recent-activity {
        margin-bottom: 3rem;
    }

    .activity-list {
        background-color: var(--highlight-color);
        border-radius: 0.5rem;
        padding: 1.5rem;
    }

    .activity-item {
        display: flex;
        align-items: flex-start;
        padding: 1rem 0;
        border-bottom: 1px solid rgba(229, 231, 235, 0.1);
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
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

    .activity-content {
        flex: 1;
    }

    .activity-detail {
        margin-bottom: 0.25rem;
        color: var(--secondary-color);
    }

    .activity-time {
        font-size: 0.85rem;
        color: var(--accent-color);
    }

    .empty-state {
        text-align: center;
        padding: 2rem 0;
    }

    .empty-icon {
        font-size: 3rem;
        color: var(--accent-color);
        margin-bottom: 1rem;
    }

    /* Quick Actions Styles */
    .quick-actions {
        margin-bottom: 3rem;
    }

    .actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .action-card {
        background-color: var(--highlight-color);
        border-radius: 0.5rem;
        padding: 2rem 1.5rem;
        text-align: center;
        text-decoration: none;
        transition: transform 0.3s, background-color 0.3s;
    }

    .action-card:hover {
        transform: translateY(-5px);
        background-color: var(--primary-color);
    }

    .action-icon {
        font-size: 2rem;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }

    .action-card:hover .action-icon {
        color: var(--secondary-color);
    }

    .action-title {
        font-size: 1.1rem;
        color: var(--secondary-color);
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .dashboard-stats {
            grid-template-columns: 1fr;
        }
        
        .actions-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection 