@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">My Profile</h4>
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Edit Profile
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            <div class="profile-avatar-container mb-3">
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->username }}" class="rounded-circle profile-avatar">
                            </div>
                            <h5 class="mb-1">{{ $user->full_name }}</h5>
                            <span class="badge rounded-pill {{ $user->isAdmin() ? 'bg-danger' : ($user->isTeacher() ? 'bg-primary' : 'bg-success') }}">
                                {{ ucfirst($user->role) }}
                            </span>
                            <p class="text-muted mt-2">{{ '@' . $user->username }}</p>

                            @if($user->isTeacher())
                                <div class="mt-3">
                                    <a href="{{ route('teacher.briefs.index') }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-file-alt me-1"></i> My Briefs
                                    </a>
                                </div>
                            @endif

                            @if($user->isStudent())
                                <div class="mt-3">
                                    <a href="{{ route('submissions.index') }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-upload me-1"></i> My Submissions
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h5 class="border-bottom pb-2 mb-3">Account Information</h5>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Username</label>
                                    <p>{{ $user->username }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Email</label>
                                    <p>{{ $user->email }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">First Name</label>
                                    <p>{{ $user->first_name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Last Name</label>
                                    <p>{{ $user->last_name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Account Type</label>
                                    <p>{{ ucfirst($user->role) }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Member Since</label>
                                    <p>{{ $user->created_at->format('F d, Y') }}</p>
                                </div>
                            </div>

                            @if($user->bio)
                                <h5 class="border-bottom pb-2 mb-3">Bio</h5>
                                <div class="profile-bio mb-4">
                                    <p>{{ $user->bio }}</p>
                                </div>
                            @endif

                            <h5 class="border-bottom pb-2 mb-3">Activity Summary</h5>
                            <div class="row">
                                @if($user->isTeacher())
                                    <div class="col-md-4 mb-3">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h3>{{ $user->briefs()->count() }}</h3>
                                                <p class="text-muted mb-0">Briefs Created</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($user->isStudent())
                                    <div class="col-md-4 mb-3">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h3>{{ $user->submissions()->count() }}</h3>
                                                <p class="text-muted mb-0">Submissions</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h3>{{ $user->evaluations()->count() }}</h3>
                                                <p class="text-muted mb-0">Evaluations</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .profile-avatar-container {
        width: 150px;
        height: 150px;
        margin: 0 auto;
        overflow: hidden;
        border: 5px solid rgba(0, 0, 0, 0.1);
        padding: 3px;
    }
    
    .profile-avatar {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .profile-bio {
        white-space: pre-line;
        color: #555;
    }
    
    @media (max-width: 767px) {
        .profile-avatar-container {
            width: 120px;
            height: 120px;
        }
    }
</style>
@endsection 