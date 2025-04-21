@extends('layouts.app')

@section('title', 'Evaluation Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Evaluation Details</h1>
                <p class="mt-2 text-gray-600">
                    Evaluation of {{ $evaluation->submission->student->username }}'s submission by {{ $evaluation->evaluator->username }}
                </p>
            </div>
            <div class="mt-3 sm:mt-0">
                <a href="{{ route('teacher.evaluations.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Evaluations
                </a>
            </div>
        </div>
    </div>

    <!-- Status Banner -->
    <div class="mb-6 p-4 rounded-md {{ $evaluation->status == 'completed' ? 'bg-green-50 border border-green-200' : ($evaluation->status == 'in_progress' ? 'bg-yellow-50 border border-yellow-200' : 'bg-gray-50 border border-gray-200') }}">
        <div class="flex">
            <div class="flex-shrink-0">
                @if($evaluation->status == 'completed')
                    <i class="fas fa-check-circle text-green-500"></i>
                @elseif($evaluation->status == 'in_progress')
                    <i class="fas fa-clock text-yellow-500"></i>
                @else
                    <i class="fas fa-info-circle text-gray-500"></i>
                @endif
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium {{ $evaluation->status == 'completed' ? 'text-green-800' : ($evaluation->status == 'in_progress' ? 'text-yellow-800' : 'text-gray-800') }}">
                    Evaluation Status: {{ ucfirst($evaluation->status) }}
                </h3>
                <div class="mt-2 text-sm {{ $evaluation->status == 'completed' ? 'text-green-700' : ($evaluation->status == 'in_progress' ? 'text-yellow-700' : 'text-gray-700') }}">
                    @if($evaluation->status == 'completed')
                        <p>This evaluation was completed on {{ $evaluation->completed_at->format('M d, Y') }}.</p>
                    @elseif($evaluation->status == 'in_progress')
                        <p>This evaluation is currently in progress. Last edited: {{ $evaluation->last_edited_at ? $evaluation->last_edited_at->format('M d, Y') : 'Not yet edited' }}</p>
                    @else
                        <p>This evaluation has not been started yet.</p>
                    @endif
                    
                    @if($evaluation->due_date)
                        <p class="mt-1 {{ $evaluation->is_overdue ? 'text-red-600 font-semibold' : '' }}">
                            Due date: {{ $evaluation->due_date->format('M d, Y') }}
                            @if($evaluation->is_overdue)
                                <span class="font-semibold">(Overdue)</span>
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Evaluation Info -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden md:col-span-1">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <h2 class="text-lg font-medium text-gray-900">Evaluation Info</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Brief</h3>
                    <p class="mt-1 text-md text-gray-900">{{ $evaluation->submission->brief->title }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Assigned Date</h3>
                    <p class="mt-1 text-md text-gray-900">{{ $evaluation->assigned_at ? $evaluation->assigned_at->format('M d, Y') : 'Not recorded' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Evaluator</h3>
                    <p class="mt-1 text-md text-gray-900">{{ $evaluation->evaluator->username }} ({{ $evaluation->evaluator->first_name }} {{ $evaluation->evaluator->last_name }})</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Student Being Evaluated</h3>
                    <p class="mt-1 text-md text-gray-900">{{ $evaluation->submission->student->username }} ({{ $evaluation->submission->student->first_name }} {{ $evaluation->submission->student->last_name }})</p>
                </div>
            </div>
        </div>

        <!-- Submission Info -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden md:col-span-2">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Submission</h2>
                <a href="{{ route('teacher.submissions.show', $evaluation->submission->id) }}" class="text-sm text-blue-600 hover:text-blue-800">View Full Submission</a>
            </div>
            <div class="p-6">
                <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                    <h3 class="font-medium text-gray-900 mb-2">Submission Content</h3>
                    <p class="text-gray-600 mb-4">{{ Str::limit($evaluation->submission->content, 300) }}</p>
                    
                    @if($evaluation->submission->file_path)
                        <div class="mt-4">
                            <h3 class="font-medium text-gray-900 mb-2">Attached Files</h3>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-paperclip text-gray-400 mr-2"></i>
                                <a href="{{ route('teacher.submissions.download', $evaluation->submission->id) }}" class="text-blue-600 hover:text-blue-800">
                                    Download Attachment
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Evaluation Results (if completed) -->
    @if($evaluation->status == 'completed')
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <h2 class="text-lg font-medium text-gray-900">Evaluation Results</h2>
            </div>
            <div class="p-6">
                @if(count($evaluation->answers) > 0)
                    <div class="space-y-6">
                        @foreach($evaluation->answers as $answer)
                            <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                                <h3 class="font-medium text-gray-900 mb-2">{{ $answer->criterion->name }}</h3>
                                <p class="text-sm text-gray-500 mb-4">{{ $answer->criterion->description }}</p>
                                
                                <div class="mb-2">
                                    <span class="text-sm font-medium text-gray-700">Score: </span>
                                    <span class="text-sm font-bold text-gray-900">{{ $answer->score }} / {{ $answer->criterion->max_score }}</span>
                                </div>
                                
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-1">Feedback:</h4>
                                    <p class="text-sm text-gray-800 bg-white p-3 rounded border border-gray-200">{{ $answer->feedback }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Overall Feedback</h3>
                        <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                            <p class="text-gray-800">{{ $evaluation->feedback ?? 'No overall feedback provided.' }}</p>
                        </div>
                        
                        <div class="mt-4 flex items-center">
                            <span class="text-sm font-medium text-gray-700 mr-2">Total Score:</span>
                            <span class="text-xl font-bold text-gray-900">{{ $evaluation->answers->sum('score') }} / {{ $evaluation->answers->sum(function($answer) { return $answer->criterion->max_score; }) }}</span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 mb-2">
                            <i class="fas fa-clipboard-check text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">No evaluation criteria found</h3>
                        <p class="text-gray-500">This evaluation does not have any criteria answers recorded.</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Teacher Actions -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h2 class="text-lg font-medium text-gray-900">Teacher Actions</h2>
        </div>
        <div class="p-6">
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('teacher.evaluations.student.show', [$evaluation->id, $evaluation->evaluator_id]) }}" class="inline-flex items-center px-4 py-2 border border-blue-300 rounded-md shadow-sm text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-user-check mr-2"></i> View as Evaluator
                </a>
                
                <a href="{{ route('teacher.evaluations.student.show', [$evaluation->id, $evaluation->submission->student_id]) }}" class="inline-flex items-center px-4 py-2 border border-blue-300 rounded-md shadow-sm text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-user mr-2"></i> View as Student
                </a>
                
                @if($evaluation->status != 'completed')
                    <form action="{{ route('teacher.evaluations.remind', $evaluation->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-bell mr-2"></i> Send Reminder
                        </button>
                    </form>
                @endif
                
                <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500" onclick="toggleReassignForm()">
                    <i class="fas fa-exchange-alt mr-2"></i> Reassign Evaluation
                </button>
                
                @if($evaluation->status != 'completed')
                    <form action="{{ route('teacher.evaluations.cancel', $evaluation->id) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="return confirm('Are you sure you want to cancel this evaluation? This action cannot be undone.');">
                            <i class="fas fa-trash-alt mr-2"></i> Cancel Evaluation
                        </button>
                    </form>
                @endif
            </div>
            
            <!-- Reassign Form (hidden by default) -->
            <div id="reassignForm" class="hidden mt-6 p-4 bg-gray-50 rounded-md border border-gray-200">
                <h3 class="text-md font-medium text-gray-900 mb-3">Reassign to Another Student</h3>
                <form action="{{ route('teacher.evaluations.reassign', $evaluation->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="new_evaluator_id" class="block text-sm font-medium text-gray-700 mb-1">Select New Evaluator</label>
                        <select id="new_evaluator_id" name="new_evaluator_id" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">-- Select a student --</option>
                            @foreach($potentialEvaluators as $student)
                                <option value="{{ $student->id }}">
                                    {{ $student->username }} ({{ $student->first_name }} {{ $student->last_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" class="mr-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="toggleReassignForm()">
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Reassign
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleReassignForm() {
        const form = document.getElementById('reassignForm');
        form.classList.toggle('hidden');
    }
</script>
@endsection 