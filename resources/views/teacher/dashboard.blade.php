@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-3xl font-bold mb-2">Teacher Dashboard</h1>
        <p class="text-gray-600 mb-6">Welcome back, {{ Auth::user()->first_name }}!</p>
        
        <!-- Summary Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4">
                        <i class="fas fa-file-alt text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Briefs</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $totalBriefs }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-paper-plane text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Submissions</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $totalSubmissions }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                        <i class="fas fa-clipboard-check text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Pending Evaluations</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $pendingEvaluations }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Active Students</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $activeStudents }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Dashboard Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Active Briefs -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow">
                <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800">Active Briefs</h2>
                    <a href="{{ route('teacher.briefs.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</a>
                </div>
                <div class="p-6">
                    @if(count($activeBriefs) > 0)
                        <div class="space-y-4">
                            @foreach($activeBriefs as $brief)
                                <div class="brief-item border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-150">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                        <div class="mb-2 md:mb-0">
                                            <h3 class="font-medium text-gray-900">{{ $brief->title }}</h3>
                                            <div class="text-sm text-gray-500">
                                                <span class="mr-4"><i class="far fa-calendar-alt mr-1"></i> Due: {{ $brief->end_date ? $brief->end_date->format('M d, Y') : 'No deadline' }}</span>
                                                <span><i class="far fa-file-alt mr-1"></i> {{ $brief->submissions_count }} submissions</span>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('teacher.briefs.show', $brief->id) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                View
                                            </a>
                                            <a href="{{ route('teacher.briefs.submissions', $brief->id) }}" class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                Submissions
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-2">
                                <i class="fas fa-file-alt text-4xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">No active briefs</h3>
                            <p class="text-gray-500 mb-3">You don't have any active briefs at the moment.</p>
                            <a href="{{ route('teacher.briefs.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-plus mr-2"></i> Create Brief
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="lg:col-span-1 bg-white rounded-lg shadow">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-xl font-bold text-gray-800">Quick Actions</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-4">
                        <a href="{{ route('teacher.briefs.create') }}" class="group flex items-center p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-indigo-50 hover:border-indigo-200 transition-colors duration-150">
                            <div class="flex-shrink-0 p-3 rounded-full bg-indigo-100 text-indigo-600 group-hover:bg-indigo-200">
                                <i class="fas fa-plus text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-medium text-gray-900 group-hover:text-indigo-900">Create Brief</h3>
                                <p class="text-sm text-gray-500 group-hover:text-indigo-700">Create a new assignment for students</p>
                            </div>
                        </a>
                        
                        <a href="{{ route('teacher.evaluations.assign') }}" class="group flex items-center p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-blue-50 hover:border-blue-200 transition-colors duration-150">
                            <div class="flex-shrink-0 p-3 rounded-full bg-blue-100 text-blue-600 group-hover:bg-blue-200">
                                <i class="fas fa-user-check text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-medium text-gray-900 group-hover:text-blue-900">Assign Evaluations</h3>
                                <p class="text-sm text-gray-500 group-hover:text-blue-700">Assign peer evaluations to students</p>
                            </div>
                        </a>

                        <a href="{{ route('teacher.evaluations.random') }}" class="group flex items-center p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-purple-50 hover:border-purple-200 transition-colors duration-150">
                            <div class="flex-shrink-0 p-3 rounded-full bg-purple-100 text-purple-600 group-hover:bg-purple-200">
                                <i class="fas fa-random text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-medium text-gray-900 group-hover:text-purple-900">Random Evaluations</h3>
                                <p class="text-sm text-gray-500 group-hover:text-purple-700">Randomly assign peer evaluations</p>
                            </div>
                        </a>
                        
                        <a href="{{ route('teacher.results.index') }}" class="group flex items-center p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-green-50 hover:border-green-200 transition-colors duration-150">
                            <div class="flex-shrink-0 p-3 rounded-full bg-green-100 text-green-600 group-hover:bg-green-200">
                                <i class="fas fa-chart-bar text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-medium text-gray-900 group-hover:text-green-900">View Results</h3>
                                <p class="text-sm text-gray-500 group-hover:text-green-700">See evaluation results and reports</p>
                            </div>
                        </a>
                        
                        <a href="{{ route('teacher.submissions.index') }}" class="group flex items-center p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-yellow-50 hover:border-yellow-200 transition-colors duration-150">
                            <div class="flex-shrink-0 p-3 rounded-full bg-yellow-100 text-yellow-600 group-hover:bg-yellow-200">
                                <i class="fas fa-inbox text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-medium text-gray-900 group-hover:text-yellow-900">All Submissions</h3>
                                <p class="text-sm text-gray-500 group-hover:text-yellow-700">View all student submissions</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Recent Submissions -->
            <div class="bg-white rounded-lg shadow">
                <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800">Recent Submissions</h2>
                    <a href="{{ route('teacher.submissions.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</a>
                </div>
                <div class="p-6">
                    @if(count($recentSubmissions) > 0)
                        <div class="space-y-4">
                            @foreach($recentSubmissions as $submission)
                                <div class="submission-item border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-150">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-medium text-gray-900">{{ $submission->user->username }}</h3>
                                            <p class="text-sm text-gray-500">
                                                Brief: {{ Str::limit($submission->brief->title, 30) }}
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1">
                                                Submitted: {{ $submission->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <a href="{{ route('teacher.submissions.show', $submission->id) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                            View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-2">
                                <i class="fas fa-inbox text-4xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">No recent submissions</h3>
                            <p class="text-gray-500">Students haven't submitted any work recently.</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Pending Evaluations -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow">
                <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800">Pending Evaluations</h2>
                    <a href="{{ route('teacher.evaluations.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</a>
                </div>
                <div class="p-6">
                    @if(count($evaluations) > 0)
                        <div class="space-y-4">
                            @foreach($evaluations as $evaluation)
                                <div class="evaluation-item border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-150 {{ $evaluation->is_overdue ? 'border-red-300 bg-red-50' : '' }}">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                        <div class="mb-2 md:mb-0">
                                            <h3 class="font-medium text-gray-900">
                                                Evaluator: {{ $evaluation->evaluator->username }}
                                                <span class="mx-2">•</span>
                                                Student: {{ $evaluation->submission->user->username }}
                                            </h3>
                                            <p class="text-sm text-gray-500">
                                                Brief: {{ Str::limit($evaluation->submission->brief->title, 40) }}
                                            </p>
                                            <p class="text-xs {{ $evaluation->is_overdue ? 'text-red-600 font-semibold' : 'text-gray-400' }} mt-1">
                                                @if($evaluation->due_date)
                                                    Due: {{ $evaluation->due_date->format('M d, Y') }}
                                                    @if($evaluation->is_overdue)
                                                        <span class="ml-2 text-red-600 font-semibold">(Overdue)</span>
                                                    @endif
                                                @else
                                                    Assigned: {{ $evaluation->created_at->format('M d, Y') }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="flex space-x-2 items-center">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                @if($evaluation->status == 'completed') bg-green-100 text-green-800
                                                @elseif($evaluation->status == 'in_progress') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($evaluation->status) }}
                                            </span>
                                            <a href="{{ route('teacher.evaluations.show', $evaluation->id) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-2">
                                <i class="fas fa-clipboard-check text-4xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">No pending evaluations</h3>
                            <p class="text-gray-500 mb-3">There are no pending evaluations at the moment.</p>
                            <a href="{{ route('teacher.evaluations.assign') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-plus mr-2"></i> Assign Evaluations
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Dashboard grid layout */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        @media (min-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: 2fr 1fr;
            }
        }
        
        /* Card design */
        .card {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        
        .card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Action cards */
        .action-cards {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .action-card {
            display: flex;
            align-items: center;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }
        
        .action-card:hover {
            background-color: #f3f4f6;
            border-color: #d1d5db;
        }
        
        .action-icon {
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            margin-right: 1rem;
        }
        
        /* Brief items */
        .brief-item {
            margin-bottom: 1rem;
            transition: all 0.2s;
        }
        
        .brief-item:hover {
            background-color: #f3f4f6;
        }
        
        /* Submission items */
        .submission-item {
            margin-bottom: 1rem;
            transition: all 0.2s;
        }
        
        .submission-item:hover {
            background-color: #f3f4f6;
        }
        
        /* Evaluation items */
        .evaluation-item {
            margin-bottom: 1rem;
            transition: all 0.2s;
        }
        
        .evaluation-item:hover {
            background-color: #f3f4f6;
        }
        
        /* Responsive styles */
        @media (max-width: 640px) {
            .action-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection 