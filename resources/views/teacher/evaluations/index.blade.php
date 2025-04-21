@extends('layouts.app')

@section('title', 'Evaluations')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Evaluations Management</h1>
            <p class="page-subtitle">Manage and track peer evaluations</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('teacher.evaluations.assign') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Assign Evaluations
            </a>
            <a href="{{ route('teacher.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="content-card">
        <div class="card-header">
            <div class="filter-controls">
                <div class="search-wrapper">
                    <input type="text" id="evaluationSearch" class="search-input" placeholder="Search evaluations...">
                    <i class="fas fa-search search-icon"></i>
                </div>
                
                <div class="filter-dropdown">
                    <select id="statusFilter" class="filter-select">
                        <option value="all">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                    <label for="statusFilter">Status</label>
                </div>
                
                <div class="filter-dropdown">
                    <select id="briefFilter" class="filter-select">
                        <option value="all">All Briefs</option>
                        @foreach($briefs ?? [] as $brief)
                            <option value="{{ $brief->id }}">{{ $brief->title }}</option>
                        @endforeach
                    </select>
                    <label for="briefFilter">Brief</label>
                </div>
            </div>
            
            <div class="summary-stats">
                <div class="stat-item">
                    <span class="stat-value">{{ $evaluationStats['total'] ?? 0 }}</span>
                    <span class="stat-label">Total</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">{{ $evaluationStats['pending'] ?? 0 }}</span>
                    <span class="stat-label">Pending</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">{{ $evaluationStats['in_progress'] ?? 0 }}</span>
                    <span class="stat-label">In Progress</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">{{ $evaluationStats['completed'] ?? 0 }}</span>
                    <span class="stat-label">Completed</span>
                </div>
            </div>
        </div>
        
        <div class="evaluations-table-container">
            <table class="evaluations-table">
                <thead>
                    <tr>
                        <th>Evaluator</th>
                        <th>Student</th>
                        <th>Brief</th>
                        <th>Status</th>
                        <th>Assigned Date</th>
                        <th>Due Date</th>
                        <th>Completion Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($evaluations as $evaluation)
                        <tr data-status="{{ $evaluation->status }}" data-brief="{{ $evaluation->submission->brief_id }}">
                            <td>
                                {{ $evaluation->evaluator->username }}
                            </td>
                            <td>
                                {{ $evaluation->submission->student->username }}
                            </td>
                            <td>
                                {{ $evaluation->submission->brief->title }}
                            </td>
                            <td>
                                <span class="status-badge {{ $evaluation->status }}">
                                    {{ ucfirst($evaluation->status) }}
                                </span>
                            </td>
                            <td>
                                {{ $evaluation->created_at->format('M d, Y') }}
                            </td>
                            <td>
                                {{ $evaluation->due_date ? $evaluation->due_date->format('M d, Y') : 'N/A' }}
                            </td>
                            <td>
                                {{ $evaluation->completed_at ? $evaluation->completed_at->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="actions-cell">
                                <a href="{{ route('teacher.evaluations.show', $evaluation->id) }}" class="btn-icon" title="View Evaluation">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button" class="btn-icon remind-btn" data-id="{{ $evaluation->id }}" title="Send Reminder">
                                    <i class="fas fa-bell"></i>
                                </button>
                                <form action="{{ route('teacher.evaluations.destroy', $evaluation->id) }}" 
                                      method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-icon delete-btn" title="Delete Evaluation">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="8" class="empty-table">
                                <div class="empty-state">
                                    <i class="fas fa-clipboard-check"></i>
                                    <p>No evaluations found.</p>
                                    <a href="{{ route('teacher.evaluations.assign') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Assign Evaluations
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="pagination-container">
            {{ $evaluations->links() }}
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
                <p>Are you sure you want to delete this evaluation?</p>
                <p class="warning">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline modal-cancel">Cancel</button>
                <button type="button" class="btn btn-danger modal-confirm">Delete</button>
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
                <p>Send a reminder to the student about this evaluation?</p>
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

    .header-actions {
        display: flex;
        gap: 0.75rem;
    }

    /* Content Card */
    .content-card {
        background-color: var(--highlight-color);
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        padding: 1.5rem;
        border-bottom: 1px solid rgba(229, 231, 235, 0.1);
    }

    /* Filter Controls */
    .filter-controls {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
    }

    .search-wrapper {
        position: relative;
        flex: 1;
        min-width: 200px;
    }

    .search-input {
        padding: 0.65rem 0.75rem;
        padding-left: 2.5rem;
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(229, 231, 235, 0.2);
        border-radius: 0.375rem;
        color: var(--secondary-color);
        width: 100%;
    }

    .search-icon {
        position: absolute;
        left: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--accent-color);
    }

    .filter-dropdown {
        position: relative;
    }

    .filter-select {
        padding: 0.65rem 2.5rem 0.65rem 0.75rem;
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(229, 231, 235, 0.2);
        border-radius: 0.375rem;
        color: var(--secondary-color);
        appearance: none;
        min-width: 150px;
        cursor: pointer;
    }

    .filter-dropdown label {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--accent-color);
        font-size: 0.75rem;
        pointer-events: none;
    }

    /* Summary Stats */
    .summary-stats {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .stat-item {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--secondary-color);
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--accent-color);
    }

    /* Table Styles */
    .evaluations-table-container {
        overflow-x: auto;
    }

    .evaluations-table {
        width: 100%;
        border-collapse: collapse;
    }

    .evaluations-table th {
        text-align: left;
        padding: 1rem 1.25rem;
        font-weight: 600;
        color: var(--secondary-color);
        white-space: nowrap;
    }

    .evaluations-table td {
        padding: 1rem 1.25rem;
        border-top: 1px solid rgba(229, 231, 235, 0.1);
        color: var(--secondary-color);
    }

    .evaluations-table tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.03);
    }

    .actions-cell {
        white-space: nowrap;
        text-align: right;
    }

    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 2rem;
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

    /* Empty State */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem 1rem;
        text-align: center;
        color: var(--accent-color);
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1.5rem;
        color: var(--accent-color);
    }

    .empty-state p {
        margin-bottom: 1.5rem;
    }

    /* Pagination */
    .pagination-container {
        padding: 1.5rem;
        display: flex;
        justify-content: center;
    }

    /* Button Styles */
    .btn-icon {
        background: none;
        border: none;
        padding: 0.5rem;
        margin-left: 0.5rem;
        font-size: 0.95rem;
        color: var(--accent-color);
        cursor: pointer;
        border-radius: 0.25rem;
        transition: all 0.2s;
    }

    .btn-icon:hover {
        color: var(--primary-color);
        background-color: rgba(255, 255, 255, 0.05);
    }

    .remind-btn:hover {
        color: #ECC94B;
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

        .filter-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .summary-stats {
            justify-content: space-around;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('evaluationSearch');
        const statusFilter = document.getElementById('statusFilter');
        const briefFilter = document.getElementById('briefFilter');
        const rows = document.querySelectorAll('.evaluations-table tbody tr:not(.empty-row)');

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const statusValue = statusFilter.value;
            const briefValue = briefFilter.value;
            
            let hasVisibleRows = false;
            
            rows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                const rowStatus = row.dataset.status;
                const rowBrief = row.dataset.brief;
                
                const matchesSearch = rowText.includes(searchTerm);
                const matchesStatus = statusValue === 'all' || rowStatus === statusValue;
                const matchesBrief = briefValue === 'all' || rowBrief === briefValue;
                
                const shouldShow = matchesSearch && matchesStatus && matchesBrief;
                
                row.style.display = shouldShow ? '' : 'none';
                
                if (shouldShow) {
                    hasVisibleRows = true;
                }
            });
            
            // Show empty message if no matches
            const emptyRow = document.querySelector('.empty-row');
            if (emptyRow) {
                emptyRow.style.display = hasVisibleRows ? 'none' : '';
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', filterTable);
        }
        
        if (statusFilter) {
            statusFilter.addEventListener('change', filterTable);
        }
        
        if (briefFilter) {
            briefFilter.addEventListener('change', filterTable);
        }

        // Delete confirmation modal
        const deleteModal = document.getElementById('deleteModal');
        const deleteBtns = document.querySelectorAll('.delete-btn');
        const modalCancel = document.querySelector('#deleteModal .modal-cancel');
        const modalConfirm = document.querySelector('#deleteModal .modal-confirm');
        const closeModalBtn = document.querySelector('#deleteModal .close-modal');
        let currentDeleteForm = null;

        deleteBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                currentDeleteForm = this.closest('form');
                deleteModal.style.display = 'flex';
            });
        });

        if (modalCancel) {
            modalCancel.addEventListener('click', function() {
                deleteModal.style.display = 'none';
                currentDeleteForm = null;
            });
        }

        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', function() {
                deleteModal.style.display = 'none';
                currentDeleteForm = null;
            });
        }

        if (modalConfirm) {
            modalConfirm.addEventListener('click', function() {
                if (currentDeleteForm) {
                    currentDeleteForm.submit();
                }
            });
        }

        // Reminder modal
        const reminderModal = document.getElementById('reminderModal');
        const remindBtns = document.querySelectorAll('.remind-btn');
        const reminderCancel = document.querySelector('#reminderModal .modal-cancel');
        const reminderConfirm = document.querySelector('#reminderModal .reminder-confirm');
        const closeReminderBtn = document.querySelector('#reminderModal .close-modal');
        let currentReminderEvalId = null;

        remindBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                currentReminderEvalId = this.dataset.id;
                reminderModal.style.display = 'flex';
            });
        });

        if (reminderCancel) {
            reminderCancel.addEventListener('click', function() {
                reminderModal.style.display = 'none';
                currentReminderEvalId = null;
            });
        }

        if (closeReminderBtn) {
            closeReminderBtn.addEventListener('click', function() {
                reminderModal.style.display = 'none';
                currentReminderEvalId = null;
            });
        }

        if (reminderConfirm) {
            reminderConfirm.addEventListener('click', function() {
                if (currentReminderEvalId) {
                    const message = document.getElementById('reminderMessage').value;
                    
                    // Send reminder via AJAX
                    fetch(`/teacher/evaluations/${currentReminderEvalId}/remind`, {
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
                        currentReminderEvalId = null;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                        reminderModal.style.display = 'none';
                        currentReminderEvalId = null;
                    });
                }
            });
        }

        // Close modals when clicking outside
        window.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                deleteModal.style.display = 'none';
                currentDeleteForm = null;
            }
            if (e.target === reminderModal) {
                reminderModal.style.display = 'none';
                currentReminderEvalId = null;
            }
        });
    });
</script>
@endsection 