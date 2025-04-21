@extends('layouts.app')

@section('title', 'Submissions')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Submissions Management</h1>
            <p class="page-subtitle">View and manage student submissions</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('teacher.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="content-card">
        <div class="card-header">
            <div class="filter-controls">
                <div class="search-wrapper">
                    <input type="text" id="submissionSearch" class="search-input" placeholder="Search submissions...">
                    <i class="fas fa-search search-icon"></i>
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
        </div>
        
        <div class="submissions-table-container">
            <table class="submissions-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Brief</th>
                        <th>Submitted At</th>
                        <th>Status</th>
                        <th>Evaluations</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $submission)
                        <tr>
                            <td>
                                {{ $submission->student->username }}
                            </td>
                            <td>
                                {{ $submission->brief->title }}
                            </td>
                            <td>
                                {{ $submission->created_at->format('M d, Y') }}
                            </td>
                            <td>
                                <span class="status-badge completed">
                                    Submitted
                                </span>
                            </td>
                            <td>
                                {{ $submission->evaluations_count ?? $submission->evaluations->count() }} evaluations
                            </td>
                            <td class="actions-cell">
                                <a href="{{ route('teacher.submissions.show', $submission->id) }}" class="btn-icon" title="View Submission">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="6" class="empty-table">
                                <div class="empty-state">
                                    <i class="fas fa-file-upload"></i>
                                    <p>No submissions found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="pagination-container">
            {{ $submissions->links() }}
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

    /* Table Styles */
    .submissions-table-container {
        overflow-x: auto;
    }

    .submissions-table {
        width: 100%;
        border-collapse: collapse;
    }

    .submissions-table th {
        text-align: left;
        padding: 1rem 1.25rem;
        font-weight: 600;
        color: var(--secondary-color);
        white-space: nowrap;
    }

    .submissions-table td {
        padding: 1rem 1.25rem;
        border-top: 1px solid rgba(229, 231, 235, 0.1);
        color: var(--secondary-color);
    }

    .submissions-table tbody tr:hover {
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
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('submissionSearch');
        const briefFilter = document.getElementById('briefFilter');
        const rows = document.querySelectorAll('.submissions-table tbody tr:not(.empty-row)');

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const briefValue = briefFilter.value;
            
            let hasVisibleRows = false;
            
            rows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                const briefId = row.dataset.brief;
                
                const matchesSearch = rowText.includes(searchTerm);
                const matchesBrief = briefValue === 'all' || briefId === briefValue;
                
                const shouldShow = matchesSearch && matchesBrief;
                
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
        
        if (briefFilter) {
            briefFilter.addEventListener('change', filterTable);
        }
    });
</script>
@endsection 