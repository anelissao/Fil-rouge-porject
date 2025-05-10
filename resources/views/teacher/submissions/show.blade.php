@extends('layouts.app')

@section('title', 'View Submission')

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
                                <a href="{{ route('teacher.submissions.index') }}" class="text-blue-200 hover:text-white transition-colors">
                                    Submissions
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-blue-200 mx-2 text-xs"></i>
                                <span class="text-white">Submission Details</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-bold text-white mb-2">Submission Details</h1>
                <p class="text-blue-100">
                    {{ $submission->student->username }}'s submission for <strong>{{ $submission->brief->title }}</strong>
                </p>
            </div>
            <div>
                <a href="{{ route('teacher.submissions.index') }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg backdrop-blur-sm transition-all duration-300 border border-white/20">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Submissions
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Submission Information Card -->
        <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700">
            <div class="border-b border-gray-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white">Submission Information</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-white mb-2">Student</h3>
                        <p class="text-gray-300 mb-4">{{ $submission->student->username }}</p>

                        <h3 class="text-lg font-medium text-white mb-2">Submitted On</h3>
                        <p class="text-gray-300 mb-4">{{ $submission->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-white mb-2">Brief</h3>
                        <p class="text-gray-300 mb-4">{{ $submission->brief->title }}</p>

                        <h3 class="text-lg font-medium text-white mb-2">Status</h3>
                        <div class="mb-4">
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center bg-green-900/30 text-green-400">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-green-400"></span>
                                Submitted
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submission Content Card -->
        <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700">
            <div class="border-b border-gray-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white">Submission Content</h2>
            </div>
            <div class="p-6">
                @if($submission->content)
                    <div class="bg-gray-750 p-4 rounded-lg border border-gray-700 mb-6">
                        <div class="text-gray-300 whitespace-pre-line">
                            {!! nl2br(e($submission->content)) !!}
                        </div>
                    </div>
                @endif
                
                @if($submission->file_path)
                    <div class="mt-4">
                        <h3 class="text-lg font-medium text-white mb-3">Attachments</h3>
                        <div class="flex items-center p-3 bg-gray-750 rounded-lg border border-gray-700">
                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-blue-900/30 text-blue-400">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="ml-3 flex-grow">
                                <p class="text-sm font-medium text-white">{{ basename($submission->file_path) }}</p>
                                <p class="text-xs text-gray-400">Added {{ $submission->created_at->format('M d, Y') }}</p>
                            </div>
                            <a href="{{ asset('storage/' . $submission->file_path) }}" class="flex-shrink-0 p-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors" target="_blank" download>
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Evaluations Card -->
    <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700 mb-8">
        <div class="border-b border-gray-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Evaluations</h2>
        </div>
        <div class="p-6">
            @if(count($submission->evaluations) > 0)
                <div class="space-y-6">
                    @foreach($submission->evaluations as $evaluation)
                        <div class="p-4 border rounded-lg 
                            {{ $evaluation->status == 'completed' ? 'border-green-500/30 bg-green-900/10' : 'border-yellow-500/30 bg-yellow-900/10' }}">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                                <div>
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full bg-gray-700 text-gray-400">
                                            <i class="fas fa-user-check"></i>
                                        </div>
                                        <h3 class="ml-2 text-lg font-medium text-white">
                                            {{ $evaluation->evaluator->username }}
                                        </h3>
                                    </div>
                                    <p class="text-sm text-gray-400 mt-1">
                                        @if($evaluation->status == 'completed')
                                            Completed on {{ $evaluation->updated_at->format('M d, Y') }}
                                        @else
                                            Assigned on {{ $evaluation->created_at->format('M d, Y') }}
                                        @endif
                                    </p>
                                </div>
                                <div class="mt-2 sm:mt-0">
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center
                                        {{ $evaluation->status == 'completed' 
                                            ? 'bg-green-900/30 text-green-400' 
                                            : 'bg-yellow-900/30 text-yellow-400' }}">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 
                                            {{ $evaluation->status == 'completed' 
                                                ? 'bg-green-400' 
                                                : 'bg-yellow-400' }}"></span>
                                        {{ ucfirst($evaluation->status) }}
                                    </span>
                                </div>
                            </div>
                            
                            @if($evaluation->status == 'completed')
                                <div class="mt-4 space-y-4">
                                    @if($evaluation->feedback)
                                        <div class="p-3 bg-gray-750 rounded-lg border border-gray-700">
                                            <h4 class="text-md font-semibold text-white">Overall Feedback</h4>
                                            <div class="mt-2 text-gray-300 whitespace-pre-line">
                                                {!! nl2br(e($evaluation->feedback)) !!}
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($evaluation->answers && $evaluation->answers->count() > 0)
                                        <div class="space-y-3">
                                            <h4 class="text-md font-semibold text-white">Criteria Evaluation</h4>
                                            @foreach($evaluation->answers as $answer)
                                                <div class="p-3 bg-gray-750 rounded-lg border border-gray-700">
                                                    <h4 class="text-md font-semibold text-white">{{ $answer->criterion->title }}</h4>
                                                    <p class="text-sm text-gray-400 mt-1">{{ $answer->criterion->description }}</p>
                                                    
                                                    <div class="mt-2 flex items-center">
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
                                                        <div class="mt-3 text-sm bg-gray-800 p-3 rounded-lg">
                                                            <p class="font-medium text-white mb-1">Comment:</p>
                                                            <p class="text-gray-300">{{ $answer->comment }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="mt-4 p-4 bg-gray-750 rounded-lg border border-gray-700 text-center">
                                    <p class="text-gray-300 mb-4">This evaluation is not yet completed.</p>
                                    <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors send-reminder-btn" data-id="{{ $evaluation->id }}">
                                        <i class="fas fa-bell mr-2"></i> Send Reminder
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-16 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-700 text-gray-500 mb-4">
                        <i class="fas fa-clipboard-check text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-medium text-white mb-2">No evaluations assigned</h3>
                    <p class="text-gray-400 max-w-md mx-auto mb-6">No evaluations have been assigned for this submission yet.</p>
                    <a href="{{ route('teacher.evaluations.assign') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i> Assign Evaluation
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Reminder Modal -->
    <div id="reminderModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-gray-800 rounded-xl shadow-xl max-w-md w-full mx-4">
            <div class="border-b border-gray-700 px-6 py-4 flex items-center justify-between">
                <h3 class="text-xl font-bold text-white">Send Reminder</h3>
                <button type="button" class="close-modal text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <p class="text-gray-300 mb-4">Send a reminder to the evaluator about this evaluation?</p>
                <div class="mb-4">
                    <label for="reminderMessage" class="block text-sm font-medium text-white mb-2">Custom Message (Optional)</label>
                    <textarea id="reminderMessage" rows="3" class="block w-full rounded-lg bg-gray-700 border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-white placeholder-gray-400" placeholder="Add a personal message..."></textarea>
                </div>
            </div>
            <div class="border-t border-gray-700 px-6 py-4 flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors modal-cancel">
                    Cancel
                </button>
                <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors reminder-confirm">
                    Send Reminder
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reminderModal = document.getElementById('reminderModal');
        const reminderButtons = document.querySelectorAll('.send-reminder-btn');
        const closeModalButton = document.querySelector('.close-modal');
        const cancelButton = document.querySelector('.modal-cancel');
        const confirmButton = document.querySelector('.reminder-confirm');
        let currentEvaluationId = null;

        // Open modal
        reminderButtons.forEach(button => {
            button.addEventListener('click', function() {
                currentEvaluationId = this.getAttribute('data-id');
                reminderModal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            });
        });

        // Close modal functions
        const closeModal = () => {
            reminderModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            document.getElementById('reminderMessage').value = '';
            currentEvaluationId = null;
        };

        closeModalButton.addEventListener('click', closeModal);
        cancelButton.addEventListener('click', closeModal);

        // Send reminder
        confirmButton.addEventListener('click', function() {
            if (!currentEvaluationId) return;
            
            const message = document.getElementById('reminderMessage').value;
            
            // Send AJAX request
            fetch(`/teacher/evaluations/${currentEvaluationId}/remind`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success notification
                    alert('Reminder sent successfully!');
                    closeModal();
                } else {
                    alert('Failed to send reminder: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while sending the reminder.');
            });
        });
    });
</script>
@endsection 