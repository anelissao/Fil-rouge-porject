@extends('layouts.app')

@section('title', $brief->title)

@section('content')
    <div class="page-header">
        <div>
            <div class="title-container">
                <h1 class="page-title">{{ $brief->title }}</h1>
                <span class="status-badge {{ $brief->status }}">{{ ucfirst($brief->status) }}</span>
            </div>
            <p class="page-subtitle">Created {{ $brief->created_at->format('M d, Y') }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('teacher.briefs.edit', $brief->id) }}" class="btn btn-outline">
                <i class="fas fa-edit"></i> Edit Brief
            </a>
            <a href="{{ route('teacher.briefs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Briefs
            </a>
        </div>
    </div>

    <div class="brief-container">
        <div class="brief-sidebar">
            <div class="sidebar-card">
                <h3 class="sidebar-title">Brief Stats</h3>
                <div class="stat-list">
                    <div class="stat-item">
                        <span class="stat-label">Deadline</span>
                        <span class="stat-value">
                            <i class="fas fa-calendar-alt"></i>
                            {{ $brief->deadline->format('M d, Y \a\t g:i A') }}
                        </span>
                        <span class="deadline-indicator {{ $brief->isExpired() ? 'expired' : 'active' }}">
                            {{ $brief->isExpired() ? 'Expired' : 'Active' }}
                        </span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Submissions</span>
                        <span class="stat-value">
                            <i class="fas fa-upload"></i>
                            {{ $submissionsCount ?? 0 }} total
                        </span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Assigned</span>
                        <span class="stat-value">
                            <i class="fas fa-users"></i>
                            {{ $brief->assigned_students_count ?? 0 }} students
                        </span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Evaluations</span>
                        <span class="stat-value">
                            <i class="fas fa-star"></i>
                            {{ $brief->evaluations_count ?? 0 }} completed
                        </span>
                    </div>
                </div>
            </div>

            <div class="sidebar-card">
                <h3 class="sidebar-title">Actions</h3>
                <div class="action-list">
                    <a href="{{ route('teacher.briefs.submissions', $brief->id) }}" class="action-item">
                        <i class="fas fa-upload"></i> View Submissions
                    </a>
                    <a href="{{ route('teacher.briefs.results', $brief->id) }}" class="action-item">
                        <i class="fas fa-chart-bar"></i> View Results
                    </a>
                    <a href="#" class="action-item">
                        <i class="fas fa-share-alt"></i> Share with Students
                    </a>
                    <a href="#" class="action-item">
                        <i class="fas fa-download"></i> Download Brief
                    </a>
                    
                    @if($brief->isDraft())
                        <form action="{{ route('teacher.briefs.publish', $brief->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="action-item text-success">
                                <i class="fas fa-share-square"></i> Publish Brief
                            </button>
                        </form>
                    @elseif($brief->isActive())
                        <form action="{{ route('teacher.briefs.unpublish', $brief->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="action-item text-warning">
                                <i class="fas fa-eye-slash"></i> Unpublish Brief
                            </button>
                        </form>
                    @endif
                    
                    @if($submissionsCount == 0)
                        <form action="{{ route('teacher.briefs.destroy', $brief->id) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this brief? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-item text-danger">
                                <i class="fas fa-trash-alt"></i> Delete Brief
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="brief-content">
            <div class="content-card">
                <h3 class="content-title">Brief Description</h3>
                <div class="brief-description">
                    {{ $brief->description }}
                </div>
            </div>

            @if(isset($brief->attachment_path))
                <div class="content-card">
                    <h3 class="content-title">Attachment</h3>
                    <div class="attachment-container">
                        <a href="{{ Storage::url($brief->attachment_path) }}" target="_blank" class="attachment-link">
                            <i class="fas fa-file-alt"></i>
                            <span>View attached document</span>
                        </a>
                    </div>
                </div>
            @endif

            @if(isset($criteria) && count($criteria) > 0)
                <div class="content-card">
                    <h3 class="content-title">Evaluation Criteria</h3>
                    <div class="criteria-list">
                        @foreach($criteria as $criterion)
                            <div class="criterion-item">
                                <div class="criterion-header">
                                    <h4 class="criterion-title">{{ $criterion->title }}</h4>
                                </div>
                                <div class="criterion-description">
                                    {{ $criterion->description }}
                                </div>
                                
                                @if($criterion->tasks->count() > 0)
                                    <div class="task-list">
                                        @foreach($criterion->tasks as $task)
                                            <div class="task-item">
                                                <i class="fas fa-check-circle"></i>
                                                <span>{{ $task->description }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="content-card">
                    <h3 class="content-title">Evaluation Criteria</h3>
                    <div class="empty-state">
                        <i class="fas fa-clipboard-list"></i>
                        <p>No evaluation criteria have been defined for this brief.</p>
                        <a href="{{ route('teacher.briefs.edit', $brief->id) }}" class="btn btn-primary">Add Criteria</a>
                    </div>
                </div>
            @endif

            @if(isset($brief->skills) && !empty($brief->skills))
                <div class="content-card">
                    <h3 class="content-title">Skills & Competencies</h3>
                    <div class="skills-container">
                        @foreach(explode(',', $brief->skills) as $skill)
                            <span class="skill-badge">{{ trim($skill) }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(isset($brief->assigned_students) && count($brief->assigned_students) > 0)
                <div class="content-card">
                    <h3 class="content-title">Assigned Students</h3>
                    <div class="students-list">
                        @foreach($brief->assigned_students as $student)
                            <div class="student-item">
                                <div class="student-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="student-info">
                                    <span class="student-name">{{ $student->first_name }} {{ $student->last_name }}</span>
                                    <span class="student-email">{{ $student->email }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('styles')
<style>
    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
    }

    .title-container {
        display: flex;
        align-items: center;
        gap: 1rem;
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

    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .status-badge.draft {
        background-color: rgba(107, 114, 128, 0.1);
        color: #6B7280;
    }

    .status-badge.active {
        background-color: rgba(16, 185, 129, 0.1);
        color: #10B981;
    }

    .status-badge.expired {
        background-color: rgba(239, 68, 68, 0.1);
        color: #EF4444;
    }

    .page-actions {
        display: flex;
        gap: 0.75rem;
    }

    /* Brief Container Layout */
    .brief-container {
        display: grid;
        grid-template-columns: minmax(250px, 1fr) 3fr;
        gap: 1.5rem;
    }

    /* Sidebar */
    .brief-sidebar {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .sidebar-card {
        background-color: var(--highlight-color);
        border-radius: 0.5rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .sidebar-title {
        font-size: 1.1rem;
        margin-bottom: 1rem;
        color: var(--secondary-color);
        border-bottom: 1px solid rgba(229, 231, 235, 0.1);
        padding-bottom: 0.75rem;
    }

    /* Stats */
    .stat-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .stat-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .stat-label {
        color: var(--accent-color);
        font-size: 0.875rem;
    }

    .stat-value {
        color: var(--secondary-color);
        font-weight: 500;
    }

    .stat-value i {
        margin-right: 0.5rem;
        color: var(--primary-color);
    }

    .deadline-indicator {
        display: inline-block;
        margin-top: 0.25rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 500;
        width: max-content;
    }

    .deadline-indicator.active {
        background-color: rgba(16, 185, 129, 0.1);
        color: #10B981;
    }

    .deadline-indicator.expired {
        background-color: rgba(239, 68, 68, 0.1);
        color: #EF4444;
    }

    /* Actions */
    .action-list {
        display: flex;
        flex-direction: column;
    }

    .action-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 0;
        color: var(--secondary-color);
        text-decoration: none;
        border-bottom: 1px solid rgba(229, 231, 235, 0.1);
        background: none;
        border-left: none;
        border-right: none;
        border-top: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
        font-size: 0.95rem;
    }

    .action-item:last-child {
        border-bottom: none;
    }

    .action-item i {
        margin-right: 0.75rem;
        width: 1.25rem;
        color: var(--primary-color);
    }

    .action-item:hover {
        color: var(--primary-color);
    }

    .action-item.text-success i {
        color: #10B981;
    }

    .action-item.text-warning i {
        color: #F59E0B;
    }

    .action-item.text-danger i {
        color: #EF4444;
    }

    /* Brief Content */
    .brief-content {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .content-card {
        background-color: var(--highlight-color);
        border-radius: 0.5rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .content-title {
        font-size: 1.1rem;
        margin-bottom: 1rem;
        color: var(--secondary-color);
        border-bottom: 1px solid rgba(229, 231, 235, 0.1);
        padding-bottom: 0.75rem;
    }

    .brief-description {
        color: var(--secondary-color);
        line-height: 1.6;
        white-space: pre-line;
    }

    /* Attachment */
    .attachment-container {
        display: flex;
        align-items: center;
    }

    .attachment-link {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        background-color: rgba(37, 99, 235, 0.1);
        border-radius: 0.5rem;
        color: var(--primary-color);
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .attachment-link:hover {
        background-color: rgba(37, 99, 235, 0.2);
    }

    .attachment-link i {
        font-size: 1.25rem;
        margin-right: 0.75rem;
    }

    /* Criteria */
    .criteria-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .criterion-item {
        border: 1px solid rgba(229, 231, 235, 0.1);
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .criterion-header {
        background-color: rgba(255, 255, 255, 0.05);
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(229, 231, 235, 0.1);
    }

    .criterion-title {
        margin: 0;
        font-size: 1rem;
        color: var(--secondary-color);
    }

    .criterion-description {
        padding: 1rem 1.25rem;
        color: var(--secondary-color);
        line-height: 1.5;
    }

    .task-list {
        padding: 0 1.25rem 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .task-item {
        display: flex;
        align-items: center;
        color: var(--accent-color);
    }

    .task-item i {
        margin-right: 0.75rem;
        color: var(--primary-color);
        font-size: 0.875rem;
    }

    /* Skills */
    .skills-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .skill-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        background-color: rgba(37, 99, 235, 0.1);
        color: var(--primary-color);
        border-radius: 1rem;
        font-size: 0.875rem;
    }

    /* Students */
    .students-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
    }

    .student-item {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        border: 1px solid rgba(229, 231, 235, 0.1);
        border-radius: 0.5rem;
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
    }

    .student-email {
        color: var(--accent-color);
        font-size: 0.875rem;
    }

    /* Empty State */
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
        color: var(--primary-color);
    }

    .empty-state p {
        margin-bottom: 1rem;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .brief-container {
            grid-template-columns: 1fr;
        }

        .brief-sidebar {
            flex-direction: row;
            flex-wrap: wrap;
        }

        .sidebar-card {
            flex: 1;
            min-width: 250px;
        }
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
        }

        .page-actions {
            margin-top: 1rem;
            width: 100%;
        }

        .page-actions .btn {
            flex: 1;
            text-align: center;
        }

        .students-list {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection 