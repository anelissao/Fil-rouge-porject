@extends('layouts.app')

@section('title', 'Submission Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section with Gradient Background -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-lg mb-8 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div class="mb-4 md:mb-0">
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('home') }}" class="text-blue-200 hover:text-white transition-colors">
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-blue-200 mx-2 text-xs"></i>
                                <a href="{{ route('student.submissions.index') }}" class="text-blue-200 hover:text-white transition-colors">
                                    My Submissions
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-blue-200 mx-2 text-xs"></i>
                                <span class="text-white">Submission Details</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-bold text-white mb-2">Submission Details</h1>
                <p class="text-blue-100">View your submission and evaluations</p>
            </div>
            <div>
                <a href="{{ route('student.submissions.index') }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg backdrop-blur-sm transition-all duration-300 border border-white/20">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Submissions
                </a>
            </div>
        </div>
    </div>

    <!-- Submission Information -->
    <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700 mb-8">
        <div class="border-b border-gray-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Submission Information</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-white mb-2">Brief</h3>
                    <p class="text-gray-300 mb-4">{{ $submission->brief->title }}</p>

                    <h3 class="text-lg font-medium text-white mb-2">Submitted On</h3>
                    <p class="text-gray-300 mb-4">{{ $submission->submission_date->format('M d, Y - h:i A') }}</p>

                    <h3 class="text-lg font-medium text-white mb-2">Status</h3>
                    <div class="mb-4">
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center
                            {{ $submission->completed_evaluations == $submission->total_evaluations && $submission->total_evaluations > 0 
                                ? 'bg-green-900/30 text-green-400' 
                                : 'bg-yellow-900/30 text-yellow-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 
                                {{ $submission->completed_evaluations == $submission->total_evaluations && $submission->total_evaluations > 0 
                                    ? 'bg-green-400' 
                                    : 'bg-yellow-400' }}"></span>
                            {{ $submission->completed_evaluations == $submission->total_evaluations && $submission->total_evaluations > 0 
                                ? 'Evaluated' 
                                : ($submission->total_evaluations > 0 
                                    ? 'Evaluation in Progress' 
                                    : 'Pending Evaluation') }}
                        </span>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-white mb-2">Teacher</h3>
                    <p class="text-gray-300 mb-4">{{ $submission->brief->teacher->username }}</p>

                    <h3 class="text-lg font-medium text-white mb-2">Evaluations</h3>
                    <div class="mb-4">
                        <div class="flex items-center">
                            <div class="text-gray-300">
                                {{ $submission->completed_evaluations }} / {{ $submission->total_evaluations }} completed
                            </div>
                            @if($submission->completed_evaluations > 0)
                                <div class="ml-2 text-xs px-2 py-0.5 bg-blue-900/30 text-blue-400 rounded-full">
                                    {{ round(($submission->completed_evaluations / max(1, $submission->total_evaluations)) * 100) }}%
                                </div>
                            @endif
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-1.5 mt-2">
                            <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ ($submission->completed_evaluations / max(1, $submission->total_evaluations)) * 100 }}%"></div>
                        </div>
                    </div>

                    @if($submission->file_path)
                        <h3 class="text-lg font-medium text-white mb-2">Attached File</h3>
                        <a href="{{ route('student.submissions.download', $submission->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-download mr-2"></i> Download File
                        </a>
                    @endif
                </div>
            </div>

            @if($submission->content)
                <div class="mt-6">
                    <h3 class="text-lg font-medium text-white mb-2">Submission Content</h3>
                    <div class="bg-gray-750 p-4 rounded-lg border border-gray-700">
                        <div class="text-gray-300 whitespace-pre-line">
                            {!! nl2br(e($submission->content)) !!}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Evaluations -->
    <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700 mb-8">
        <div class="border-b border-gray-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Evaluations</h2>
        </div>
        <div class="p-6">
            @if(count($evaluations) > 0)
                <div class="space-y-6">
                    @foreach($evaluations as $evaluation)
                        <div class="p-4 border rounded-lg 
                            {{ $evaluation->status == 'completed' ? 'border-green-500/30 bg-green-900/10' : 'border-yellow-500/30 bg-yellow-900/10' }}">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                                <div>
                                    <h3 class="text-lg font-medium text-white">
                                        Evaluation by {{ $evaluation->evaluator->username }}
                                    </h3>
                                    <p class="text-sm text-gray-400">
                                        {{ $evaluation->status == 'completed' 
                                            ? 'Completed on ' . $evaluation->completed_at->format('M d, Y') 
                                            : 'Not yet completed' }}
                                    </p>
                                </div>
                                <div class="mt-2 sm:mt-0">
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center
                                        {{ $evaluation->status == 'completed' 
                                            ? 'bg-green-900/30 text-green-400' 
                                            : 'bg-yellow-900/30 text-yellow-400' }}">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 
                                            {{ $evaluation->status == 'completed' 
                                                ? 'bg-green-400' 
                                                : 'bg-yellow-400' }}"></span>
                                        {{ ucfirst($evaluation->status) }}
                                    </span>
                                </div>
                            </div>

                            @if($evaluation->status == 'completed' && $evaluation->answers->count() > 0)
                                <div class="mt-4 space-y-4">
                                    @foreach($evaluation->answers as $answer)
                                        <div class="p-3 bg-gray-750 rounded-lg border border-gray-700">
                                            <h4 class="text-md font-semibold text-white">{{ $answer->criterion->title }}</h4>
                                            <p class="text-sm text-gray-400 mt-1">{{ $answer->criterion->description }}</p>
                                            
                                            <div class="mt-2 flex items-center">
                                                <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center
                                                    {{ $answer->is_valid 
                                                        ? 'bg-green-900/30 text-green-400' 
                                                        : 'bg-red-900/30 text-red-400' }}">
                                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 
                                                        {{ $answer->is_valid 
                                                            ? 'bg-green-400' 
                                                            : 'bg-red-400' }}"></span>
                                                    {{ $answer->is_valid ? 'Validated' : 'Not Validated' }}
                                                </span>
                                            </div>
                                            
                                            @if($answer->comment)
                                                <div class="mt-3 text-sm bg-gray-800 p-3 rounded-lg">
                                                    <p class="font-medium text-white mb-1">Comment:</p>
                                                    <p class="text-gray-300">{{ $answer->comment }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if($evaluation->feedback)
                                <div class="mt-4 p-3 bg-gray-750 rounded-lg border border-gray-700">
                                    <h4 class="text-md font-semibold text-white">Overall Feedback</h4>
                                    <div class="mt-2 text-gray-300 whitespace-pre-line">
                                        {!! nl2br(e($evaluation->feedback)) !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-16 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-700 text-gray-500 mb-4">
                        <i class="fas fa-clipboard-check text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-medium text-white mb-2">No evaluations yet</h3>
                    <p class="text-gray-400 max-w-md mx-auto">Your submission is waiting to be evaluated.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 