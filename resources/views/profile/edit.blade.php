@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        @if(session('success'))
            <div class="bg-green-500/75 text-white px-4 py-3 rounded-lg shadow-sm mb-6 flex items-center justify-between" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="text-white hover:text-gray-100" onclick="this.parentElement.remove()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        <div class="bg-gray-800 rounded-xl shadow-xl overflow-hidden">
            <!-- Profile Header -->
            <div class="relative">
                <div class="h-48 bg-gradient-to-r from-blue-600 via-blue-400 to-blue-800"></div>
                <div class="absolute top-0 right-0 m-4">
                    <a href="{{ route('profile.show') }}" class="px-4 py-2 bg-gray-900/50 hover:bg-gray-900/70 text-white rounded-lg backdrop-blur-sm transition-all duration-300 border border-white/20 inline-flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Profile
                    </a>
                </div>
                <div class="text-center text-white absolute bottom-0 left-0 right-0 p-6">
                    <h1 class="text-2xl font-bold mb-1"><i class="fas fa-user-edit mr-2"></i>Edit Your Profile</h1>
                    <p class="text-blue-200">Update your personal information and preferences</p>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="p-6">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                        <!-- Avatar Section -->
                        <div class="lg:col-span-1">
                            <div class="bg-gray-700/30 rounded-xl p-6">
                                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                                    <i class="fas fa-camera mr-2 text-blue-400"></i>Profile Picture
                                </h3>
                                
                                <div class="flex flex-col items-center">
                                    <div class="relative group mb-4">
                                        <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-gray-800 mx-auto">
                                            <img id="avatar-preview" src="{{ $user->avatar_url }}" alt="{{ $user->username }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="absolute inset-0 bg-black/50 rounded-full opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity duration-300">
                                            <i class="fas fa-camera text-white text-xl"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="flex flex-wrap justify-center gap-2">
                                        <label for="avatar" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg cursor-pointer transition-colors inline-flex items-center">
                                            <i class="fas fa-upload mr-2"></i> Change Avatar
                                            <input type="file" id="avatar" name="avatar" class="hidden" accept="image/*" onchange="previewAvatar(event)">
                                        </label>
                                        
                                        @if($user->avatar)
                                            <button type="button" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors inline-flex items-center" data-bs-toggle="modal" data-bs-target="#removeAvatarModal">
                                                <i class="fas fa-trash-alt mr-2"></i> Remove
                                            </button>
                                        @endif
                                    </div>
                                    
                                    @error('avatar')
                                        <div class="mt-2 text-red-400 text-sm">
                                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                        </div>
                                    @enderror
                                    
                                    <div class="mt-4 text-center">
                                        <p class="text-xs text-gray-400">
                                            <i class="fas fa-info-circle mr-1"></i> Recommended: Square image, at least 200x200 pixels. Max size: 2MB.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Profile Information -->
                        <div class="lg:col-span-3 space-y-6">
                            <!-- Account Information -->
                            <div class="bg-gray-700/30 rounded-xl p-6">
                                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                                    <i class="fas fa-user-circle mr-2 text-blue-400"></i>Account Information
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="username" class="block text-sm font-medium text-gray-300 mb-1">Username <span class="text-red-400">*</span></label>
                                        <input type="text" class="w-full bg-gray-700 border border-gray-600 rounded-lg py-2 px-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('username') border-red-500 @enderror" 
                                            id="username" name="username" value="{{ old('username', $user->username) }}" required>
                                        @error('username')
                                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email <span class="text-red-400">*</span></label>
                                        <input type="email" class="w-full bg-gray-700 border border-gray-600 rounded-lg py-2 px-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror" 
                                            id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="first_name" class="block text-sm font-medium text-gray-300 mb-1">First Name <span class="text-red-400">*</span></label>
                                        <input type="text" class="w-full bg-gray-700 border border-gray-600 rounded-lg py-2 px-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('first_name') border-red-500 @enderror" 
                                            id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                                        @error('first_name')
                                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="last_name" class="block text-sm font-medium text-gray-300 mb-1">Last Name <span class="text-red-400">*</span></label>
                                        <input type="text" class="w-full bg-gray-700 border border-gray-600 rounded-lg py-2 px-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('last_name') border-red-500 @enderror" 
                                            id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                                        @error('last_name')
                                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div class="md:col-span-2">
                                        <label for="bio" class="block text-sm font-medium text-gray-300 mb-1">Bio</label>
                                        <textarea class="w-full bg-gray-700 border border-gray-600 rounded-lg py-2 px-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('bio') border-red-500 @enderror" 
                                            id="bio" name="bio" rows="4">{{ old('bio', $user->bio) }}</textarea>
                                        <p class="mt-1 text-xs text-gray-400">Write a short bio about yourself. This is optional and will be visible on your profile.</p>
                                        @error('bio')
                                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Password Section -->
                            <div class="bg-gray-700/30 rounded-xl p-6">
                                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                                    <i class="fas fa-lock mr-2 text-blue-400"></i>Change Password
                                </h3>
                                
                                <div class="bg-blue-900/30 border border-blue-800/50 rounded-lg p-3 mb-4 flex items-start">
                                    <i class="fas fa-info-circle text-blue-400 mt-1 mr-3"></i>
                                    <span class="text-sm text-blue-200">Leave these fields blank if you don't want to change your password</span>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label for="current_password" class="block text-sm font-medium text-gray-300 mb-1">Current Password</label>
                                        <input type="password" class="w-full bg-gray-700 border border-gray-600 rounded-lg py-2 px-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('current_password') border-red-500 @enderror" 
                                            id="current_password" name="current_password">
                                        @error('current_password')
                                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-300 mb-1">New Password</label>
                                        <input type="password" class="w-full bg-gray-700 border border-gray-600 rounded-lg py-2 px-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror" 
                                            id="password" name="password">
                                        @error('password')
                                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">Confirm New Password</label>
                                        <input type="password" class="w-full bg-gray-700 border border-gray-600 rounded-lg py-2 px-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                            id="password_confirmation" name="password_confirmation">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="flex justify-end">
                                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-300 flex items-center">
                                    <i class="fas fa-save mr-2"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Remove Avatar Modal -->
<div class="modal fade" id="removeAvatarModal" tabindex="-1" aria-labelledby="removeAvatarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-gray-800 border border-gray-700">
            <div class="modal-header border-gray-700">
                <h5 class="modal-title text-white" id="removeAvatarModalLabel">
                    <i class="fas fa-trash-alt mr-2 text-red-400"></i>Remove Avatar
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-gray-300">
                <p>Are you sure you want to remove your profile picture? This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-gray-700">
                <button type="button" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('profile.avatar.remove') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-trash-alt mr-1"></i> Remove Avatar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function previewAvatar(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('avatar-preview');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection 