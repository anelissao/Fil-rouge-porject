@extends('layouts.app')

@section('title', 'View Submission')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Submission Details</h1>
            <p class="page-subtitle">
                {{ $submission->student->username }}'s submission for <strong>{{ $submission->brief->title }}</strong>
            </p>
        </div>
        <div class="header-actions">
            <a href="{{ route('teacher.submissions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Submissions
            </a>
        </div>
    </div>

    <div class="content-grid">
        <!-- Submission Information Card -->
        <div class="content-card">
            <div class="card-header">
                <h2 class="section-title">Submission Information</h2>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Student</span>
                        <span class="info-value">{{ $submission->student->username }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Submitted On</span>
                        <span class="info-value">{{ $submission->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Brief</span>
                        <span class="info-value">{{ $submission->brief->title }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status</span>
                        <span class="info-value status-badge completed">Submitted</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submission Content Card -->
        <div class="content-card">
            <div class="card-header">
                <h2 class="section-title">Submission Content</h2>
            </div>
            <div class="card-body">
                @if($submission->content)
                    <div class="submission-content">
                        {!! nl2br(e($submission->content)) !!}
                    </div>
                @endif
                
                @if($submission->file_path)
                    <div class="file-attachment">
                        <h3 class="subsection-title">Attachments</h3>
                        <div class="attachment-item">
                            <i class="fas fa-file-alt attachment-icon"></i>
                            <div class="attachment-details">
                                <span class="attachment-name">{{ basename($submission->file_path) }}</span>
                                <span class="attachment-meta">Added {{ $submission->created_at->format('M d, Y') }}</span>
                            </div>
                            <a href="{{ asset('storage/' . $submission->file_path) }}" class="btn-icon download-btn" target="_blank" download>
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Evaluations Card -->
        <div class="content-card">
            <div class="card-header">
                <h2 class="section-title">Evaluations</h2>
            </div>
            <div class="card-body">
                @if(count($submission->evaluations) > 0)
                    <div class="evaluations-list">
                        @foreach($submission->evaluations as $evaluation)
                            <div class="evaluation-item">
                                <div class="evaluation-header">
                                    <div class="evaluation-meta">
                                        <span class="evaluator-name">
                                            <i class="fas fa-user-check"></i> {{ $evaluation->evaluator->username }}
                                        </span>
                                        <span class="evaluation-status {{ $evaluation->status }}">
                                            {{ ucfirst($evaluation->status) }}
                                        </span>
                                    </div>
                                    <div class="evaluation-timestamp">
                                        @if($evaluation->status == 'completed')
                                            Completed on {{ $evaluation->updated_at->format('M d, Y') }}
                                        @else
                                            Assigned on {{ $evaluation->created_at->format('M d, Y') }}
                                        @endif
                                    </div>
                                </div>
                                
                                @if($evaluation->status == 'completed')
                                    <div class="evaluation-content">
                                        @if($evaluation->overall_comment)
                                            <div class="evaluation-comment">
                                                <h4>Overall Comment</h4>
                                                <p>{{ $evaluation->overall_comment }}</p>
                                            </div>
                                        @endif
                                        
                                        @if(isset($evaluation->criteriaScores) && count($evaluation->criteriaScores) > 0)
                                            <div class="scores-list">
                                                <h4>Criteria Scores</h4>
                                                @foreach($evaluation->criteriaScores as $score)
                                                    <div class="score-item">
                                                        <div class="score-header">
                                                            <span class="criterion-name">{{ $score->criterion->name }}</span>
                                                            <span class="score-value">{{ $score->score }}/10</span>
                                                        </div>
                                                        @if($score->comment)
                                                            <p class="score-comment">{{ $score->comment }}</p>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="pending-message">
                                        <p>This evaluation is not yet completed.</p>
                                        <button class="btn btn-primary send-reminder-btn" data-id="{{ $evaluation->id }}">
                                            <i class="fas fa-bell"></i> Send Reminder
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-clipboard-check"></i>
                        <p>No evaluations have been assigned for this submission yet.</p>
                        <a href="{{ route('teacher.evaluations.assign') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Assign Evaluation
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Reminder Modal -->
    <div id="reminderModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Send Reminder</h3>
                <button type="button" class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Send a reminder to the evaluator about this evaluation?</p>
                <div class="form-group">
                    <label for="reminderMessage">Custom Message (Optional)</label>
                    <textarea id="reminderMessage" class="form-control" rows="3" placeholder="Add a personal message..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline modal-cancel">Cancel</button>
                <button type="button" class="btn btn-primary reminder-confirm">Send Reminder</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Page Layout */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.5rem;
    }

    .page-title {
        font-size: 1.75rem;
        margin-bottom: 0.25rem;
        color: var(--secondary-color);
    }

    .page-subtitle {
        color: var(--accent-color);
    }

    .content-grid {
        display: grid;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    /* Content Cards */
    .content-card {
        background-color: var(--highlight-color);
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(229, 231, 235, 0.1);
    }

    .section-title {
        font-size: 1.25rem;
        color: var(--secondary-color);
        margin: 0;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Info Grid */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 0.875rem;
        color: var(--accent-color);
        margin-bottom: 0.375rem;
    }

    .info-value {
        font-size: 1rem;
        color: var(--secondary-color);
        font-weight: 500;
    }

    /* Status Badge */
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 500;
        width: fit-content;
    }

    .status-badge.completed {
        background-color: rgba(72, 187, 120, 0.1);
        color: #48BB78;
    }

    .status-badge.pending {
        background-color: rgba(236, 201, 75, 0.1);
        color: #ECC94B;
    }

    .status-badge.in_progress {
        background-color: rgba(66, 153, 225, 0.1);
        color: #4299E1;
    }

    /* Submission Content */
    .submission-content {
        background-color: rgba(255, 255, 255, 0.03);
        border-radius: 0.375rem;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        white-space: pre-line;
        color: var(--secondary-color);
    }

    /* File Attachment */
    .subsection-title {
        font-size: 1.125rem;
        color: var(--secondary-color);
        margin-bottom: 1rem;
    }

    .attachment-item {
        display: flex;
        align-items: center;
        background-color: rgba(255, 255, 255, 0.03);
        border-radius: 0.375rem;
        padding: 0.75rem 1rem;
    }

    .attachment-icon {
        font-size: 1.25rem;
        color: var(--primary-color);
        margin-right: 0.75rem;
    }

    .attachment-details {
        flex: 1;
    }

    .attachment-name {
        display: block;
        font-weight: 500;
        color: var(--secondary-color);
        margin-bottom: 0.25rem;
    }

    .attachment-meta {
        display: block;
        font-size: 0.75rem;
        color: var(--accent-color);
    }

    .download-btn {
        color: var(--primary-color);
    }

    /* Evaluations */
    .evaluations-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .evaluation-item {
        background-color: rgba(255, 255, 255, 0.03);
        border-radius: 0.375rem;
        overflow: hidden;
    }

    .evaluation-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.25rem;
        background-color: rgba(0, 0, 0, 0.1);
    }

    .evaluation-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .evaluator-name {
        font-weight: 500;
        color: var(--secondary-color);
    }

    .evaluation-status {
        padding: 0.25rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .evaluation-status.completed {
        background-color: rgba(72, 187, 120, 0.1);
        color: #48BB78;
    }

    .evaluation-status.pending {
        background-color: rgba(236, 201, 75, 0.1);
        color: #ECC94B;
    }

    .evaluation-timestamp {
        font-size: 0.875rem;
        color: var(--accent-color);
    }

    .evaluation-content {
        padding: 1.25rem;
    }

    .evaluation-comment {
        margin-bottom: 1.5rem;
    }

    .evaluation-comment h4 {
        font-size: 1rem;
        color: var(--secondary-color);
        margin-bottom: 0.5rem;
    }

    .evaluation-comment p {
        color: var(--secondary-color);
        white-space: pre-line;
    }

    .scores-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .scores-list h4 {
        font-size: 1rem;
        color: var(--secondary-color);
        margin-bottom: 0.5rem;
    }

    .score-item {
        border-top: 1px solid rgba(229, 231, 235, 0.1);
        padding-top: 1rem;
    }

    .score-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .criterion-name {
        font-weight: 500;
        color: var(--secondary-color);
    }

    .score-value {
        font-weight: 700;
        color: var(--primary-color);
    }

    .score-comment {
        color: var(--secondary-color);
        font-size: 0.875rem;
    }

    .pending-message {
        padding: 1.25rem;
        text-align: center;
        color: var(--accent-color);
    }

    /* Empty State */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        text-align: center;
        color: var(--accent-color);
    }

    .empty-state i {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: var(--accent-color);
    }

    .empty-state p {
        margin-bottom: 1rem;
    }

    /* Modal Styles */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .modal-content {
        background-color: var(--highlight-color);
        border-radius: 0.5rem;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(229, 231, 235, 0.1);
    }

    .modal-title {
        font-size: 1.25rem;
        color: var(--secondary-color);
        margin: 0;
    }

    .close-modal {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--accent-color);
        cursor: pointer;
    }

    .modal-body {
        padding: 1.5rem;
        color: var(--secondary-color);
    }

    .form-group {
        margin-top: 1rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        color: var(--accent-color);
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(229, 231, 235, 0.2);
        border-radius: 0.375rem;
        color: var(--secondary-color);
        font-family: inherit;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding: 1.25rem 1.5rem;
        border-top: 1px solid rgba(229, 231, 235, 0.1);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            gap: 1rem;
        }

        .header-actions {
            width: 100%;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .evaluation-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Reminder modal functionality
        const reminderModal = document.getElementById('reminderModal');
        const reminderBtns = document.querySelectorAll('.send-reminder-btn');
        const reminderCancel = document.querySelector('.modal-cancel');
        const reminderConfirm = document.querySelector('.reminder-confirm');
        const closeReminderBtn = document.querySelector('.close-modal');
        let currentEvalId = null;

        reminderBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                currentEvalId = this.dataset.id;
                reminderModal.style.display = 'flex';
            });
        });

        if (reminderCancel) {
            reminderCancel.addEventListener('click', function() {
                reminderModal.style.display = 'none';
                currentEvalId = null;
            });
        }

        if (closeReminderBtn) {
            closeReminderBtn.addEventListener('click', function() {
                reminderModal.style.display = 'none';
                currentEvalId = null;
            });
        }

        if (reminderConfirm) {
            reminderConfirm.addEventListener('click', function() {
                if (currentEvalId) {
                    const message = document.getElementById('reminderMessage').value;
                    
                    // Send reminder via AJAX
                    fetch(`/teacher/evaluations/${currentEvalId}/remind`, {
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
                            alert('Reminder sent successfully!');
                        } else {
                            alert('Failed to send reminder.');
                        }
                        reminderModal.style.display = 'none';
                        currentEvalId = null;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                        reminderModal.style.display = 'none';
                        currentEvalId = null;
                    });
                }
            });
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            if (e.target === reminderModal) {
                reminderModal.style.display = 'none';
                currentEvalId = null;
            }
        });
    });
</script>
@endsection 