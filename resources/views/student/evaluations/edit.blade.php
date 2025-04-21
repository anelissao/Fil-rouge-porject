@extends('layouts.app')

@section('title', 'Complete Evaluation')

@section('content')
<div class="container mx-auto px-4 py-6">
    <nav class="mb-5">
        <ol class="list-reset flex text-gray-500">
            <li><a href="{{ route('student.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="{{ route('student.evaluations.index') }}" class="text-blue-600 hover:text-blue-800">My Evaluations</a></li>
            <li><span class="mx-2">/</span></li>
            <li class="text-gray-700">Complete Evaluation</li>
        </ol>
    </nav>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Complete Evaluation</h1>
        <a href="{{ route('student.evaluations.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
            <i class="fas fa-arrow-left mr-2"></i> Back to Evaluations
        </a>
    </div>

    <!-- Submission Info -->
    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-xl font-semibold text-gray-800">Submission Details</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Brief</h3>
                    <p class="text-gray-700 mb-4">{{ $evaluation->submission->brief->title }}</p>

                    <h3 class="text-lg font-medium text-gray-900 mb-2">Submitted By</h3>
                    <p class="text-gray-700 mb-4">{{ $evaluation->submission->student->username }}</p>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Submission Date</h3>
                    <p class="text-gray-700 mb-4">{{ $evaluation->submission->submission_date->format('M d, Y') }}</p>

                    <h3 class="text-lg font-medium text-gray-900 mb-2">Due Date</h3>
                    <p class="text-gray-700 mb-4 {{ $evaluation->due_at && $evaluation->due_at->isPast() ? 'text-red-600 font-semibold' : '' }}">
                        {{ $evaluation->due_at ? $evaluation->due_at->format('M d, Y') : 'No deadline set' }}
                        @if($evaluation->due_at && $evaluation->due_at->isPast())
                            <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Overdue</span>
                        @elseif($evaluation->due_at)
                            <span class="text-sm text-gray-500">({{ $evaluation->due_at->diffForHumans() }})</span>
                        @endif
                    </p>
                </div>
            </div>

            @if($evaluation->submission->content)
                <div class="mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Submission Content</h3>
                    <div class="bg-gray-50 p-4 rounded border border-gray-200">
                        {!! nl2br(e($evaluation->submission->content)) !!}
                    </div>
                </div>
            @endif

            @if($evaluation->submission->file_path)
                <div class="mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Attached File</h3>
                    <a href="{{ route('student.submissions.download', $evaluation->submission->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        <i class="fas fa-download mr-2"></i> Download Submission File
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Evaluation Form -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-xl font-semibold text-gray-800">Evaluation Form</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('student.evaluations.update', $evaluation->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Criteria Evaluation -->
                <h3 class="text-lg font-medium text-gray-900 mb-4">Evaluation Criteria</h3>
                <p class="text-gray-600 mb-6">Please evaluate the submission against each of the following criteria:</p>

                @foreach($evaluation->submission->brief->criteria as $criterion)
                    <div class="criterion-card bg-gray-50 p-5 rounded-md border border-gray-200 mb-6 hover:border-indigo-300 transition-colors">
                        <!-- Criterion Title and Description -->
                        <h4 class="text-md font-semibold text-gray-800">{{ $criterion->title }}</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ $criterion->description }}</p>

                        <!-- Validation Toggle -->
                        <div class="mt-4 flex items-center space-x-4">
                            <span class="text-sm font-medium text-gray-700">Is this criterion met?</span>
                            <div class="flex items-center space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="criteria[{{ $criterion->id }}][valid]" value="1" 
                                        class="form-radio h-5 w-5 text-indigo-600"
                                        {{ old('criteria.'.$criterion->id.'.valid', isset($existingAnswers[$criterion->id]) && $existingAnswers[$criterion->id]->is_valid ? '1' : '') == '1' ? 'checked' : '' }}>
                                    <span class="ml-2 text-gray-700">Yes</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="criteria[{{ $criterion->id }}][valid]" value="0" 
                                        class="form-radio h-5 w-5 text-red-600"
                                        {{ old('criteria.'.$criterion->id.'.valid', isset($existingAnswers[$criterion->id]) && $existingAnswers[$criterion->id]->is_valid === false ? '0' : '') == '0' ? 'checked' : '' }}>
                                    <span class="ml-2 text-gray-700">No</span>
                                </label>
                            </div>
                        </div>
                        @error('criteria.'.$criterion->id.'.valid')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <!-- Comment Field -->
                        <div class="mt-4">
                            <label for="criteria-{{ $criterion->id }}-comment" class="block text-sm font-medium text-gray-700 mb-1">Comment</label>
                            <textarea id="criteria-{{ $criterion->id }}-comment" 
                                name="criteria[{{ $criterion->id }}][comment]" 
                                rows="3" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Provide feedback on this criterion...">{{ old('criteria.'.$criterion->id.'.comment', isset($existingAnswers[$criterion->id]) ? $existingAnswers[$criterion->id]->comment : '') }}</textarea>
                            @error('criteria.'.$criterion->id.'.comment')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endforeach

                <!-- Overall Feedback -->
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Overall Feedback</h3>
                    <p class="text-sm text-gray-600 mb-4">Provide some general feedback for the student:</p>
                    <textarea name="overall_feedback" rows="5" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Provide feedback on the submission overall...">{{ old('overall_feedback', $evaluation->feedback ? $evaluation->feedback->content : '') }}</textarea>
                    @error('overall_feedback')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit buttons -->
                <div class="mt-8 flex justify-end space-x-4">
                    <button type="submit" name="is_complete" value="0" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                        Save Draft
                    </button>
                    <button type="submit" name="is_complete" value="1" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        Complete Evaluation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .criterion-card {
        transition: border-color 0.2s ease-in-out;
    }
    
    .criterion-card:hover {
        border-color: #4f46e5;
    }
</style>
@endsection 