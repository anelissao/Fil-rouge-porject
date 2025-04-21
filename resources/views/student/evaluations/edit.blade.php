@extends('layouts.app')

@section('title', 'Complete Evaluation')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Complete Evaluation</h1>
        <p class="text-gray-600">Evaluate your peer's submission based on the criteria below.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="p-6 bg-blue-50 border-b border-blue-100">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-xl font-semibold text-blue-800">{{ $evaluation->submission->brief->title }}</h2>
                    <p class="text-blue-600 text-sm mt-1">
                        <span class="font-medium">Submitter:</span> {{ $evaluation->submission->user->username }}
                    </p>
                </div>
                <div class="text-right">
                    @if($evaluation->due_date)
                        <p class="text-sm {{ $evaluation->is_overdue ? 'text-red-600 font-bold' : 'text-blue-600' }}">
                            <span class="font-medium">Due:</span> 
                            {{ $evaluation->due_date->format('M d, Y') }}
                            @if($evaluation->is_overdue)
                                <span class="ml-1 bg-red-100 text-red-800 text-xs px-2 py-1 rounded">OVERDUE</span>
                            @endif
                        </p>
                    @endif
                    <p class="text-sm text-blue-600 mt-1">
                        <span class="font-medium">Status:</span>
                        @if($evaluation->status === 'pending')
                            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Not Started</span>
                        @elseif($evaluation->status === 'in_progress')
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">In Progress</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Submission Details -->
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

        <!-- Evaluation Form -->
        <form action="{{ route('student.evaluations.update', $evaluation->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-6 space-y-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Evaluation Criteria</h3>
                
                @if(count($evaluation->submission->brief->criteria) > 0)
                    <div class="space-y-8">
                        @foreach($evaluation->submission->brief->criteria as $criterion)
                            <div class="criterion-card bg-gray-50 p-5 rounded-md border border-gray-200">
                                <div class="mb-3">
                                    <h4 class="text-md font-semibold text-gray-800">{{ $criterion->title }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">{{ $criterion->description }}</p>
                                </div>
                                
                                <div class="flex flex-col sm:flex-row gap-4 mt-4">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Criteria Met?</label>
                                        <div class="flex space-x-4">
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="criteria[{{ $criterion->id }}][valid]" value="1" 
                                                    class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                                    {{ old('criteria.'.$criterion->id.'.valid', isset($existingAnswers[$criterion->id]) && $existingAnswers[$criterion->id]->is_valid ? '1' : '') == '1' ? 'checked' : '' }}>
                                                <span class="ml-2 text-sm text-gray-700">Yes</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="criteria[{{ $criterion->id }}][valid]" value="0" 
                                                    class="h-4 w-4 text-red-600 border-gray-300 focus:ring-red-500"
                                                    {{ old('criteria.'.$criterion->id.'.valid', isset($existingAnswers[$criterion->id]) && $existingAnswers[$criterion->id]->is_valid === false ? '0' : '') == '0' ? 'checked' : '' }}>
                                                <span class="ml-2 text-sm text-gray-700">No</span>
                                            </label>
                                        </div>
                                        @error('criteria.'.$criterion->id.'.valid')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div class="flex-grow">
                                        <label for="criteria-{{ $criterion->id }}-comment" class="block text-sm font-medium text-gray-700 mb-1">Comment</label>
                                        <textarea id="criteria-{{ $criterion->id }}-comment" 
                                            name="criteria[{{ $criterion->id }}][comment]" 
                                            rows="3" 
                                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                            placeholder="Provide feedback on this criterion...">{{ old('criteria.'.$criterion->id.'.comment', isset($existingAnswers[$criterion->id]) ? $existingAnswers[$criterion->id]->comment : '') }}</textarea>
                                        @error('criteria.'.$criterion->id.'.comment')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-8 bg-gray-50 p-5 rounded-md border border-gray-200">
                        <label for="overall_comment" class="block text-lg font-semibold text-gray-800 mb-2">Overall Feedback</label>
                        <p class="text-sm text-gray-600 mb-3">Provide comprehensive feedback that will help your peer improve their work.</p>
                        <textarea id="overall_comment" name="overall_comment" rows="6" 
                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            placeholder="Write your overall feedback here...">{{ old('overall_comment', $evaluation->overall_comment) }}</textarea>
                        @error('overall_comment')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    <div class="bg-yellow-50 p-4 rounded-md border border-yellow-200">
                        <p class="text-yellow-700">No criteria have been specified for this brief.</p>
                    </div>
                @endif
            </div>
            
            <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
                <a href="{{ route('student.evaluations.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Evaluations
                </a>
                <div class="flex space-x-3">
                    <button type="submit" name="save_draft" value="1" class="inline-flex items-center px-4 py-2 border border-blue-300 rounded-md shadow-sm text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i> Save Draft
                    </button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-check mr-2"></i> Submit Evaluation
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    /* Custom styles for the evaluation form */
    .criterion-card {
        transition: all 0.2s ease;
        border-left: 4px solid #E5E7EB;
    }
    
    .criterion-card:hover {
        border-left-color: #1E90FF;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    /* Custom radio button styles */
    input[type="radio"]:checked {
        background-color: #1E90FF;
        border-color: #1E90FF;
    }
    
    input[type="radio"]:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(30, 144, 255, 0.2);
    }
    
    /* Custom textarea styles */
    textarea:focus {
        border-color: #1E90FF;
        box-shadow: 0 0 0 3px rgba(30, 144, 255, 0.2);
    }
</style>
@endsection 