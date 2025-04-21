@extends('layouts.app')

@section('title', 'Assign Evaluations')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Assign Evaluations</h1>
            <p class="page-subtitle">Create peer evaluation assignments for student submissions</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('teacher.evaluations.index') }}" class="btn btn-outline">
                <i class="fas fa-list"></i> All Evaluations
            </a>
            <a href="{{ route('teacher.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="content-grid">
        <!-- Assignment Method Card -->
        <div class="content-card">
            <h2 class="section-title">Assignment Method</h2>
            <div class="assignment-methods">
                <button id="manualAssignmentBtn" class="method-card active">
                    <div class="method-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="method-content">
                        <h3 class="method-title">Manual Assignment</h3>
                        <p class="method-desc">Manually pair students for peer evaluations</p>
                    </div>
                </button>
                <button id="randomAssignmentBtn" class="method-card">
                    <div class="method-icon">
                        <i class="fas fa-random"></i>
                    </div>
                    <div class="method-content">
                        <h3 class="method-title">Random Assignment</h3>
                        <p class="method-desc">Automatically create random evaluation pairs</p>
                    </div>
                </button>
            </div>
        </div>

        <!-- Manual Assignment Form -->
        <div id="manualAssignmentForm" class="content-card">
            <div class="card-header">
                <h2 class="section-title">Manual Assignment</h2>
            </div>
            
            <form action="{{ route('teacher.evaluations.store') }}" method="POST">
                @csrf
                <input type="hidden" name="assignment_type" value="manual">
                
                <div class="form-section">
                    <div class="form-group">
                        <label for="submission_id">Select Submission to Evaluate</label>
                        <select id="submission_id" name="submission_id" class="form-control" required>
                            <option value="">-- Select a submission --</option>
                            @foreach($submissions as $submission)
                                <option value="{{ $submission->id }}">
                                    {{ $submission->student->username }} - {{ $submission->brief->title }} 
                                    (Submitted: {{ $submission->created_at->format('M d, Y') }})
                                </option>
                            @endforeach
                        </select>
                        @error('submission_id')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="evaluator_id">Select Evaluator</label>
                        <select id="evaluator_id" name="evaluator_id" class="form-control" required>
                            <option value="">-- Select an evaluator --</option>
                            @foreach($evaluators as $evaluator)
                                <option value="{{ $evaluator->id }}">
                                    {{ $evaluator->username }} ({{ $evaluator->first_name }} {{ $evaluator->last_name }})
                                </option>
                            @endforeach
                        </select>
                        @error('evaluator_id')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="due_date">Evaluation Due Date (Optional)</label>
                        <input type="datetime-local" id="due_date" name="due_date" class="form-control">
                        <small class="form-text">Leave empty to use the default due date (7 days from assignment)</small>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Assignment
                    </button>
                </div>
            </form>
        </div>

        <!-- Random Assignment Form -->
        <div id="randomAssignmentForm" class="content-card" style="display: none;">
            <div class="card-header">
                <h2 class="section-title">Random Assignment</h2>
            </div>
            
            <form action="{{ route('teacher.evaluations.random') }}" method="POST">
                @csrf
                <input type="hidden" name="assignment_type" value="random">
                
                <div class="form-section">
                    <div class="form-group">
                        <label for="brief_id">Select Brief</label>
                        <select id="brief_id" name="brief_id" class="form-control" required>
                            <option value="">-- Select a brief --</option>
                            @foreach($briefs ?? [] as $brief)
                                <option value="{{ $brief->id }}">
                                    {{ $brief->title }} ({{ $brief->submissions_count ?? 0 }} submissions)
                                </option>
                            @endforeach
                        </select>
                        @error('brief_id')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="evaluations_per_submission">Evaluations Per Submission</label>
                        <input type="number" id="evaluations_per_submission" name="evaluations_per_submission" 
                               class="form-control" min="1" max="5" value="2" required>
                        <small class="form-text">Number of students who will evaluate each submission</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="due_date_random">Evaluation Due Date (Optional)</label>
                        <input type="datetime-local" id="due_date_random" name="due_date" class="form-control">
                        <small class="form-text">Leave empty to use the default due date (7 days from assignment)</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="prevent_self_evaluation" checked>
                            <span>Prevent self-evaluation</span>
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="prevent_reciprocal" checked>
                            <span>Prevent reciprocal evaluations</span>
                        </label>
                        <small class="form-text">If A evaluates B, B will not evaluate A</small>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-random"></i> Generate Random Assignments
                    </button>
                </div>
            </form>
        </div>

        <!-- Current Assignments -->
        <div class="content-card current-assignments">
            <div class="card-header">
                <h2 class="section-title">Current Assignments</h2>
                <div class="header-actions">
                    <div class="search-wrapper">
                        <input type="text" id="assignmentSearch" class="search-input" placeholder="Search assignments...">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                </div>
            </div>
            
            <div class="assignments-table-container">
                <table class="assignments-table">
                    <thead>
                        <tr>
                            <th>Evaluator</th>
                            <th>Submission</th>
                            <th>Brief</th>
                            <th>Status</th>
                            <th>Assigned Date</th>
                            <th>Due Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($currentAssignments ?? [] as $assignment)
                            <tr>
                                <td class="evaluator-cell">
                                    {{ $assignment->evaluator->username }}
                                </td>
                                <td>
                                    {{ $assignment->submission->student->username }}
                                </td>
                                <td>
                                    {{ $assignment->submission->brief->title }}
                                </td>
                                <td>
                                    <span class="status-badge {{ $assignment->status }}">
                                        {{ ucfirst($assignment->status) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $assignment->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    {{ $assignment->due_date ? $assignment->due_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="actions-cell">
                                    <form action="{{ route('teacher.evaluations.destroy', $assignment->id) }}" 
                                          method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn-icon delete-btn" title="Delete Assignment">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-table">
                                    <div class="empty-state">
                                        <i class="fas fa-clipboard-check"></i>
                                        <p>No evaluation assignments found.</p>
                                        <p class="hint">Use the forms above to create new assignments.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Deletion</h3>
                <button type="button" class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this evaluation assignment?</p>
                <p class="warning">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline modal-cancel">Cancel</button>
                <button type="button" class="btn btn-danger modal-confirm">Delete</button>
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

    .header-actions {
        display: flex;
        gap: 0.75rem;
    }

    /* Content Cards */
    .content-card {
        background-color: var(--highlight-color);
        border-radius: 0.5rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .section-title {
        font-size: 1.25rem;
        margin-bottom: 1rem;
        color: var(--secondary-color);
    }

    /* Assignment Method Cards */
    .assignment-methods {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }

    .method-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        background-color: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(229, 231, 235, 0.1);
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: left;
    }

    .method-card:hover {
        background-color: rgba(30, 144, 255, 0.05);
        border-color: rgba(30, 144, 255, 0.3);
    }

    .method-card.active {
        background-color: rgba(30, 144, 255, 0.1);
        border-color: var(--primary-color);
    }

    .method-icon {
        font-size: 1.5rem;
        width: 3rem;
        height: 3rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(30, 144, 255, 0.1);
        color: var(--primary-color);
        border-radius: 50%;
    }

    .method-title {
        font-size: 1.1rem;
        margin-bottom: 0.25rem;
        color: var(--secondary-color);
    }

    .method-desc {
        font-size: 0.875rem;
        color: var(--accent-color);
    }

    /* Form Styles */
    .form-section {
        margin-bottom: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-control {
        display: block;
        width: 100%;
        padding: 0.65rem 0.75rem;
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(229, 231, 235, 0.2);
        border-radius: 0.375rem;
        color: var(--secondary-color);
    }

    .form-control:focus {
        border-color: var(--primary-color);
        outline: none;
        box-shadow: 0 0 0 2px rgba(30, 144, 255, 0.2);
    }

    label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--secondary-color);
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
    }

    .form-text {
        display: block;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: var(--accent-color);
    }

    .form-error {
        color: #f56565;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(229, 231, 235, 0.1);
    }

    /* Table Styles */
    .assignments-table-container {
        overflow-x: auto;
    }

    .assignments-table {
        width: 100%;
        border-collapse: collapse;
    }

    .assignments-table th {
        text-align: left;
        padding: 0.75rem 1rem;
        font-weight: 600;
        color: var(--secondary-color);
        border-bottom: 1px solid rgba(229, 231, 235, 0.15);
    }

    .assignments-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(229, 231, 235, 0.1);
        color: var(--secondary-color);
    }

    .assignments-table tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.03);
    }

    .actions-cell {
        text-align: right;
        width: 100px;
    }

    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .status-badge.pending {
        background-color: rgba(236, 201, 75, 0.1);
        color: #ECC94B;
    }

    .status-badge.in_progress {
        background-color: rgba(66, 153, 225, 0.1);
        color: #4299E1;
    }

    .status-badge.completed {
        background-color: rgba(72, 187, 120, 0.1);
        color: #48BB78;
    }

    /* Search Box */
    .search-wrapper {
        position: relative;
    }

    .search-input {
        padding: 0.5rem 0.75rem;
        padding-left: 2.25rem;
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(229, 231, 235, 0.2);
        border-radius: 0.375rem;
        color: var(--secondary-color);
        width: 250px;
    }

    .search-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--accent-color);
    }

    /* Empty State */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2.5rem 1rem;
        text-align: center;
        color: var(--accent-color);
    }

    .empty-state i {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: var(--accent-color);
    }

    .empty-state .hint {
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    /* Button Styles */
    .btn-icon {
        background: none;
        border: none;
        padding: 0.5rem;
        font-size: 0.9rem;
        color: var(--accent-color);
        cursor: pointer;
        border-radius: 0.25rem;
        transition: all 0.2s;
    }

    .btn-icon:hover {
        color: var(--primary-color);
        background-color: rgba(255, 255, 255, 0.05);
    }

    .delete-btn:hover {
        color: #F56565;
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

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding: 1.25rem 1.5rem;
        border-top: 1px solid rgba(229, 231, 235, 0.1);
    }

    .warning {
        color: #F56565;
        font-weight: 500;
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

        .assignment-methods {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle between manual and random assignment
        const manualAssignmentBtn = document.getElementById('manualAssignmentBtn');
        const randomAssignmentBtn = document.getElementById('randomAssignmentBtn');
        const manualAssignmentForm = document.getElementById('manualAssignmentForm');
        const randomAssignmentForm = document.getElementById('randomAssignmentForm');

        manualAssignmentBtn.addEventListener('click', function() {
            manualAssignmentBtn.classList.add('active');
            randomAssignmentBtn.classList.remove('active');
            manualAssignmentForm.style.display = 'block';
            randomAssignmentForm.style.display = 'none';
        });

        randomAssignmentBtn.addEventListener('click', function() {
            randomAssignmentBtn.classList.add('active');
            manualAssignmentBtn.classList.remove('active');
            randomAssignmentForm.style.display = 'block';
            manualAssignmentForm.style.display = 'none';
        });

        // Search functionality
        const searchInput = document.getElementById('assignmentSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('.assignments-table tbody tr');
                
                rows.forEach(row => {
                    if (row.classList.contains('empty-row')) {
                        return;
                    }
                    
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // Delete confirmation modal
        const deleteModal = document.getElementById('deleteModal');
        const deleteBtns = document.querySelectorAll('.delete-btn');
        const modalCancel = document.querySelector('.modal-cancel');
        const modalConfirm = document.querySelector('.modal-confirm');
        const closeModal = document.querySelector('.close-modal');
        let currentForm = null;

        deleteBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                currentForm = this.closest('form');
                deleteModal.style.display = 'flex';
            });
        });

        if (modalCancel) {
            modalCancel.addEventListener('click', function() {
                deleteModal.style.display = 'none';
                currentForm = null;
            });
        }

        if (closeModal) {
            closeModal.addEventListener('click', function() {
                deleteModal.style.display = 'none';
                currentForm = null;
            });
        }

        if (modalConfirm) {
            modalConfirm.addEventListener('click', function() {
                if (currentForm) {
                    currentForm.submit();
                }
            });
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                deleteModal.style.display = 'none';
                currentForm = null;
            }
        });
    });
</script>
@endsection 