@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section with Gradient Background -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-lg mb-8 p-6">
        <h1 class="text-3xl font-bold text-white mb-2">Dashboard</h1>
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
                    <div class="text-2xl font-bold text-white">{{ $totalSubmissions }}</div>
                    <div class="text-sm text-gray-400">Total Submissions</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700 p-5 transform hover:scale-[1.02] transition-all duration-300">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center rounded-lg bg-green-900/30 text-green-400">
                    <i class="fas fa-clipboard-check text-2xl"></i>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-white">{{ $pendingSubmissions }}</div>
                    <div class="text-sm text-gray-400">Pending Submissions</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700 p-5 transform hover:scale-[1.02] transition-all duration-300">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center rounded-lg bg-yellow-900/30 text-yellow-400">
                    <i class="fas fa-tasks text-2xl"></i>
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
                    <i class="fas fa-award text-2xl"></i>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-white">{{ $receivedEvaluations }}</div>
                    <div class="text-sm text-gray-400">Received Evaluations</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Active Briefs Section -->
        <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700">
            <div class="border-b border-gray-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white">Active Briefs</h2>
            </div>
            <div class="p-6">
                @if(count($activeBriefs) > 0)
                    <ul class="divide-y divide-gray-700">
                        @foreach($activeBriefs as $brief)
                            <li class="py-4">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h3 class="font-medium text-white">{{ $brief->title }}</h3>
                                        <p class="text-sm text-gray-400">
                                            Due: {{ $brief->deadline->format('M d, Y') }}
                                        </p>
                                    </div>
                                    <div class="flex space-x-2">
                                        @if($brief->hasSubmitted)
                                            <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center bg-green-900/30 text-green-400">
                                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-green-400"></span>
                                                Submitted
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center bg-yellow-900/30 text-yellow-400">
                                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-yellow-400"></span>
                                                Pending
                                            </span>
                                        @endif
                                        <a href="{{ route('briefs.show', $brief->id) }}" class="px-3 py-1 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">View</a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="py-8 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-700 text-gray-500 mb-3">
                            <i class="fas fa-book text-xl"></i>
                        </div>
                        <p class="text-gray-400">No active briefs available at the moment.</p>
                    </div>
                @endif
            </div>
            <div class="border-t border-gray-700 px-6 py-3">
                <a href="{{ route('briefs.index') }}" class="text-blue-400 hover:text-blue-300 transition-colors flex items-center">
                    <span>View all briefs</span>
                    <i class="fas fa-arrow-right ml-2 text-sm"></i>
                </a>
            </div>
        </div>

        <!-- My Evaluations Section -->
        <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700">
            <div class="border-b border-gray-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white">My Evaluations</h2>
            </div>
            <div class="p-6">
                @if(count($evaluations) > 0)
                    <ul class="divide-y divide-gray-700">
                        @foreach($evaluations as $evaluation)
                            <li class="py-4">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h3 class="font-medium text-white">{{ $evaluation->submission->brief->title }}</h3>
                                        <p class="text-sm text-gray-400">
                                            Student: {{ $evaluation->submission->student->username }}
                                        </p>
                                    </div>
                                    <div class="flex space-x-2">
                                        @if($evaluation->is_overdue)
                                            <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center bg-red-900/30 text-red-400">
                                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-red-400"></span>
                                                Overdue
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center bg-yellow-900/30 text-yellow-400">
                                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-yellow-400"></span>
                                                Pending
                                            </span>
                                        @endif
                                        <a href="{{ route('student.evaluations.edit', $evaluation->id) }}" class="px-3 py-1 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">Evaluate</a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="py-8 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-700 text-gray-500 mb-3">
                            <i class="fas fa-clipboard-check text-xl"></i>
                        </div>
                        <p class="text-gray-400">No evaluations assigned to you at the moment.</p>
                    </div>
                @endif
            </div>
            <div class="border-t border-gray-700 px-6 py-3">
                <a href="{{ route('student.evaluations.index') }}" class="text-blue-400 hover:text-blue-300 transition-colors flex items-center">
                    <span>View all evaluations</span>
                    <i class="fas fa-arrow-right ml-2 text-sm"></i>
                </a>
            </div>
        </div>

        <!-- My Results Section -->
        <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700">
            <div class="border-b border-gray-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white">My Results</h2>
            </div>
            <div class="p-6">
                @if(count($receivedEvaluationsList) > 0)
                    <ul class="divide-y divide-gray-700">
                        @foreach($receivedEvaluationsList as $evaluation)
                            <li class="py-4">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h3 class="font-medium text-white">{{ $evaluation->submission->brief->title }}</h3>
                                        <p class="text-sm text-gray-400">
                                            Evaluated by: {{ $evaluation->evaluator->username }}
                                        </p>
                                    </div>
                                    <div>
                                        <a href="{{ route('student.evaluations.show', $evaluation->id) }}" class="px-3 py-1 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">View Results</a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="py-8 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-700 text-gray-500 mb-3">
                            <i class="fas fa-award text-xl"></i>
                        </div>
                        <p class="text-gray-400">No evaluation results received yet.</p>
                    </div>
                @endif
            </div>
            <div class="border-t border-gray-700 px-6 py-3">
                <a href="{{ route('student.evaluations.index') }}" class="text-blue-400 hover:text-blue-300 transition-colors flex items-center">
                    <span>View all results</span>
                    <i class="fas fa-arrow-right ml-2 text-sm"></i>
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700">
            <div class="border-b border-gray-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white">Quick Actions</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-4">
                    <a href="{{ route('briefs.index') }}" class="flex items-center p-4 bg-gray-750 rounded-lg hover:bg-gray-700 transition-colors group">
                        <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-blue-900/30 text-blue-400 group-hover:bg-blue-800/50 transition-colors">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="ml-4 text-white">View All Briefs</div>
                    </a>
                    <a href="{{ route('student.submissions.index') }}" class="flex items-center p-4 bg-gray-750 rounded-lg hover:bg-gray-700 transition-colors group">
                        <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-green-900/30 text-green-400 group-hover:bg-green-800/50 transition-colors">
                            <i class="fas fa-file-upload"></i>
                        </div>
                        <div class="ml-4 text-white">My Submissions</div>
                    </a>
                    <a href="{{ route('student.evaluations.index') }}" class="flex items-center p-4 bg-gray-750 rounded-lg hover:bg-gray-700 transition-colors group">
                        <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-yellow-900/30 text-yellow-400 group-hover:bg-yellow-800/50 transition-colors">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div class="ml-4 text-white">My Evaluations</div>
                    </a>
                    <a href="{{ route('student.evaluations.index') }}" class="flex items-center p-4 bg-gray-750 rounded-lg hover:bg-gray-700 transition-colors group">
                        <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-purple-900/30 text-purple-400 group-hover:bg-purple-800/50 transition-colors">
                            <i class="fas fa-award"></i>
                        </div>
                        <div class="ml-4 text-white">View My Results</div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 