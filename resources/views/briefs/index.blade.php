@extends('layouts.app')

@section('title', 'All Briefs')

@section('content')
    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="page-title">All Briefs</h1>
                <p class="text-muted">View all available briefs and their details</p>
            </div>
            @if(auth()->user()->isTeacher())
            <div class="col-md-4 text-end d-flex align-items-center justify-content-end">
                <a href="{{ route('teacher.briefs.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-2"></i> Create New Brief
                </a>
            </div>
            @endif
        </div>

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('briefs.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Search by title..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-secondary me-2">Apply Filters</button>
                        <a href="{{ route('briefs.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Briefs List -->
        <div class="briefs-list">
            @if(isset($briefs) && count($briefs) > 0)
                @foreach($briefs as $brief)
                    <div class="card brief-card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h3 class="brief-title">
                                        <a href="{{ auth()->user()->isTeacher() ? route('teacher.briefs.show', $brief->id) : route('briefs.show', $brief->id) }}">
                                            {{ $brief->title }}
                                        </a>
                                    </h3>
                                    <div class="brief-meta">
                                        <span class="badge bg-{{ $brief->status == 'published' ? 'success' : ($brief->status == 'draft' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($brief->status) }}
                                        </span>
                                        <span class="text-muted ms-3">
                                            <i class="fas fa-user me-1"></i> {{ $brief->teacher->username ?? 'Unknown Teacher' }}
                                        </span>
                                        @if($brief->status == 'published')
                                            <span class="text-muted ms-3">
                                                <i class="fas fa-calendar me-1"></i> Deadline: {{ $brief->deadline ? $brief->deadline->format('M d, Y') : 'No deadline' }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="brief-description mt-2">
                                        {{ \Illuminate\Support\Str::limit($brief->description, 150) }}
                                    </p>
                                </div>
                                <div class="col-md-4 d-flex flex-column justify-content-center align-items-end">
                                    @if($brief->status == 'published')
                                        <div class="deadline-indicator mb-2">
                                            @if($brief->isExpired())
                                                <span class="badge bg-danger"><i class="fas fa-clock me-1"></i> Expired</span>
                                            @else
                                                <span class="badge bg-info">
                                                    <i class="fas fa-clock me-1"></i> 
                                                    {{ $brief->deadline ? $brief->deadline->diffForHumans() : 'No deadline' }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    <div class="action-buttons">
                                        <a href="{{ auth()->user()->isTeacher() ? route('teacher.briefs.show', $brief->id) : route('briefs.show', $brief->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i> View
                                        </a>
                                        
                                        @if(auth()->user()->isTeacher() && $brief->teacher_id == auth()->id())
                                            <a href="{{ route('teacher.briefs.edit', $brief->id) }}" class="btn btn-sm btn-outline-secondary ms-1">
                                                <i class="fas fa-edit me-1"></i> Edit
                                            </a>
                                        @endif
                                        
                                        @if(auth()->user()->isStudent() && $brief->status == 'published' && !$brief->isExpired())
                                            <a href="{{ route('student.submissions.create', ['brief_id' => $brief->id]) }}" class="btn btn-sm btn-primary ms-1">
                                                <i class="fas fa-upload me-1"></i> Submit
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $briefs->links() }}
                </div>
            @else
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <h4>No briefs found</h4>
                    <p>There are no briefs available matching your criteria.</p>
                    @if(auth()->user()->isTeacher())
                        <a href="{{ route('teacher.briefs.create') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-plus-circle me-2"></i> Create New Brief
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection

@section('styles')
<style>
    .brief-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border-left: 5px solid #ccc;
    }
    
    .brief-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    /* Change border color based on status */
    .brief-card:has(.badge.bg-success) {
        border-left-color: var(--bs-success);
    }
    
    .brief-card:has(.badge.bg-warning) {
        border-left-color: var(--bs-warning);
    }
    
    .brief-card:has(.badge.bg-secondary) {
        border-left-color: var(--bs-secondary);
    }
    
    .brief-card:has(.badge.bg-danger) {
        border-left-color: var(--bs-danger);
    }
    
    .brief-title a {
        color: var(--bs-dark);
        text-decoration: none;
    }
    
    .brief-title a:hover {
        color: var(--bs-primary);
    }
    
    .brief-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    @media (max-width: 768px) {
        .action-buttons {
            margin-top: 1rem;
        }
        
        .col-md-4.d-flex {
            align-items: flex-start !important;
        }
    }
</style>
@endsection 