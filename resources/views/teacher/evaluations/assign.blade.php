@extends('layouts.app')

@section('title', 'Assign Evaluations')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-3xl font-bold text-gray-900">Assign Evaluations</h1>
            <div class="mt-3 sm:mt-0 space-x-2">
                <a href="{{ route('teacher.evaluations.random') }}" class="inline-flex items-center px-4 py-2 border border-blue-300 rounded-md shadow-sm text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-random mr-2"></i> Random Assignment
                </a>
                <a href="{{ route('teacher.evaluations.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Evaluations
                </a>
            </div>
        </div>
        <p class="mt-2 text-gray-600">Manually assign evaluations to specific students.</p>
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
            @if(isset($submissions) && count($submissions) > 0)
                <form action="{{ route('teacher.evaluations.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="brief_id" value="{{ $brief->id }}">
                    
                    <div class="mb-6">
                        <h2 class="text-lg font-medium text-gray-900">Brief: {{ $brief->title }}</h2>
                        <p class="text-sm text-gray-600 mt-1">{{ $brief->submissions->count() }} submissions available for evaluation.</p>
                    </div>
                    
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Due Date (Optional)</label>
                        <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}" class="mt-1 block w-64 border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        @error('due_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Evaluations will be due by this date. If not specified, no due date will be set.</p>
                    </div>
                    
                    <div class="mt-6">
                        <h3 class="text-md font-medium text-gray-700 mb-3">Assign Evaluators</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submission</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Evaluators</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assign New Evaluator</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($submissions as $index => $submission)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">Submission #{{ $submission->id }}</div>
                                                <div class="text-sm text-gray-500">{{ $submission->created_at->format('M d, Y') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $submission->student->username }}</div>
                                                <div class="text-sm text-gray-500">{{ $submission->student->first_name }} {{ $submission->student->last_name }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($submission->evaluations->count() > 0)
                                                    <ul class="text-sm text-gray-500 list-disc list-inside">
                                                        @foreach($submission->evaluations as $evaluation)
                                                            <li>{{ $evaluation->evaluator->username }} ({{ $evaluation->status }})</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-sm text-gray-500">No evaluators assigned</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <select name="assignments[{{ $index }}][evaluator_id]" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                                    <option value="">-- Select evaluator --</option>
                                                    @foreach($students as $student)
                                                        @if($student->id !== $submission->student_id)
                                                            <option value="{{ $student->id }}">
                                                                {{ $student->username }} ({{ $student->first_name }} {{ $student->last_name }})
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="assignments[{{ $index }}][submission_id]" value="{{ $submission->id }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-user-check mr-2"></i> Assign Evaluations
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
                            <h3 class="text-sm font-medium text-yellow-800">No Submissions Found</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>There are no submissions available for assigning evaluations.</p>
                                <p class="mt-1">Please wait for students to submit work for your briefs.</p>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('teacher.briefs.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-list mr-2"></i> View Briefs
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