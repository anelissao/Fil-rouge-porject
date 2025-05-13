@extends('layouts.app')

@section('title', 'View Evaluation')

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
                                <a href="{{ route('student.evaluations.index') }}" class="text-blue-200 hover:text-white transition-colors">
                                    My Evaluations
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-blue-200 mx-2 text-xs"></i>
                                <span class="text-white">View Evaluation</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-bold text-white mb-2">Evaluation Details</h1>
                <p class="text-blue-100">
                    @if($evaluation->evaluator_id == auth()->id())
                        Your evaluation of {{ $evaluation->submission->student->username }}'s submission
                    @else
                        Evaluation by {{ $evaluation->evaluator->username }} of your submission
                    @endif
                </p>
            </div>
            <div>
                <a href="{{ route('student.evaluations.index') }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg backdrop-blur-sm transition-all duration-300 border border-white/20">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Evaluations
                </a>
            </div>
        </div>
    </div>

    <!-- Submission Info -->
    <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700 mb-8">
        <div class="border-b border-gray-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Submission Details</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-white mb-2">Brief</h3>
                    <p class="text-gray-300 mb-4">{{ $evaluation->submission->brief->title }}</p>

                    <h3 class="text-lg font-medium text-white mb-2">Submitted By</h3>
                    <p class="text-gray-300 mb-4">{{ $evaluation->submission->student->username }}</p>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-white mb-2">Submission Date</h3>
                    <p class="text-gray-300 mb-4">{{ $evaluation->submission->submission_date->format('M d, Y') }}</p>

                    <h3 class="text-lg font-medium text-white mb-2">Evaluation Status</h3>
                    <p class="text-gray-300 mb-4">
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center
                        {{ $evaluation->status === 'completed' ? 'bg-green-900/30 text-green-400' : 'bg-yellow-900/30 text-yellow-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 
                            {{ $evaluation->status === 'completed' ? 'bg-green-400' : 'bg-yellow-400' }}"></span>
                            {{ ucfirst($evaluation->status) }}
                        </span>
                        @if($evaluation->completed_at)
                            <span class="text-sm text-gray-400 ml-2">(Completed {{ $evaluation->completed_at->format('M d, Y') }})</span>
                        @endif
                    </p>
                </div>
            </div>

            @if($evaluation->submission->content)
                <div class="mt-6">
                    <h3 class="text-lg font-medium text-white mb-2">Submission Content</h3>
                    <div class="bg-gray-750 p-4 rounded-lg border border-gray-700">
                        <div class="text-gray-300 whitespace-pre-line">
                            {!! nl2br(e($evaluation->submission->content)) !!}
                        </div>
                    </div>
                </div>
            @endif

            @if($evaluation->submission->file_path)
                <div class="mt-6">
                    <h3 class="text-lg font-medium text-white mb-2">Attached File</h3>
                    <a href="{{ route('student.submissions.download', $evaluation->submission->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-download mr-2"></i> Download Submission File
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Evaluation Results -->
    <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700 mb-8">
        <div class="border-b border-gray-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Evaluation Results</h2>
        </div>
        <div class="p-6">
            <!-- Criteria Evaluation Results -->
            <h3 class="text-lg font-medium text-white mb-4">Criteria Evaluation</h3>
            
            @if($evaluation->answers->count() > 0)
                @foreach($evaluation->answers as $answer)
                    @if($answer->task && $answer->task->criteria)
                        <div class="criterion-card bg-gray-750 p-5 rounded-lg border border-gray-700 mb-6 
                            {{ $answer->response ? 'border-green-500/50' : 'border-red-500/50' }}">
                            <!-- Criterion Title and Description -->
                            <div class="flex justify-between items-start">
                                <h4 class="text-md font-semibold text-white">{{ $answer->task->criteria->title }}</h4>
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center
                                {{ $answer->response ? 'bg-green-900/30 text-green-400' : 'bg-red-900/30 text-red-400' }}">
                                    @if($answer->response)
                                        <i class="fas fa-check mr-1"></i> Criteria Met
                                    @else
                                        <i class="fas fa-times mr-1"></i> Criteria Not Met
                                    @endif
                                </span>
                            </div>
                            <p class="text-sm text-gray-400 mt-1">{{ $answer->task->criteria->description }}</p>

                            <!-- Comment Field -->
                            @if($answer->comment)
                                <div class="mt-4 pt-4 border-t border-gray-700">
                                    <h5 class="text-sm font-medium text-white mb-2">Comment:</h5>
                                    <div class="text-gray-300 text-sm">
                                        {!! nl2br(e($answer->comment)) !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            @else
                <div class="bg-gray-750 p-4 rounded-lg border border-gray-700 text-gray-400">
                    No criteria have been evaluated yet.
                </div>
            @endif

            <!-- Overall Feedback -->
            <div class="mt-8">
                <h3 class="text-lg font-medium text-white mb-2">Overall Feedback</h3>
                @if($feedback)
                    <div class="bg-gray-750 p-5 rounded-lg border border-gray-700">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span class="text-sm text-gray-400">Provided by: {{ $evaluation->evaluator->username }}</span>
                            </div>
                            @if($feedback->rating)
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-white mr-2">Rating:</span>
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $feedback->rating ? 'text-yellow-400' : 'text-gray-600' }} mr-1"></i>
                                        @endfor
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="text-gray-300">
                            {!! nl2br(e($feedback->content)) !!}
                        </div>
                    </div>
                @else
                    <div class="bg-gray-750 p-4 rounded-lg border border-gray-700 text-gray-400">
                        No overall feedback has been provided.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .criterion-card {
        transition: border-color 0.2s ease-in-out;
    }
</style>
@endsection 