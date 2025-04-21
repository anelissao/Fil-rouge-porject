@extends('layouts.app')

@section('title', 'Submissions for ' . $brief->title)

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Submissions for Brief</h1>
            <p class="page-subtitle">{{ $brief->title }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('teacher.briefs.show', $brief->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Brief
            </a>
            <a href="{{ route('teacher.briefs.index') }}" class="btn btn-outline">
                <i class="fas fa-list"></i> All Briefs
            </a>
        </div>
    </div>

    <div class="submissions-container">
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $brief->assigned_students_count ?? 0 }}</h3>
                    <p class="stat-label">Assigned Students</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-upload"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $submissions->total() ?? 0 }}</h3>
                    <p class="stat-label">Total Submissions</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">
                        @if($brief->assigned_students_count > 0)
                            {{ round(($submissions->total() / $brief->assigned_students_count) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </h3>
                    <p class="stat-label">Submission Rate</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $evaluatedCount ?? 0 }}</h3>
                    <p class="stat-label">Evaluated</p>
                </div>
            </div>
        </div>

        <div class="filter-container">
            <form action="{{ route('teacher.briefs.submissions', $brief->id) }}" method="GET" class="filter-form">
                <div class="form-group search-group">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Search by student name or username" 
                            class="form-control search-input"
                            value="{{ request('search') }}"
                        >
                        @if(request('search'))
                            <a href="{{ route('teacher.briefs.submissions', $brief->id) }}" class="clear-search">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
                
                <div class="form-group">
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="evaluated" {{ request('status') == 'evaluated' ? 'selected' : '' }}>Evaluated</option>
                        <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late Submissions</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <select name="sort" class="form-control">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="student_asc" {{ request('sort') == 'student_asc' ? 'selected' : '' }}>Student (A-Z)</option>
                        <option value="student_desc" {{ request('sort') == 'student_desc' ? 'selected' : '' }}>Student (Z-A)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-filter">Apply Filters</button>
                
                @if(request()->anyFilled(['search', 'status', 'sort']))
                    <a href="{{ route('teacher.briefs.submissions', $brief->id) }}" class="btn btn-reset">Reset</a>
                @endif
            </form>
        </div>

        <!-- Submissions List -->
        @if(isset($submissions) && count($submissions) > 0)
            <div class="table-container">
                <table class="submissions-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Submission Date</th>
                            <th>Status</th>
                            <th>Evaluation</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submissions as $submission)
                            <tr>
                                <td class="student-cell">
                                    <div class="student-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="student-info">
                                        <div class="student-name">{{ $submission->student->first_name }} {{ $submission->student->last_name }}</div>
                                        <div class="student-username">{{ $submission->student->username }}</div>
                                    </div>
                                </td>
                                <td class="date-cell">
                                    <div class="submission-date">
                                        {{ $submission->created_at->format('M d, Y') }}
                                    </div>
                                    <div class="submission-time">
                                        {{ $submission->created_at->format('g:i A') }}
                                        @if($submission->created_at > $brief->deadline)
                                            <span class="late-tag">Late</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="status-cell">
                                    <span class="status-badge {{ $submission->status }}">
                                        {{ ucfirst($submission->status) }}
                                    </span>
                                </td>
                                <td class="evaluation-cell">
                                    @if($submission->evaluations->count() > 0)
                                        <div class="evaluation-status">
                                            <i class="fas fa-check-circle text-success"></i> 
                                            {{ $submission->evaluations->count() }} evaluations
                                        </div>
                                    @else
                                        <div class="evaluation-status pending">
                                            <i class="fas fa-hourglass-half"></i> Pending
                                        </div>
                                    @endif
                                </td>
                                <td class="actions-cell">
                                    <div class="action-buttons">
                                        <a href="{{ route('teacher.submissions.show', $submission->id) }}" class="btn-action view">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn-action evaluate">
                                            <i class="fas fa-star"></i>
                                        </a>
                                        <a href="#" class="btn-action download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="pagination-container">
                {{ $submissions->appends(request()->query())->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-file-upload"></i>
                <h3>No submissions found</h3>
                @if(request()->anyFilled(['search', 'status', 'sort']))
                    <p>Try adjusting your search filters</p>
                    <a href="{{ route('teacher.briefs.submissions', $brief->id) }}" class="btn btn-outline">Reset Filters</a>
                @else
                    <p>There are no submissions for this brief yet.</p>
                    <a href="{{ route('teacher.briefs.show', $brief->id) }}" class="btn btn-primary">Back to Brief</a>
                @endif
            </div>
        @endif
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

    /* Stats Row */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background-color: var(--highlight-color);
        border-radius: 0.5rem;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        width: 3rem;
        height: 3rem;
        background-color: rgba(37, 99, 235, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
    }

    .stat-icon i {
        font-size: 1.25rem;
        color: var(--primary-color);
    }

    .stat-content {
        flex: 1;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
        color: var(--secondary-color);
    }

    .stat-label {
        color: var(--accent-color);
        font-size: 0.875rem;
        margin: 0;
    }

    /* Filter Container */
    .filter-container {
        background-color: var(--highlight-color);
        border-radius: 0.5rem;
        padding: 1.25rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
    }

    .search-group {
        flex: 2;
        min-width: 300px;
    }

    .search-input-wrapper {
        position: relative;
    }

    .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--accent-color);
    }

    .search-input {
        padding-left: 2.5rem;
        height: 2.75rem;
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(229, 231, 235, 0.1);
        color: var(--secondary-color);
    }

    .search-input:focus {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: var(--primary-color);
    }

    .clear-search {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--accent-color);
        cursor: pointer;
    }

    .form-group {
        flex: 1;
        min-width: 180px;
    }

    .form-control {
        height: 2.75rem;
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(229, 231, 235, 0.1);
        color: var(--secondary-color);
    }

    .form-control:focus {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: var(--primary-color);
    }

    .btn-filter {
        height: 2.75rem;
        background-color: var(--primary-color);
        color: white;
        border: none;
        white-space: nowrap;
    }

    .btn-reset {
        height: 2.75rem;
        background-color: transparent;
        color: var(--accent-color);
        border: 1px solid var(--accent-color);
        white-space: nowrap;
    }

    /* Table */
    .table-container {
        overflow-x: auto;
        background-color: var(--highlight-color);
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .submissions-table {
        width: 100%;
        border-collapse: collapse;
    }

    .submissions-table th,
    .submissions-table td {
        padding: 1rem;
        text-align: left;
    }

    .submissions-table th {
        background-color: rgba(0, 0, 0, 0.1);
        color: var(--secondary-color);
        font-weight: 600;
        font-size: 0.95rem;
        white-space: nowrap;
    }

    .submissions-table tr {
        border-bottom: 1px solid rgba(229, 231, 235, 0.1);
    }

    .submissions-table tr:last-child {
        border-bottom: none;
    }

    .submissions-table tr:hover {
        background-color: rgba(255, 255, 255, 0.03);
    }

    /* Table Cell Content */
    .student-cell {
        display: flex;
        align-items: center;
        min-width: 200px;
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

    .student-username {
        color: var(--accent-color);
        font-size: 0.875rem;
    }

    .date-cell {
        min-width: 140px;
    }

    .submission-date {
        color: var(--secondary-color);
        font-size: 0.95rem;
        margin-bottom: 0.25rem;
    }

    .submission-time {
        color: var(--accent-color);
        font-size: 0.875rem;
        display: flex;
        align-items: center;
    }

    .late-tag {
        display: inline-block;
        margin-left: 0.5rem;
        padding: 0.125rem 0.5rem;
        background-color: rgba(239, 68, 68, 0.1);
        color: #EF4444;
        border-radius: 0.25rem;
        font-size: 0.75rem;
    }

    .status-cell {
        min-width: 120px;
    }

    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .status-badge.submitted {
        background-color: rgba(37, 99, 235, 0.1);
        color: #2563EB;
    }

    .status-badge.evaluated {
        background-color: rgba(16, 185, 129, 0.1);
        color: #10B981;
    }

    .status-badge.draft {
        background-color: rgba(107, 114, 128, 0.1);
        color: #6B7280;
    }

    .evaluation-cell {
        min-width: 140px;
    }

    .evaluation-status {
        color: var(--secondary-color);
        font-size: 0.95rem;
        display: flex;
        align-items: center;
    }

    .evaluation-status i {
        margin-right: 0.5rem;
    }

    .evaluation-status.pending {
        color: var(--accent-color);
    }

    .text-success {
        color: #10B981;
    }

    .actions-cell {
        min-width: 120px;
    }

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

    .btn-action.evaluate {
        background-color: rgba(16, 185, 129, 0.1);
    }

    .btn-action.evaluate:hover {
        background-color: #10B981;
    }

    .btn-action.download {
        background-color: rgba(245, 158, 11, 0.1);
    }

    .btn-action.download:hover {
        background-color: #F59E0B;
    }

    /* Empty State */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem 0;
        background-color: var(--highlight-color);
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        color: var(--accent-color);
        text-align: center;
    }

    .empty-state i {
        font-size: 3rem;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        color: var(--secondary-color);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        margin-bottom: 1.5rem;
    }

    /* Pagination */
    .pagination-container {
        margin-top: 1.5rem;
        display: flex;
        justify-content: center;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .stats-row {
            grid-template-columns: repeat(2, 1fr);
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

        .table-container {
            margin: 0 -1rem;
            border-radius: 0;
        }

        .submissions-table th:nth-child(4),
        .submissions-table td:nth-child(4) {
            display: none;
        }
    }

    @media (max-width: 576px) {
        .stats-row {
            grid-template-columns: 1fr;
        }

        .submissions-table th:nth-child(2),
        .submissions-table td:nth-child(2) {
            display: none;
        }
    }
</style>
@endsection 