@extends('layouts.app')

@section('title', 'Evaluation Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-3xl font-bold text-gray-900">Evaluation Details</h1>
            
            @if(Auth::user()->hasRole('teacher'))
                <div class="mt-3 sm:mt-0">
                    <a href="{{ route('teacher.evaluations.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Evaluations
                    </a>
                </div>
            @else
                <div class="mt-3 sm:mt-0">
                    <a href="{{ route('student.evaluations.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Evaluations
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Evaluation Overview Card -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="p-6 bg-blue-50 border-b border-blue-100">
            <div class="flex flex-col md:flex-row md:justify-between md:items-start">
                <div>
                    <h2 class="text-xl font-semibold text-blue-800">{{ $evaluation->submission->brief->title }}</h2>
                    <p class="text-blue-600 text-sm mt-1">
                        <span class="font-medium">Submission by:</span> {{ $evaluation->submission->user->username }}
                    </p>
                    <p class="text-blue-600 text-sm mt-1">
                        <span class="font-medium">Evaluated by:</span> {{ $evaluation->evaluator->username }}
                    </p>
                </div>
                <div class="mt-4 md:mt-0 md:text-right">
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($evaluation->status === 'completed') bg-green-100 text-green-800
                        @elseif($evaluation->status === 'in_progress') bg-blue-100 text-blue-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        <span class="mr-1 flex-shrink-0 h-2 w-2 rounded-full
                            @if($evaluation->status === 'completed') bg-green-600
                            @elseif($evaluation->status === 'in_progress') bg-blue-600
                            @else bg-yellow-600 @endif"></span>
                        {{ ucfirst(str_replace('_', ' ', $evaluation->status)) }}
                    </div>
                    
                    @if($evaluation->completed_at)
                        <p class="text-sm text-blue-600 mt-2">
                            <span class="font-medium">Completed:</span> 
                            {{ $evaluation->completed_at->format('M d, Y \a\t H:i') }}
                        </p>
                    @endif
                    
                    @if($evaluation->due_date)
                        <p class="text-sm {{ $evaluation->is_overdue && $evaluation->status !== 'completed' ? 'text-red-600 font-medium' : 'text-blue-600' }} mt-1">
                            <span class="font-medium">Due:</span> 
                            {{ $evaluation->due_date->format('M d, Y') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Submission Preview -->
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Submission</h3>
            
            @if($evaluation->submission->content)
                <div class="bg-gray-50 p-4 rounded-md mb-4">
                    <div class="prose max-w-none">
                        {{ $evaluation->submission->content }}
                    </div>
                </div>
            @endif
            
            @if($evaluation->submission->file_path)
                <div class="mt-4">
                    <a href="{{ route('student.submissions.download', $evaluation->submission->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-download mr-2"></i> Download Attachment
                    </a>
                </div>
            @endif
        </div>
        
        <!-- Evaluation Results -->
        @if($evaluation->status === 'completed')
            <div class="p-6 space-y-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Evaluation Results</h3>
                
                <!-- Criteria Results -->
                @if(count($evaluation->criteriaAnswers) > 0)
                    <div class="space-y-6">
                        @foreach($evaluation->criteriaAnswers as $answer)
                            <div class="bg-gray-50 p-5 rounded-md border border-gray-200 
                                {{ $answer->is_valid ? 'border-l-4 border-l-green-500' : 'border-l-4 border-l-red-500' }}">
                                <div class="flex flex-col md:flex-row md:justify-between md:items-start">
                                    <div>
                                        <h4 class="text-md font-semibold text-gray-800">{{ $answer->criterion->title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ $answer->criterion->description }}</p>
                                    </div>
                                    <div class="mt-2 md:mt-0">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                            {{ $answer->is_valid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $answer->is_valid ? 'Criteria Met' : 'Criteria Not Met' }}
                                        </span>
                                    </div>
                                </div>
                                
                                @if($answer->comment)
                                    <div class="mt-4 bg-white p-4 rounded-md border border-gray-100">
                                        <p class="text-sm text-gray-900">{{ $answer->comment }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-yellow-50 p-4 rounded-md border border-yellow-200">
                        <p class="text-yellow-700">No criteria were evaluated.</p>
                    </div>
                @endif
                
                <!-- Overall Feedback -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Overall Feedback</h3>
                    
                    @if($evaluation->overall_comment)
                        <div class="bg-gray-50 p-5 rounded-md border border-gray-200">
                            <div class="prose max-w-none text-gray-800">
                                {{ $evaluation->overall_comment }}
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                            <p class="text-gray-500 italic">No overall feedback provided.</p>
                        </div>
                    @endif
                </div>
                
                <!-- Evaluation Summary -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Evaluation Summary</h3>
                    
                    <div class="bg-gray-50 p-5 rounded-md border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Criteria Met</h4>
                                <div class="flex items-center">
                                    <div class="flex-1 mr-4">
                                        <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                                            <div style="width:{{ $evaluation->percentage_valid }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500"></div>
                                        </div>
                                    </div>
                                    <span class="text-lg font-semibold text-gray-800">{{ $evaluation->percentage_valid }}%</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">
                                    {{ $evaluation->valid_count }} of {{ $evaluation->total_criteria }} criteria met
                                </p>
                            </div>
                            
                            <div class="border-t md:border-t-0 md:border-l border-gray-200 pt-6 md:pt-0 md:pl-6">
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Evaluation Quality</h4>
                                @php
                                    $commentedCriteria = $evaluation->commented_criteria_count;
                                    $totalCriteria = $evaluation->total_criteria;
                                    $commentPercentage = $totalCriteria > 0 ? round(($commentedCriteria / $totalCriteria) * 100) : 0;
                                @endphp
                                
                                <div class="flex items-center">
                                    <div class="flex-1 mr-4">
                                        <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                                            <div style="width:{{ $commentPercentage }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500"></div>
                                        </div>
                                    </div>
                                    <span class="text-lg font-semibold text-gray-800">{{ $commentPercentage }}%</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">
                                    {{ $commentedCriteria }} of {{ $totalCriteria }} criteria received comments
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="p-6">
                <div class="bg-yellow-50 p-4 rounded-md border border-yellow-200">
                    <p class="text-yellow-700">
                        This evaluation has not been completed yet.
                        @if($evaluation->evaluator_id === Auth::id())
                            <a href="{{ route('student.evaluations.edit', $evaluation->id) }}" class="text-blue-600 hover:underline ml-2">
                                Complete now
                            </a>
                        @endif
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection 