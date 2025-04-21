@extends('layouts.app')

@section('title', 'Edit Profile')

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
                        <div class="back-link position-absolute top-0 end-0 m-3">
                            <a href="{{ route('profile.show') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">
                                <i class="fas fa-arrow-left me-1"></i> Back to Profile
                            </a>
                        </div>
                        
                        <div class="w-100 text-center">
                            <h3 class="header-title"><i class="fas fa-user-edit me-2"></i>Edit Your Profile</h3>
                            <p class="header-subtitle">Update your personal information and preferences</p>
                        </div>
                    </div>
                </div>

                <div class="profile-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Avatar Section -->
                            <div class="col-lg-4 mb-4 mb-lg-0">
                                <div class="profile-section">
                                    <h5 class="section-title">
                                        <i class="fas fa-camera me-2"></i>Profile Picture
                                    </h5>
                                    
                                    <div class="avatar-upload-container">
                                        <div class="avatar-preview">
                                            <div class="profile-avatar-container mb-3 mx-auto">
                                                <img id="avatar-preview" src="{{ $user->avatar_url }}" alt="{{ $user->username }}" class="rounded-circle profile-avatar">
                                                <div class="avatar-overlay">
                                                    <i class="fas fa-camera"></i>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="avatar-controls text-center">
                                            <label for="avatar" class="btn btn-primary btn-sm rounded-pill px-3">
                                                <i class="fas fa-upload me-1"></i> Change Avatar
                                            </label>
                                            <input type="file" id="avatar" name="avatar" class="d-none" accept="image/*" onchange="previewAvatar(event)">
                                            
                                            @if($user->avatar)
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 ms-2" data-bs-toggle="modal" data-bs-target="#removeAvatarModal">
                                                    <i class="fas fa-trash-alt me-1"></i> Remove
                                                </button>
                                            @endif
                                            
                                            @error('avatar')
                                                <div class="text-danger mt-2">
                                                    <small><i class="fas fa-exclamation-circle me-1"></i> {{ $message }}</small>
                                                </div>
                                            @enderror
                                        </div>
                                        
                                        <div class="avatar-tip mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i> Recommended: Square image, at least 200x200 pixels. Max size: 2MB.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Profile Information -->
                            <div class="col-lg-8">
                                <!-- Account Information -->
                                <div class="profile-section">
                                    <h5 class="section-title">
                                        <i class="fas fa-user-circle me-2"></i>Account Information
                                    </h5>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control custom-input @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $user->username) }}" placeholder="Username" required>
                                                <label for="username">Username <span class="text-danger">*</span></label>
                                                @error('username')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="email" class="form-control custom-input @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="Email" required>
                                                <label for="email">Email <span class="text-danger">*</span></label>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control custom-input @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" placeholder="First Name" required>
                                                <label for="first_name">First Name <span class="text-danger">*</span></label>
                                                @error('first_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control custom-input @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" placeholder="Last Name" required>
                                                <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                                @error('last_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <textarea class="form-control custom-input @error('bio') is-invalid @enderror" id="bio" name="bio" placeholder="Tell others about yourself..." style="height: 120px">{{ old('bio', $user->bio) }}</textarea>
                                                <label for="bio">Bio</label>
                                                @error('bio')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text"><small>Write a short bio about yourself. This is optional and will be visible on your profile.</small></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Password Section -->
                                <div class="profile-section">
                                    <h5 class="section-title">
                                        <i class="fas fa-lock me-2"></i>Change Password
                                    </h5>
                                    
                                    <div class="password-info mb-3">
                                        <div class="d-flex align-items-center info-badge">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <span>Leave these fields blank if you don't want to change your password</span>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <div class="form-floating">
                                                <input type="password" class="form-control custom-input @error('current_password') is-invalid @enderror" id="current_password" name="current_password" placeholder="Current Password">
                                                <label for="current_password">Current Password</label>
                                                @error('current_password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="password" class="form-control custom-input @error('password') is-invalid @enderror" id="password" name="password" placeholder="New Password">
                                                <label for="password">New Password</label>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="password" class="form-control custom-input" id="password_confirmation" name="password_confirmation" placeholder="Confirm New Password">
                                                <label for="password_confirmation">Confirm New Password</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i> Save Changes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Remove Avatar Modal -->
<div class="modal fade" id="removeAvatarModal" tabindex="-1" aria-labelledby="removeAvatarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeAvatarModalLabel">
                    <i class="fas fa-trash-alt me-2 text-danger"></i>Remove Avatar
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove your profile picture? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('profile.avatar.remove') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Remove Avatar
                    </button>
                </form>
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
        min-height: 100px;
        padding: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .header-title {
        color: var(--secondary-color);
        margin-bottom: 0.5rem;
    }
    
    .header-subtitle {
        color: var(--accent-color);
        opacity: 0.8;
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
    
    /* Avatar Styles */
    .profile-avatar-container {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        position: relative;
        border: 5px solid var(--highlight-color);
    }
    
    .profile-avatar {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .avatar-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        opacity: 0;
        transition: opacity 0.3s;
        cursor: pointer;
    }
    
    .profile-avatar-container:hover .avatar-overlay {
        opacity: 1;
    }
    
    .avatar-tip {
        text-align: center;
    }
    
    /* Form Styles */
    .custom-input {
        background-color: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: var(--secondary-color);
    }
    
    .custom-input:focus {
        background-color: rgba(255, 255, 255, 0.15);
        border-color: var(--primary-color);
        color: var(--secondary-color);
        box-shadow: 0 0 0 0.25rem rgba(30, 144, 255, 0.25);
    }
    
    .form-floating > .custom-input {
        height: calc(3.5rem + 2px);
        line-height: 1.25;
    }
    
    .form-floating > label {
        padding: 1rem 0.75rem;
    }
    
    .form-floating > .custom-input:focus ~ label,
    .form-floating > .custom-input:not(:placeholder-shown) ~ label {
        opacity: 0.8;
        transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
        color: var(--primary-color);
    }
    
    .form-floating > textarea.custom-input {
        height: 120px;
    }
    
    /* Info badge for password section */
    .info-badge {
        background-color: rgba(52, 152, 219, 0.1);
        border-left: 3px solid var(--primary-color);
        padding: 10px 15px;
        border-radius: 5px;
        color: var(--accent-color);
    }
    
    /* Responsive Adjustments */
    @media (max-width: 767px) {
        .profile-avatar-container {
            width: 120px;
            height: 120px;
        }
        
        .profile-cover-img {
            height: 140px;
        }
        
        .section-title {
            font-size: 1.25rem;
        }
        
        .header-title {
            font-size: 1.5rem;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    function previewAvatar(event) {
        const file = event.target.files[0];
        
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('avatar-preview').src = e.target.result;
            }
            
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection 