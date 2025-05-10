@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section with Gradient Background -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-lg mb-8 p-6">
            <h1 class="text-3xl font-bold text-white mb-2">Teacher Dashboard</h1>
            <p class="text-blue-100">Welcome back, {{ Auth::user()->first_name }}!</p>
        </div>
        
        <!-- Summary Statistics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700 p-5 transform hover:scale-[1.02] transition-all duration-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center rounded-lg bg-blue-900/30 text-blue-400">
                        <i class="fas fa-file-alt text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-white">{{ $totalBriefs }}</div>
                        <div class="text-sm text-gray-400">Total Briefs</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700 p-5 transform hover:scale-[1.02] transition-all duration-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center rounded-lg bg-green-900/30 text-green-400">
                        <i class="fas fa-paper-plane text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-white">{{ $totalSubmissions }}</div>
                        <div class="text-sm text-gray-400">Total Submissions</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700 p-5 transform hover:scale-[1.02] transition-all duration-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center rounded-lg bg-yellow-900/30 text-yellow-400">
                        <i class="fas fa-clipboard-check text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-white">{{ $pendingEvaluations }}</div>
                        <div class="text-sm text-gray-400">Pending Evaluations</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700 p-5 transform hover:scale-[1.02] transition-all duration-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center rounded-lg bg-purple-900/30 text-purple-400">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-white">{{ $activeStudents }}</div>
                        <div class="text-sm text-gray-400">Active Students</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Dashboard Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Active Briefs -->
            <div class="lg:col-span-2 bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700">
                <div class="border-b border-gray-700 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white">Active Briefs</h2>
                    <a href="{{ route('teacher.briefs.index') }}" class="text-blue-400 hover:text-blue-300 transition-colors flex items-center">
                        <span>View All</span>
                        <i class="fas fa-arrow-right ml-2 text-sm"></i>
                    </a>
                </div>
                <div class="p-6">
                    @if(count($activeBriefs) > 0)
                        <div class="space-y-4">
                            @foreach($activeBriefs as $brief)
                                <div class="brief-item border border-gray-700 rounded-lg p-4 bg-gray-750 hover:bg-gray-700 transition-colors duration-150">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                        <div class="mb-2 md:mb-0">
                                            <h3 class="font-medium text-white">{{ $brief->title }}</h3>
                                            <div class="text-sm text-gray-400">
                                                <span class="mr-4"><i class="far fa-calendar-alt mr-1"></i> Due: {{ $brief->deadline ? $brief->deadline->format('M d, Y') : 'No deadline' }}</span>
                                                <span><i class="far fa-file-alt mr-1"></i> {{ $brief->submissions_count }} submissions</span>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('teacher.briefs.show', $brief->id) }}" class="px-3 py-1 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                                View
                                            </a>
                                            <a href="{{ route('teacher.briefs.submissions', $brief->id) }}" class="px-3 py-1 text-xs bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                                                Submissions
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-8 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-700 text-gray-500 mb-4">
                                <i class="fas fa-file-alt text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-medium text-white mb-2">No active briefs</h3>
                            <p class="text-gray-400 max-w-md mx-auto">You don't have any active briefs at the moment.</p>
                            <a href="{{ route('teacher.briefs.create') }}" class="inline-flex items-center px-4 py-2 mt-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                <i class="fas fa-plus mr-2"></i> Create Brief
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="lg:col-span-1 bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700">
                <div class="border-b border-gray-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">Quick Actions</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-4">
                        <a href="{{ route('teacher.briefs.create') }}" class="flex items-center p-4 bg-gray-750 rounded-lg hover:bg-gray-700 transition-colors group">
                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-blue-900/30 text-blue-400 group-hover:bg-blue-800/50 transition-colors">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="ml-4 text-white">Create Brief</div>
                        </a>
                        
                        <a href="{{ route('teacher.evaluations.assign') }}" class="flex items-center p-4 bg-gray-750 rounded-lg hover:bg-gray-700 transition-colors group">
                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-green-900/30 text-green-400 group-hover:bg-green-800/50 transition-colors">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="ml-4 text-white">Assign Evaluations</div>
                        </a>

                        <a href="{{ route('teacher.evaluations.random') }}" class="flex items-center p-4 bg-gray-750 rounded-lg hover:bg-gray-700 transition-colors group">
                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-purple-900/30 text-purple-400 group-hover:bg-purple-800/50 transition-colors">
                                <i class="fas fa-random"></i>
                            </div>
                            <div class="ml-4 text-white">Random Evaluations</div>
                        </a>
                        
                        <a href="{{ route('teacher.results.index') }}" class="flex items-center p-4 bg-gray-750 rounded-lg hover:bg-gray-700 transition-colors group">
                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-green-900/30 text-green-400 group-hover:bg-green-800/50 transition-colors">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="ml-4 text-white">View Results</div>
                        </a>
                        
                        <a href="{{ route('teacher.submissions.index') }}" class="flex items-center p-4 bg-gray-750 rounded-lg hover:bg-gray-700 transition-colors group">
                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-yellow-900/30 text-yellow-400 group-hover:bg-yellow-800/50 transition-colors">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <div class="ml-4 text-white">All Submissions</div>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Recent Submissions -->
            <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700">
                <div class="border-b border-gray-700 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white">Recent Submissions</h2>
                    <a href="{{ route('teacher.submissions.index') }}" class="text-blue-400 hover:text-blue-300 transition-colors flex items-center">
                        <span>View All</span>
                        <i class="fas fa-arrow-right ml-2 text-sm"></i>
                    </a>
                </div>
                <div class="p-6">
                    @if(count($recentSubmissions) > 0)
                        <div class="space-y-4">
                            @foreach($recentSubmissions as $submission)
                                <div class="submission-item border border-gray-700 rounded-lg p-4 bg-gray-750 hover:bg-gray-700 transition-colors duration-150">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-medium text-white">{{ $submission->student->username }}</h3>
                                            <p class="text-sm text-gray-400">
                                                Brief: {{ Str::limit($submission->brief->title, 30) }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Submitted: {{ $submission->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <a href="{{ route('teacher.submissions.show', $submission->id) }}" class="px-3 py-1 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                            View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-8 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-700 text-gray-500 mb-4">
                                <i class="fas fa-inbox text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-medium text-white mb-2">No recent submissions</h3>
                            <p class="text-gray-400 max-w-md mx-auto">Students haven't submitted any work recently.</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Pending Evaluations -->
            <div class="lg:col-span-2 bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700">
                <div class="border-b border-gray-700 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white">Pending Evaluations</h2>
                    <a href="{{ route('teacher.evaluations.index') }}" class="text-blue-400 hover:text-blue-300 transition-colors flex items-center">
                        <span>View All</span>
                        <i class="fas fa-arrow-right ml-2 text-sm"></i>
                    </a>
                </div>
                <div class="p-6">
                    @if(count($evaluations) > 0)
                        <div class="space-y-4">
                            @foreach($evaluations as $evaluation)
                                <div class="evaluation-item border rounded-lg p-4 bg-gray-750 hover:bg-gray-700 transition-colors duration-150 {{ $evaluation->is_overdue ? 'border-red-500/30 bg-red-900/10' : 'border-gray-700' }}">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                        <div class="mb-2 md:mb-0">
                                            <h3 class="font-medium text-white">
                                                Evaluator: {{ $evaluation->evaluator->username }}
                                                <span class="mx-2">•</span>
                                                Student: {{ $evaluation->submission->student->username }}
                                            </h3>
                                            <p class="text-sm text-gray-400">
                                                Brief: {{ Str::limit($evaluation->submission->brief->title, 40) }}
                                            </p>
                                            <p class="text-xs {{ $evaluation->is_overdue ? 'text-red-400 font-semibold' : 'text-gray-500' }} mt-1">
                                                @if($evaluation->due_at)
                                                    Due: {{ $evaluation->due_at->format('M d, Y') }}
                                                    @if($evaluation->is_overdue)
                                                        <span class="ml-2 text-red-400 font-semibold">(Overdue)</span>
                                                    @endif
                                                @else
                                                    Assigned: {{ $evaluation->created_at->format('M d, Y') }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="flex space-x-2 items-center">
                                            <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center
                                                @if($evaluation->status == 'completed') bg-green-900/30 text-green-400
                                                @elseif($evaluation->status == 'in_progress') bg-yellow-900/30 text-yellow-400
                                                @else bg-gray-700 text-gray-400 @endif">
                                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 
                                                    @if($evaluation->status == 'completed') bg-green-400
                                                    @elseif($evaluation->status == 'in_progress') bg-yellow-400
                                                    @else bg-gray-400 @endif"></span>
                                                {{ ucfirst($evaluation->status) }}
                                            </span>
                                            <a href="{{ route('teacher.evaluations.show', $evaluation->id) }}" class="px-3 py-1 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-8 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-700 text-gray-500 mb-4">
                                <i class="fas fa-clipboard-check text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-medium text-white mb-2">No pending evaluations</h3>
                            <p class="text-gray-400 max-w-md mx-auto">All evaluations have been completed.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection 