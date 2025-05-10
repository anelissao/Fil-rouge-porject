@extends('layouts.app')

@section('title', 'Evaluation Details')

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
                                <a href="{{ route('teacher.evaluations.index') }}" class="text-blue-200 hover:text-white transition-colors">
                                    Evaluations
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-blue-200 mx-2 text-xs"></i>
                                <span class="text-white">Evaluation Details</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-bold text-white mb-2">Evaluation Details</h1>
                <p class="text-blue-100">
                    Evaluation of {{ $evaluation->submission->student->username }}'s submission by {{ $evaluation->evaluator->username }}
                </p>
            </div>
            <div>
                <a href="{{ route('teacher.evaluations.index') }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg backdrop-blur-sm transition-all duration-300 border border-white/20">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Evaluations
                </a>
            </div>
        </div>
    </div>

    <!-- Status Banner -->
    <div class="mb-8 p-4 rounded-lg 
        {{ $evaluation->status == 'completed' 
            ? 'bg-green-900/10 border border-green-500/30' 
            : ($evaluation->status == 'in_progress' 
                ? 'bg-yellow-900/10 border border-yellow-500/30' 
                : 'bg-gray-800 border border-gray-700') }}">
        <div class="flex">
            <div class="flex-shrink-0 mt-0.5">
                @if($evaluation->status == 'completed')
                    <i class="fas fa-check-circle text-green-400"></i>
                @elseif($evaluation->status == 'in_progress')
                    <i class="fas fa-clock text-yellow-400"></i>
                @else
                    <i class="fas fa-info-circle text-gray-400"></i>
                @endif
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium 
                    {{ $evaluation->status == 'completed' 
                        ? 'text-green-400' 
                        : ($evaluation->status == 'in_progress' 
                            ? 'text-yellow-400' 
                            : 'text-gray-300') }}">
                    Evaluation Status: {{ ucfirst($evaluation->status) }}
                </h3>
                <div class="mt-2 text-sm 
                    {{ $evaluation->status == 'completed' 
                        ? 'text-green-300' 
                        : ($evaluation->status == 'in_progress' 
                            ? 'text-yellow-300' 
                            : 'text-gray-400') }}">
                    @if($evaluation->status == 'completed')
                        <p>This evaluation was completed on {{ $evaluation->completed_at->format('M d, Y') }}.</p>
                    @elseif($evaluation->status == 'in_progress')
                        <p>This evaluation is currently in progress. Last edited: {{ $evaluation->last_edited_at ? $evaluation->last_edited_at->format('M d, Y') : 'Not yet edited' }}</p>
                    @else
                        <p>This evaluation has not been started yet.</p>
                    @endif
                    
                    @if($evaluation->due_at)
                        <p class="mt-1 {{ $evaluation->is_overdue ? 'text-red-400 font-semibold' : '' }}">
                            Due date: {{ $evaluation->due_at->format('M d, Y') }}
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
        <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700">
            <div class="border-b border-gray-700 px-6 py-4">
                <h2 class="text-lg font-medium text-white">Evaluation Info</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-400">Brief</h3>
                    <p class="mt-1 text-md text-white">{{ $evaluation->submission->brief->title }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-400">Assigned Date</h3>
                    <p class="mt-1 text-md text-white">{{ $evaluation->created_at ? $evaluation->created_at->format('M d, Y') : 'Not recorded' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-400">Evaluator</h3>
                    <p class="mt-1 text-md text-white">{{ $evaluation->evaluator->username }} ({{ $evaluation->evaluator->first_name }} {{ $evaluation->evaluator->last_name }})</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-400">Student Being Evaluated</h3>
                    <p class="mt-1 text-md text-white">{{ $evaluation->submission->student->username }} ({{ $evaluation->submission->student->first_name }} {{ $evaluation->submission->student->last_name }})</p>
                </div>
            </div>
        </div>

        <!-- Submission Info -->
        <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700 md:col-span-2">
            <div class="border-b border-gray-700 px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-medium text-white">Submission</h2>
                <a href="{{ route('teacher.submissions.show', $evaluation->submission->id) }}" class="text-sm text-blue-400 hover:text-blue-300 transition-colors">View Full Submission</a>
            </div>
            <div class="p-6">
                <div class="bg-gray-750 p-4 rounded-lg border border-gray-700">
                    <h3 class="font-medium text-white mb-2">Submission Content</h3>
                    <p class="text-gray-300 mb-4">{{ Str::limit($evaluation->submission->content, 300) }}</p>
                    
                    @if($evaluation->submission->file_path)
                        <div class="mt-4">
                            <h3 class="font-medium text-white mb-2">Attached Files</h3>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-paperclip text-gray-400 mr-2"></i>
                                <a href="{{ route('teacher.submissions.download', $evaluation->submission->id) }}" class="text-blue-400 hover:text-blue-300 transition-colors">
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
        <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700 mb-8">
            <div class="border-b border-gray-700 px-6 py-4">
                <h2 class="text-lg font-medium text-white">Evaluation Results</h2>
            </div>
            <div class="p-6">
                @if(count($evaluation->answers) > 0)
                    <div class="space-y-6">
                        @foreach($evaluation->answers as $answer)
                            <div class="bg-gray-750 p-4 rounded-lg border border-gray-700">
                                <h3 class="font-medium text-white mb-2">{{ $answer->criterion->title }}</h3>
                                <p class="text-sm text-gray-400 mb-4">{{ $answer->criterion->description }}</p>
                                
                                <div class="mb-2">
                                    <span class="text-sm font-medium text-gray-300">Result: </span>
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
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-300 mb-1">Comment:</h4>
                                        <p class="text-sm text-gray-300 bg-gray-800 p-3 rounded-lg border border-gray-700">{{ $answer->comment }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-700">
                        <h3 class="text-lg font-medium text-white mb-4">Overall Feedback</h3>
                        <div class="bg-gray-750 p-4 rounded-lg border border-gray-700">
                            <p class="text-gray-300">{{ $evaluation->feedback ?? 'No overall feedback provided.' }}</p>
                        </div>
                    </div>
                @else
                    <div class="py-16 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-700 text-gray-500 mb-4">
                            <i class="fas fa-clipboard-check text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-medium text-white mb-2">No evaluation criteria found</h3>
                        <p class="text-gray-400 max-w-md mx-auto">This evaluation does not have any criteria answers recorded.</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Teacher Actions -->
    <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700 mb-8">
        <div class="border-b border-gray-700 px-6 py-4">
            <h2 class="text-lg font-medium text-white">Teacher Actions</h2>
        </div>
        <div class="p-6">
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('teacher.evaluations.student.show', [$evaluation->id, $evaluation->evaluator_id]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600/20 hover:bg-blue-600/30 text-blue-400 rounded-lg transition-all duration-300 border border-blue-500/30">
                    <i class="fas fa-user-check mr-2"></i> View as Evaluator
                </a>
                
                <a href="{{ route('teacher.evaluations.student.show', [$evaluation->id, $evaluation->submission->student_id]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600/20 hover:bg-blue-600/30 text-blue-400 rounded-lg transition-all duration-300 border border-blue-500/30">
                    <i class="fas fa-user mr-2"></i> View as Student
                </a>
                
                @if($evaluation->status != 'completed')
                    <form action="{{ route('teacher.evaluations.remind', $evaluation->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-bell mr-2"></i> Send Reminder
                        </button>
                    </form>
                @endif
                
                <button type="button" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors" onclick="toggleReassignForm()">
                    <i class="fas fa-exchange-alt mr-2"></i> Reassign Evaluation
                </button>
                
                @if($evaluation->status != 'completed')
                    <form action="{{ route('teacher.evaluations.cancel', $evaluation->id) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors" onclick="return confirm('Are you sure you want to cancel this evaluation? This action cannot be undone.');">
                            <i class="fas fa-trash-alt mr-2"></i> Cancel Evaluation
                        </button>
                    </form>
                @endif
            </div>
            
            <!-- Reassign Form (hidden by default) -->
            <div id="reassignForm" class="hidden mt-6 p-4 bg-gray-750 rounded-lg border border-gray-700">
                <h3 class="text-md font-medium text-white mb-3">Reassign to Another Student</h3>
                <form action="{{ route('teacher.evaluations.reassign', $evaluation->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="new_evaluator_id" class="block text-sm font-medium text-gray-300 mb-1">Select New Evaluator</label>
                        <select id="new_evaluator_id" name="new_evaluator_id" required class="block w-full rounded-lg bg-gray-700 border-gray-600 text-white focus:ring-blue-500 focus:border-blue-500 py-2 px-3">
                            <option value="">-- Select a student --</option>
                            @foreach($potentialEvaluators as $student)
                                <option value="{{ $student->id }}">
                                    {{ $student->username }} ({{ $student->first_name }} {{ $student->last_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" class="mr-3 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors" onclick="toggleReassignForm()">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
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