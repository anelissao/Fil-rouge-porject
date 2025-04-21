@extends('layouts.app')

@section('title', 'Submission Details')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <nav class="mb-5">
            <ol class="list-reset flex text-gray-500">
                <li><a href="{{ route('student.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a></li>
                <li><span class="mx-2">/</span></li>
                <li><a href="{{ route('student.submissions.index') }}" class="text-blue-600 hover:text-blue-800">My Submissions</a></li>
                <li><span class="mx-2">/</span></li>
                <li class="text-gray-700">Submission Details</li>
            </ol>
        </nav>

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Submission Details</h1>
            <div>
                <a href="{{ route('student.submissions.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Submissions
                </a>
            </div>
        </div>

        <!-- Submission Information -->
        <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-xl font-semibold text-gray-800">Submission Information</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Brief</h3>
                        <p class="text-gray-700 mb-4">{{ $submission->brief->title }}</p>

                        <h3 class="text-lg font-medium text-gray-900 mb-2">Submitted On</h3>
                        <p class="text-gray-700 mb-4">{{ $submission->submission_date->format('M d, Y - h:i A') }}</p>

                        <h3 class="text-lg font-medium text-gray-900 mb-2">Status</h3>
                        <div class="mb-4">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $submission->completed_evaluations == $submission->total_evaluations && $submission->total_evaluations > 0 
                                    ? 'bg-green-100 text-green-800' 
                                    : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $submission->completed_evaluations == $submission->total_evaluations && $submission->total_evaluations > 0 
                                    ? 'Evaluated' 
                                    : ($submission->total_evaluations > 0 
                                        ? 'Evaluation in Progress' 
                                        : 'Pending Evaluation') }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Teacher</h3>
                        <p class="text-gray-700 mb-4">{{ $submission->brief->teacher->username }}</p>

                        <h3 class="text-lg font-medium text-gray-900 mb-2">Evaluations</h3>
                        <p class="text-gray-700 mb-4">{{ $submission->completed_evaluations }}/{{ $submission->total_evaluations }} completed</p>

                        @if($submission->file_path)
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Attached File</h3>
                            <a href="{{ route('student.submissions.download', $submission->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                <i class="fas fa-download mr-2"></i> Download File
                            </a>
                        @endif
                    </div>
                </div>

                @if($submission->content)
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Submission Content</h3>
                        <div class="bg-gray-50 p-4 rounded border border-gray-200">
                            {!! nl2br(e($submission->content)) !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Evaluations -->
        <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-xl font-semibold text-gray-800">Evaluations</h2>
            </div>
            <div class="p-6">
                @if(count($evaluations) > 0)
                    @foreach($evaluations as $evaluation)
                        <div class="mb-6 p-4 border border-gray-200 rounded-lg 
                            {{ $evaluation->status == 'completed' ? 'bg-green-50' : 'bg-yellow-50' }}">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">
                                        Evaluation by {{ $evaluation->evaluator->username }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        {{ $evaluation->status == 'completed' 
                                            ? 'Completed on ' . $evaluation->completed_at->format('M d, Y') 
                                            : 'Not yet completed' }}
                                    </p>
                                </div>
                                <div>
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $evaluation->status == 'completed' 
                                            ? 'bg-green-100 text-green-800' 
                                            : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($evaluation->status) }}
                                    </span>
                                </div>
                            </div>

                            @if($evaluation->status == 'completed' && $evaluation->answers->count() > 0)
                                <div class="mt-4">
                                    @foreach($evaluation->answers as $answer)
                                        <div class="mb-4 p-3 bg-white rounded border border-gray-200">
                                            <h4 class="text-md font-semibold text-gray-800">{{ $answer->criterion->title }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ $answer->criterion->description }}</p>
                                            
                                            <div class="mt-2 flex items-center">
                                                <span class="text-{{ $answer->is_valid ? 'green' : 'red' }}-600 font-medium">
                                                    {{ $answer->is_valid ? 'Validated' : 'Not Validated' }}
                                                </span>
                                            </div>
                                            
                                            @if($answer->comment)
                                                <div class="mt-2 text-sm bg-gray-50 p-2 rounded">
                                                    <p class="font-medium text-gray-700">Comment:</p>
                                                    <p class="text-gray-600">{{ $answer->comment }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if($evaluation->feedback)
                                <div class="mt-4 p-3 bg-white rounded border border-gray-200">
                                    <h4 class="text-md font-semibold text-gray-800">Overall Feedback</h4>
                                    <div class="mt-2 text-gray-700">
                                        {!! nl2br(e($evaluation->feedback)) !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-clipboard-check text-gray-400 text-5xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">No evaluations yet</h3>
                        <p class="text-gray-500">Your submission is waiting to be evaluated.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection 