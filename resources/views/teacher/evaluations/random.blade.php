@extends('layouts.app')

@section('title', 'Random Evaluation Assignments')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-3xl font-bold text-gray-900">Random Evaluation Assignments</h1>
            <div class="mt-3 sm:mt-0">
                <a href="{{ route('teacher.evaluations.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Evaluations
                </a>
            </div>
        </div>
        <p class="mt-2 text-gray-600">Automatically assign evaluations randomly among students who have submitted work for a specific brief.</p>
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

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            @if($briefs->count() > 0)
                <form action="{{ route('teacher.evaluations.random') }}" method="POST">
                    @csrf
                    
                    <div class="space-y-6">
                        <div>
                            <label for="brief_id" class="block text-sm font-medium text-gray-700 mb-1">Select Brief</label>
                            <select id="brief_id" name="brief_id" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">-- Select a brief --</option>
                                @foreach($briefs as $brief)
                                    <option value="{{ $brief->id }}" {{ old('brief_id') == $brief->id ? 'selected' : '' }}>
                                        {{ $brief->title }} ({{ $brief->submissions_count }} submissions)
                                    </option>
                                @endforeach
                            </select>
                            @error('brief_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="evaluations_per_submission" class="block text-sm font-medium text-gray-700 mb-1">Evaluations Per Submission</label>
                            <select id="evaluations_per_submission" name="evaluations_per_submission" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ old('evaluations_per_submission', 1) == $i ? 'selected' : '' }}>
                                        {{ $i }} {{ $i === 1 ? 'evaluation' : 'evaluations' }}
                                    </option>
                                @endfor
                            </select>
                            @error('evaluations_per_submission')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">How many students should evaluate each submission.</p>
                        </div>
                        
                        <div>
                            <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Due Date (Optional)</label>
                            <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}" class="mt-1 block w-full border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            @error('due_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Evaluations will be due by this date. If not specified, no due date will be set.</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-md border border-gray-200 mt-6">
                            <h2 class="text-lg font-medium text-gray-900">Random Assignment Rules</h2>
                            <ul class="mt-2 text-sm text-gray-600 list-disc pl-5 space-y-1">
                                <li>Each student will be assigned to evaluate other students' submissions, not their own.</li>
                                <li>If there are not enough students to fulfill the requested number of evaluations per submission, the maximum possible will be assigned.</li>
                                <li>Students who already have evaluations assigned for a submission will not be reassigned.</li>
                                <li>Students will receive notifications about their new evaluation assignments.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-random mr-2"></i> Assign Randomly
                        </button>
                    </div>
                </form>
            @else
                <div class="bg-yellow-50 p-4 rounded-md border border-yellow-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">No Eligible Briefs Found</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>There are no briefs with submissions available for random evaluation assignment.</p>
                                <p class="mt-1">Please create a brief and wait for students to submit work before assigning evaluations.</p>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('teacher.briefs.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-plus mr-2"></i> Create Brief
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 