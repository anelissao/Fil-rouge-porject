@extends('layouts.app')

@section('title', 'Manage Briefs')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Manage Briefs</h1>
            <p class="page-subtitle">Create, edit and manage student assignments</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('teacher.briefs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Create Brief
            </a>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="filter-container">
        <form action="{{ route('teacher.briefs.index') }}" method="GET" class="filter-form">
            <div class="form-group search-group">
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Search by title or description" 
                        class="form-control search-input"
                        value="{{ request('search') }}"
                    >
                    @if(request('search'))
                        <a href="{{ route('teacher.briefs.index', array_filter(request()->except('search'))) }}" class="clear-search">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>
            
            <div class="form-group">
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
            
            <div class="form-group">
                <select name="sort" class="form-control">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="deadline_asc" {{ request('sort') == 'deadline_asc' ? 'selected' : '' }}>Deadline (Soonest)</option>
                    <option value="deadline_desc" {{ request('sort') == 'deadline_desc' ? 'selected' : '' }}>Deadline (Latest)</option>
                    <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Title (A-Z)</option>
                    <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Title (Z-A)</option>
                </select>
            </div>

            <button type="submit" class="btn btn-filter">Apply Filters</button>
            
            @if(request()->anyFilled(['search', 'status', 'sort']))
                <a href="{{ route('teacher.briefs.index') }}" class="btn btn-reset">Reset</a>
            @endif
        </form>
    </div>

    <!-- Briefs List -->
    <div class="briefs-container">
        @if(isset($briefs) && count($briefs) > 0)
            <div class="briefs-grid">
                @foreach($briefs as $brief)
                    <div class="brief-card">
                        <div class="brief-status-badge {{ $brief->status }}">
                            {{ ucfirst($brief->status) }}
                        </div>
                        <div class="brief-header">
                            <h2 class="brief-title">{{ $brief->title }}</h2>
                            <div class="brief-menu">
                                <button class="btn-icon dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="{{ route('teacher.briefs.edit', $brief->id) }}" class="dropdown-item">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="{{ route('teacher.briefs.show', $brief->id) }}" class="dropdown-item">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('teacher.briefs.submissions', $brief->id) }}" class="dropdown-item">
                                        <i class="fas fa-upload"></i> Submissions
                                    </a>
                                    <a href="{{ route('teacher.briefs.results', $brief->id) }}" class="dropdown-item">
                                        <i class="fas fa-chart-bar"></i> Results
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    @if($brief->status == 'draft')
                                        <form action="{{ route('teacher.briefs.publish', $brief->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-share-square"></i> Publish
                                            </button>
                                        </form>
                                    @elseif($brief->status == 'active')
                                        <form action="{{ route('teacher.briefs.unpublish', $brief->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-eye-slash"></i> Unpublish
                                            </button>
                                        </form>
                                    @endif
                                    <div class="dropdown-divider"></div>
                                    <form action="{{ route('teacher.briefs.destroy', $brief->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this brief?')">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="brief-meta">
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Created: {{ $brief->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-clock"></i>
                                <span>Deadline: {{ $brief->deadline ? $brief->deadline->format('M d, Y') : 'No deadline' }}</span>
                            </div>
                        </div>
                        
                        <div class="brief-details">
                            <p class="brief-description">
                                {{ Str::limit($brief->description, 150) }}
                            </p>
                        </div>
                        
                        <div class="brief-stats">
                            <div class="stat-item">
                                <i class="fas fa-users"></i>
                                <span>{{ $brief->assigned_students_count ?? 0 }} Students</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-upload"></i>
                                <span>{{ $brief->submissions_count ?? 0 }} Submissions</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-star"></i>
                                <span>{{ $brief->evaluations_count ?? 0 }} Evaluations</span>
                            </div>
                        </div>
                        
                        <div class="brief-actions">
                            <a href="{{ route('teacher.briefs.edit', $brief->id) }}" class="btn btn-outline">Edit</a>
                            <a href="{{ route('teacher.briefs.submissions', $brief->id) }}" class="btn btn-primary">Submissions</a>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="pagination-container">
                {{ $briefs->appends(request()->query())->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-file-alt"></i>
                <h3>No briefs found</h3>
                @if(request()->anyFilled(['search', 'status', 'sort']))
                    <p>Try adjusting your search filters</p>
                    <a href="{{ route('teacher.briefs.index') }}" class="btn btn-outline">Reset Filters</a>
                @else
                    <p>Get started by creating your first brief</p>
                    <a href="{{ route('teacher.briefs.create') }}" class="btn btn-primary">Create Brief</a>
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

    /* Search and Filter */
    .filter-container {
        background-color: var(--highlight-color);
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
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
        color: var(--secondary-color);
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

    /* Briefs Grid */
    .briefs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
    }

    .brief-card {
        background-color: var(--highlight-color);
        border-radius: 0.5rem;
        padding: 1.5rem;
        position: relative;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .brief-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }

    .brief-status-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .brief-status-badge.active {
        background-color: rgba(16, 185, 129, 0.1);
        color: #10B981;
    }

    .brief-status-badge.draft {
        background-color: rgba(107, 114, 128, 0.1);
        color: #6B7280;
    }

    .brief-status-badge.expired {
        background-color: rgba(239, 68, 68, 0.1);
        color: #EF4444;
    }

    .brief-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .brief-title {
        font-size: 1.25rem;
        margin-bottom: 0;
        color: var(--secondary-color);
        padding-right: 2rem;
    }

    .btn-icon {
        background: transparent;
        border: none;
        color: var(--accent-color);
        cursor: pointer;
        font-size: 1rem;
        padding: 0.5rem;
    }

    .brief-meta {
        margin-bottom: 1rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
        color: var(--accent-color);
        font-size: 0.875rem;
    }

    .meta-item i {
        margin-right: 0.5rem;
        width: 1rem;
    }

    .brief-description {
        color: var(--accent-color);
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .brief-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        border-top: 1px solid rgba(229, 231, 235, 0.1);
        padding-top: 1rem;
    }

    .stat-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: var(--accent-color);
        font-size: 0.875rem;
        text-align: center;
    }

    .stat-item i {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
        color: var(--primary-color);
    }

    .brief-actions {
        display: flex;
        gap: 1rem;
    }

    .brief-actions .btn {
        flex: 1;
        text-align: center;
    }

    .btn-outline {
        background-color: transparent;
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
    }

    .btn-outline:hover {
        background-color: var(--primary-color);
        color: var(--secondary-color);
    }

    /* Empty State */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4rem 0;
        color: var(--accent-color);
        text-align: center;
    }

    .empty-state i {
        font-size: 4rem;
        color: var(--primary-color);
        margin-bottom: 1.5rem;
    }

    .empty-state h3 {
        color: var(--secondary-color);
        margin-bottom: 1rem;
    }

    .empty-state p {
        margin-bottom: 1.5rem;
    }

    /* Pagination */
    .pagination-container {
        margin-top: 2rem;
        display: flex;
        justify-content: center;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .page-actions {
            margin-top: 1rem;
        }

        .filter-form {
            flex-direction: column;
        }

        .form-group, .search-group {
            width: 100%;
        }

        .briefs-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection 