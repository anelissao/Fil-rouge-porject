@extends('layouts.app')

@section('title', 'Complete Evaluation')

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
                                <span class="text-white">Complete Evaluation</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-bold text-white mb-2">Complete Evaluation</h1>
                <p class="text-blue-100">Evaluate your peer's submission</p>
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

                    <h3 class="text-lg font-medium text-white mb-2">Due Date</h3>
                    <p class="text-gray-300 mb-4 {{ $evaluation->due_at && $evaluation->due_at->isPast() ? 'text-red-400 font-semibold' : '' }}">
                        {{ $evaluation->due_at ? $evaluation->due_at->format('M d, Y') : 'No deadline set' }}
                        @if($evaluation->due_at && $evaluation->due_at->isPast())
                            <span class="ml-2 px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center bg-red-900/30 text-red-400">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-red-400"></span>
                                Overdue
                            </span>
                        @elseif($evaluation->due_at)
                            <span class="text-sm text-gray-400">({{ $evaluation->due_at->diffForHumans() }})</span>
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

    <!-- Evaluation Form -->
    <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700">
        <div class="border-b border-gray-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Evaluation Form</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('student.evaluations.update', $evaluation->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Criteria Evaluation -->
                <h3 class="text-lg font-medium text-white mb-4">Evaluation Criteria</h3>
                <p class="text-gray-400 mb-6">Please evaluate the submission against each of the following criteria:</p>

                @foreach($evaluation->submission->brief->criteria as $criterion)
                    <div class="criterion-card bg-gray-750 p-5 rounded-lg border border-gray-700 mb-6 hover:border-blue-500 transition-colors">
                        <!-- Criterion Title and Description -->
                        <h4 class="text-md font-semibold text-white">{{ $criterion->title }}</h4>
                        <p class="text-sm text-gray-400 mt-1">{{ $criterion->description }}</p>

                        <!-- Validation Toggle -->
                        <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:space-x-4">
                            <span class="text-sm font-medium text-white mb-2 sm:mb-0">Is this criterion met?</span>
                            <div class="flex items-center space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="criteria[{{ $criterion->id }}][valid]" value="1" 
                                        class="form-radio h-5 w-5 text-blue-600 focus:ring-blue-500 focus:ring-offset-gray-800"
                                        {{ old('criteria.'.$criterion->id.'.valid', isset($existingAnswers[$criterion->id]) && $existingAnswers[$criterion->id]->is_valid ? '1' : '') == '1' ? 'checked' : '' }}>
                                    <span class="ml-2 text-white">Yes</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="criteria[{{ $criterion->id }}][valid]" value="0" 
                                        class="form-radio h-5 w-5 text-red-600 focus:ring-red-500 focus:ring-offset-gray-800"
                                        {{ old('criteria.'.$criterion->id.'.valid', isset($existingAnswers[$criterion->id]) && $existingAnswers[$criterion->id]->is_valid === false ? '0' : '') == '0' ? 'checked' : '' }}>
                                    <span class="ml-2 text-white">No</span>
                                </label>
                            </div>
                        </div>
                        @error('criteria.'.$criterion->id.'.valid')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror

                        <!-- Comment Field -->
                        <div class="mt-4">
                            <label for="criteria-{{ $criterion->id }}-comment" class="block text-sm font-medium text-white mb-1">Comment</label>
                            <textarea id="criteria-{{ $criterion->id }}-comment" 
                                name="criteria[{{ $criterion->id }}][comment]" 
                                rows="3" 
                                class="block w-full rounded-lg bg-gray-700 border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-white placeholder-gray-400"
                                placeholder="Provide feedback on this criterion...">{{ old('criteria.'.$criterion->id.'.comment', isset($existingAnswers[$criterion->id]) ? $existingAnswers[$criterion->id]->comment : '') }}</textarea>
                            @error('criteria.'.$criterion->id.'.comment')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endforeach

                <!-- Overall Feedback -->
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-white mb-2">Overall Feedback</h3>
                    <p class="text-sm text-gray-400 mb-4">Provide some general feedback for the student:</p>
                    <textarea name="overall_feedback" rows="5" class="block w-full rounded-lg bg-gray-700 border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-white placeholder-gray-400" placeholder="Provide feedback on the submission overall...">{{ old('overall_feedback', $evaluation->feedback ? $evaluation->feedback->content : '') }}</textarea>
                    @error('overall_feedback')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit buttons -->
                <div class="mt-8 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
                    <button type="submit" name="is_complete" value="0" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="far fa-save mr-2"></i>Save Draft
                    </button>
                    <button type="submit" name="is_complete" value="1" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-check-circle mr-2"></i>Complete Evaluation
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