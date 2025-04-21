@extends('layouts.app')

@section('title', $brief->title)

@section('content')
    <div class="container py-4">
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('briefs.index') }}">Briefs</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $brief->title }}</li>
                        </ol>
                    </nav>
                    <h1 class="page-title mb-1">{{ $brief->title }}</h1>
                    <div class="brief-meta mb-2">
                        <span class="badge bg-{{ $brief->status == 'published' ? 'success' : ($brief->status == 'draft' ? 'warning' : 'secondary') }}">
                            {{ ucfirst($brief->status) }}
                        </span>
                        <span class="text-muted ms-2">
                            <i class="fas fa-user me-1"></i> {{ $brief->teacher->username ?? 'Unknown Teacher' }}
                        </span>
                        @if($brief->status == 'published')
                            <span class="text-muted ms-2">
                                <i class="fas fa-calendar me-1"></i> Deadline: {{ $brief->deadline ? $brief->deadline->format('M d, Y') : 'No deadline' }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="d-flex gap-2">
                    @if(auth()->user()->isTeacher() && $brief->teacher_id == auth()->id())
                        <a href="{{ route('teacher.briefs.edit', $brief->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                    @endif
                    
                    @if(auth()->user()->isStudent() && $brief->status == 'published' && !$brief->isExpired())
                        @if($hasSubmitted)
                            <a href="{{ route('student.submissions.index', ['brief_id' => $brief->id]) }}" class="btn btn-outline-success">
                                <i class="fas fa-check-circle me-1"></i> Submitted
                            </a>
                        @else
                            <a href="{{ route('student.submissions.create', ['brief_id' => $brief->id]) }}" class="btn btn-primary">
                                <i class="fas fa-upload me-1"></i> Submit
                            </a>
                        @endif
                    @endif
                    
                    <a href="{{ route('briefs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Briefs
                    </a>
                </div>
            </div>
        </div>

        <!-- Brief Details -->
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Brief Description</h5>
                        @if($brief->status == 'published')
                            <span class="text-{{ $brief->isExpired() ? 'danger' : 'success' }}">
                                <i class="fas fa-clock me-1"></i> 
                                {{ $brief->isExpired() ? 'Expired' : ($brief->deadline ? 'Due ' . $brief->deadline->diffForHumans() : 'No deadline') }}
                            </span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="brief-content">
                            {!! nl2br(e($brief->description)) !!}
                        </div>
                    </div>
                </div>

                @if(count($brief->criteria) > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Evaluation Criteria</h5>
                        </div>
                        <div class="card-body">
                            <div class="criteria-list">
                                @foreach($brief->criteria as $index => $criterion)
                                    <div class="criterion-item mb-3">
                                        <h6 class="criterion-title fw-bold">{{ $index + 1 }}. {{ $criterion->title }}</h6>
                                        <p class="criterion-description text-muted">
                                            {{ $criterion->description }}
                                        </p>
                                        <div class="criterion-weight">
                                            <span class="badge bg-info">Weight: {{ $criterion->weight }}%</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Brief Details</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-clock me-2"></i> Created</span>
                                <span>{{ $brief->created_at->format('M d, Y') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-calendar-alt me-2"></i> Deadline</span>
                                <span>{{ $brief->deadline ? $brief->deadline->format('M d, Y') : 'No deadline' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-upload me-2"></i> Submissions</span>
                                <span>{{ $brief->submissions_count ?? 0 }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-list-check me-2"></i> Criteria</span>
                                <span>{{ $brief->criteria->count() }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                @if(auth()->user()->isTeacher() && $brief->teacher_id == auth()->id())
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Teacher Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('teacher.briefs.submissions', $brief->id) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-file-upload me-1"></i> View Submissions
                                </a>
                                <a href="{{ route('teacher.briefs.results', $brief->id) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-chart-bar me-1"></i> View Results
                                </a>
                                
                                @if($brief->status == 'draft')
                                    <form action="{{ route('teacher.briefs.publish', $brief->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-globe me-1"></i> Publish Brief
                                        </button>
                                    </form>
                                @elseif($brief->status == 'published')
                                    <form action="{{ route('teacher.briefs.unpublish', $brief->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-warning w-100">
                                            <i class="fas fa-eye-slash me-1"></i> Unpublish Brief
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .brief-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .brief-content {
        white-space: pre-line;
    }
    
    .criterion-item {
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }
    
    .criterion-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
</style>
@endsection 