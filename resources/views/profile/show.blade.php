@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        @if(session('success'))
            <div class="bg-green-500 text-white px-4 py-3 rounded-lg shadow-sm mb-6 flex items-center justify-between" role="alert">
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

        <div class="bg-gray-800 rounded-xl overflow-hidden shadow-xl">
            <!-- Profile Header -->
            <div class="relative">
                <!-- Cover Image -->
                <div class="h-48 bg-gradient-to-r from-blue-600 via-blue-400 to-blue-800"></div>
                
                <!-- Profile Content -->
                <div class="relative px-6 pb-6">
                    <!-- Avatar -->
                    <div class="absolute -top-16 left-6">
                        <div class="w-32 h-32 rounded-full border-4 border-gray-800 overflow-hidden shadow-lg">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->username }}" class="w-full h-full object-cover">
                        </div>
                    </div>
                    
                    <!-- User Info -->
                    <div class="ml-40 pt-4">
                        <h3 class="text-2xl font-bold text-white mb-1">{{ $user->full_name }}</h3>
                        
                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            <span class="px-3 py-1 rounded-full text-sm font-medium 
                                {{ $user->isAdmin() ? 'bg-purple-600 text-white' : 
                                   ($user->isTeacher() ? 'bg-blue-600 text-white' : 
                                   'bg-green-600 text-white') }}">
                                <i class="fas {{ $user->isAdmin() ? 'fa-user-shield' : ($user->isTeacher() ? 'fa-chalkboard-teacher' : 'fa-user-graduate') }} mr-1"></i>
                                {{ ucfirst($user->role) }}
                            </span>
                            <span class="text-blue-400">{{ '@' . $user->username }}</span>
                        </div>
                        
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('profile.edit') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-300 inline-flex items-center">
                                <i class="fas fa-edit mr-2"></i> Edit Profile
                            </a>
                            
                            @if($user->isTeacher())
                                <a href="{{ route('teacher.briefs.index') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors duration-300 inline-flex items-center">
                                    <i class="fas fa-file-alt mr-2"></i> My Briefs
                                </a>
                            @endif

                            @if($user->isStudent())
                                <a href="{{ route('student.submissions.index') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors duration-300 inline-flex items-center">
                                    <i class="fas fa-upload mr-2"></i> My Submissions
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Profile Body -->
            <div class="p-6 bg-gray-900">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Activity Stats -->
                    <div>
                        <div class="bg-gray-800 rounded-lg p-5 shadow-md">
                            <h5 class="text-lg font-semibold text-white mb-4 flex items-center">
                                <i class="fas fa-chart-line mr-2 text-blue-400"></i>Activity Summary
                            </h5>
                            
                            <div class="space-y-4">
                                @if($user->isTeacher())
                                    <div class="bg-gray-700 rounded-lg p-4 flex items-center">
                                        <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center mr-4">
                                            <i class="fas fa-file-alt text-xl text-white"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-2xl font-bold text-white">{{ $user->briefs()->count() }}</h3>
                                            <p class="text-gray-400">Briefs Created</p>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-gray-700 rounded-lg p-4 flex items-center">
                                        <div class="w-12 h-12 rounded-full bg-green-600 flex items-center justify-center mr-4">
                                            <i class="fas fa-clipboard-check text-xl text-white"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-2xl font-bold text-white">{{ \App\Models\Submission::whereHas('brief', function($q) use ($user) { $q->where('teacher_id', $user->id); })->count() }}</h3>
                                            <p class="text-gray-400">Total Submissions</p>
                                        </div>
                                    </div>
                                @endif

                                @if($user->isStudent())
                                    <div class="bg-gray-700 rounded-lg p-4 flex items-center">
                                        <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center mr-4">
                                            <i class="fas fa-upload text-xl text-white"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-2xl font-bold text-white">{{ $user->submissions()->count() }}</h3>
                                            <p class="text-gray-400">Submissions</p>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-gray-700 rounded-lg p-4 flex items-center">
                                        <div class="w-12 h-12 rounded-full bg-purple-600 flex items-center justify-center mr-4">
                                            <i class="fas fa-user-check text-xl text-white"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-2xl font-bold text-white">{{ $user->evaluations()->count() }}</h3>
                                            <p class="text-gray-400">Evaluations</p>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="bg-gray-700 rounded-lg p-4 flex items-center">
                                    <div class="w-12 h-12 rounded-full bg-yellow-600 flex items-center justify-center mr-4">
                                        <i class="fas fa-calendar-alt text-xl text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-white">{{ $user->created_at->diffForHumans(null, true) }}</h3>
                                        <p class="text-gray-400">Member For</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- User Bio and Account Details -->
                    <div class="lg:col-span-2">
                        <!-- User Bio Section -->
                        @if($user->bio)
                            <div class="bg-gray-800 rounded-lg p-5 shadow-md mb-6">
                                <h5 class="text-lg font-semibold text-white mb-4 flex items-center">
                                    <i class="fas fa-quote-left mr-2 text-blue-400"></i>Bio
                                </h5>
                                <div class="text-gray-300">
                                    <p>{{ $user->bio }}</p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Account Details Section -->
                        <div class="bg-gray-800 rounded-lg p-5 shadow-md">
                            <h5 class="text-lg font-semibold text-white mb-4 flex items-center">
                                <i class="fas fa-user-circle mr-2 text-blue-400"></i>Account Information
                            </h5>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-700 p-3 rounded-lg">
                                    <span class="block text-sm text-gray-400">Username</span>
                                    <span class="text-white">{{ $user->username }}</span>
                                </div>
                                
                                <div class="bg-gray-700 p-3 rounded-lg">
                                    <span class="block text-sm text-gray-400">Email</span>
                                    <span class="text-white">{{ $user->email }}</span>
                                </div>
                                
                                <div class="bg-gray-700 p-3 rounded-lg">
                                    <span class="block text-sm text-gray-400">First Name</span>
                                    <span class="text-white">{{ $user->first_name }}</span>
                                </div>
                                
                                <div class="bg-gray-700 p-3 rounded-lg">
                                    <span class="block text-sm text-gray-400">Last Name</span>
                                    <span class="text-white">{{ $user->last_name }}</span>
                                </div>
                                
                                <div class="bg-gray-700 p-3 rounded-lg">
                                    <span class="block text-sm text-gray-400">Account Type</span>
                                    <span class="text-white">{{ ucfirst($user->role) }}</span>
                                </div>
                                
                                <div class="bg-gray-700 p-3 rounded-lg">
                                    <span class="block text-sm text-gray-400">Joined</span>
                                    <span class="text-white">{{ $user->created_at->format('F d, Y') }}</span>
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