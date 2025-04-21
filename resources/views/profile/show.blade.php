@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>{{ session('success') }}</strong>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="profile-card border-0 shadow-lg rounded-3 overflow-hidden">
                <div class="profile-header position-relative">
                    <div class="profile-cover-img"></div>
                    <div class="profile-header-content">
                        <div class="profile-avatar-container shadow">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->username }}" class="rounded-circle profile-avatar">
                        </div>
                        <div class="profile-header-text">
                            <h3 class="mb-1">{{ $user->full_name }}</h3>
                            <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                <span class="badge rounded-pill user-role-badge {{ $user->isAdmin() ? 'admin-badge' : ($user->isTeacher() ? 'teacher-badge' : 'student-badge') }}">
                                    <i class="fas {{ $user->isAdmin() ? 'fa-user-shield' : ($user->isTeacher() ? 'fa-chalkboard-teacher' : 'fa-user-graduate') }} me-1"></i>
                                    {{ ucfirst($user->role) }}
                                </span>
                                <span class="profile-username">{{ '@' . $user->username }}</span>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                                    <i class="fas fa-edit me-1"></i> Edit Profile
                                </a>
                                
                                @if($user->isTeacher())
                                    <a href="{{ route('teacher.briefs.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 ms-2">
                                        <i class="fas fa-file-alt me-1"></i> My Briefs
                                    </a>
                                @endif

                                @if($user->isStudent())
                                    <a href="{{ route('student.submissions.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 ms-2">
                                        <i class="fas fa-upload me-1"></i> My Submissions
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="profile-body">
                    <div class="row">
                        <div class="col-lg-4 mb-4 mb-lg-0">
                            <!-- Activity Stats -->
                            <div class="profile-section">
                                <h5 class="section-title">
                                    <i class="fas fa-chart-line me-2"></i>Activity Summary
                                </h5>
                                
                                <div class="stats-container">
                                    @if($user->isTeacher())
                                        <div class="stat-card">
                                            <div class="stat-icon brief-icon">
                                                <i class="fas fa-file-alt"></i>
                                            </div>
                                            <div class="stat-content">
                                                <h3 class="stat-value">{{ $user->briefs()->count() }}</h3>
                                                <p class="stat-label">Briefs Created</p>
                                            </div>
                                        </div>
                                        
                                        <div class="stat-card mt-3">
                                            <div class="stat-icon evaluation-icon">
                                                <i class="fas fa-clipboard-check"></i>
                                            </div>
                                            <div class="stat-content">
                                                <h3 class="stat-value">{{ \App\Models\Submission::whereHas('brief', function($q) use ($user) { $q->where('teacher_id', $user->id); })->count() }}</h3>
                                                <p class="stat-label">Total Submissions</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if($user->isStudent())
                                        <div class="stat-card">
                                            <div class="stat-icon submission-icon">
                                                <i class="fas fa-upload"></i>
                                            </div>
                                            <div class="stat-content">
                                                <h3 class="stat-value">{{ $user->submissions()->count() }}</h3>
                                                <p class="stat-label">Submissions</p>
                                            </div>
                                        </div>
                                        
                                        <div class="stat-card mt-3">
                                            <div class="stat-icon evaluation-icon">
                                                <i class="fas fa-user-check"></i>
                                            </div>
                                            <div class="stat-content">
                                                <h3 class="stat-value">{{ $user->evaluations()->count() }}</h3>
                                                <p class="stat-label">Evaluations</p>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="stat-card mt-3">
                                        <div class="stat-icon badge-icon">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h3 class="stat-value">{{ $user->created_at->diffForHumans(null, true) }}</h3>
                                            <p class="stat-label">Member For</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-8">
                            <!-- User Bio Section -->
                            @if($user->bio)
                                <div class="profile-section mb-4">
                                    <h5 class="section-title">
                                        <i class="fas fa-quote-left me-2"></i>Bio
                                    </h5>
                                    <div class="profile-bio">
                                        <p>{{ $user->bio }}</p>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Account Details Section -->
                            <div class="profile-section">
                                <h5 class="section-title">
                                    <i class="fas fa-user-circle me-2"></i>Account Information
                                </h5>
                                
                                <div class="account-details">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="detail-item">
                                                <span class="detail-label">Username</span>
                                                <span class="detail-value">{{ $user->username }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <div class="detail-item">
                                                <span class="detail-label">Email</span>
                                                <span class="detail-value">{{ $user->email }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <div class="detail-item">
                                                <span class="detail-label">First Name</span>
                                                <span class="detail-value">{{ $user->first_name }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <div class="detail-item">
                                                <span class="detail-label">Last Name</span>
                                                <span class="detail-value">{{ $user->last_name }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <div class="detail-item">
                                                <span class="detail-label">Account Type</span>
                                                <span class="detail-value">{{ ucfirst($user->role) }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <div class="detail-item">
                                                <span class="detail-label">Joined</span>
                                                <span class="detail-value">{{ $user->created_at->format('F d, Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
    /* Profile Card Styles */
    .profile-card {
        background-color: var(--highlight-color);
    }
    
    /* Header Styles */
    .profile-header {
        color: var(--secondary-color);
    }
    
    .profile-cover-img {
        height: 180px;
        background: linear-gradient(135deg, #2980b9, #6dd5fa, #2c3e50);
        position: relative;
    }
    
    .profile-header-content {
        position: relative;
        margin-top: -75px;
        padding: 0 25px 20px;
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 1.5rem;
    }
    
    .profile-avatar-container {
        width: 150px;
        height: 150px;
        border: 5px solid var(--highlight-color);
        border-radius: 50%;
        overflow: hidden;
    }
    
    .profile-avatar {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .profile-header-text {
        flex: 1;
        min-width: 200px;
    }
    
    .profile-username {
        color: var(--accent-color);
        font-size: 0.95rem;
    }
    
    .user-role-badge {
        padding: 0.35rem 0.75rem;
        font-weight: 500;
    }
    
    .admin-badge {
        background-color: #e74c3c;
        color: white;
    }
    
    .teacher-badge {
        background-color: #3498db;
        color: white;
    }
    
    .student-badge {
        background-color: #2ecc71;
        color: white;
    }
    
    /* Body Styles */
    .profile-body {
        padding: 25px;
    }
    
    .profile-section {
        background-color: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .section-title {
        border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 10px;
        margin-bottom: 20px;
        color: var(--primary-color);
        font-weight: 600;
        display: flex;
        align-items: center;
    }
    
    /* Bio Section */
    .profile-bio {
        padding: 15px;
        background-color: rgba(255, 255, 255, 0.03);
        border-radius: 8px;
        border-left: 3px solid var(--primary-color);
        font-style: italic;
        white-space: pre-line;
        color: var(--accent-color);
    }
    
    /* Account Details */
    .account-details {
        background-color: rgba(255, 255, 255, 0.03);
        border-radius: 8px;
        padding: 15px;
    }
    
    .detail-item {
        display: flex;
        flex-direction: column;
        margin-bottom: 0.5rem;
    }
    
    .detail-label {
        font-size: 0.85rem;
        color: var(--accent-color);
        margin-bottom: 0.25rem;
    }
    
    .detail-value {
        font-weight: 500;
        color: var(--secondary-color);
    }
    
    /* Stats Styles */
    .stats-container {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .stat-card {
        display: flex;
        align-items: center;
        background-color: rgba(255, 255, 255, 0.03);
        border-radius: 8px;
        padding: 15px;
        transition: transform 0.2s;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-right: 15px;
        font-size: 1.5rem;
    }
    
    .brief-icon {
        background-color: rgba(52, 152, 219, 0.2);
        color: #3498db;
    }
    
    .submission-icon {
        background-color: rgba(230, 126, 34, 0.2);
        color: #e67e22;
    }
    
    .evaluation-icon {
        background-color: rgba(46, 204, 113, 0.2);
        color: #2ecc71;
    }
    
    .badge-icon {
        background-color: rgba(155, 89, 182, 0.2);
        color: #9b59b6;
    }
    
    .stat-content {
        flex: 1;
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0;
        color: var(--secondary-color);
    }
    
    .stat-label {
        color: var(--accent-color);
        font-size: 0.85rem;
        margin-bottom: 0;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 767px) {
        .profile-avatar-container {
            width: 120px;
            height: 120px;
        }
        
        .profile-header-content {
            justify-content: center;
            text-align: center;
        }
        
        .profile-header-text {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .profile-cover-img {
            height: 140px;
        }
        
        .section-title {
            font-size: 1.25rem;
        }
    }
</style>
@endsection 