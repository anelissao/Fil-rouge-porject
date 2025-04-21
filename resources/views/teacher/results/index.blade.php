@extends('layouts.app')

@section('title', 'Results Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Brief Results Overview</h1>
            <p class="text-gray-600">Comprehensive overview of all briefs and their evaluation results</p>
        </div>
        <div>
            <a href="{{ route('teacher.dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Briefs</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalBriefs }}</p>
                </div>
                <div class="bg-blue-100 p-2 rounded-md">
                    <i class="fas fa-file-alt text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Submissions</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalSubmissions }}</p>
                </div>
                <div class="bg-green-100 p-2 rounded-md">
                    <i class="fas fa-paper-plane text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Evaluations</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalEvaluations }}</p>
                </div>
                <div class="bg-purple-100 p-2 rounded-md">
                    <i class="fas fa-clipboard-check text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Briefs Results Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-lg font-medium text-gray-800">All Briefs</h2>
            <p class="text-sm text-gray-600">Click on a brief to view detailed results</p>
        </div>
        
        <div class="p-6">
            @if(count($briefs) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brief Title</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submissions</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($briefs as $brief)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                        {{ $brief->title }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            {{ $brief->is_published ? 
                                                ($brief->end_date && $brief->end_date < now() ? 
                                                    'bg-gray-100 text-gray-800' : 
                                                    'bg-green-100 text-green-800') : 
                                                'bg-yellow-100 text-yellow-800' 
                                            }}">
                                            {{ $brief->is_published ? 
                                                ($brief->end_date && $brief->end_date < now() ? 
                                                    'Ended' : 
                                                    'Active') : 
                                                'Draft' 
                                            }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $brief->submissions_count ?? 0 }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        @if(isset($brief->completion_percentage))
                                            <div class="flex items-center">
                                                <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $brief->completion_percentage }}%"></div>
                                                </div>
                                                <span>{{ $brief->completion_percentage }}%</span>
                                            </div>
                                        @else
                                            <span>N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $brief->updated_at->diffForHumans() }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('teacher.results.show', $brief->id) }}" class="text-blue-600 hover:text-blue-800 transition">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </a>
                                            
                                            <a href="{{ route('teacher.results.export', $brief->id) }}" class="text-green-600 hover:text-green-800 transition">
                                                <i class="fas fa-download mr-1"></i>Export
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-10">
                    <div class="text-gray-400 mb-3">
                        <i class="fas fa-folder-open text-5xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">No briefs found</h3>
                    <p class="text-gray-500">Create a brief to get started with student evaluations.</p>
                    <div class="mt-5">
                        <a href="{{ route('teacher.briefs.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                            <i class="fas fa-plus mr-2"></i>Create Brief
                        </a>
                    </div>
                </div>
            @endif
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

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
    }

    .stat-card {
        display: flex;
        align-items: center;
        padding: 1.25rem;
        background-color: rgba(255, 255, 255, 0.03);
        border-radius: 0.5rem;
        border: 1px solid rgba(229, 231, 235, 0.1);
    }

    .stat-icon {
        width: 3rem;
        height: 3rem;
        background-color: rgba(30, 144, 255, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        color: var(--primary-color);
        font-size: 1.25rem;
    }

    .stat-content {
        flex: 1;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        color: var(--secondary-color);
    }

    .stat-label {
        color: var(--accent-color);
        font-size: 0.875rem;
    }

    /* Search Box */
    .search-wrapper {
        position: relative;
    }

    .search-input {
        padding: 0.65rem 0.75rem;
        padding-left: 2.5rem;
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(229, 231, 235, 0.2);
        border-radius: 0.375rem;
        color: var(--secondary-color);
        width: 250px;
    }

    .search-icon {
        position: absolute;
        left: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--accent-color);
    }

    /* Table Styles */
    .table-container {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th {
        text-align: left;
        padding: 0.75rem 1rem;
        font-weight: 600;
        color: var(--secondary-color);
        border-bottom: 1px solid rgba(229, 231, 235, 0.15);
    }

    .data-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(229, 231, 235, 0.1);
        color: var(--secondary-color);
    }

    .data-table tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.03);
    }

    .sortable {
        cursor: pointer;
        position: relative;
    }

    .sortable:after {
        content: '↕';
        position: absolute;
        right: 0.5rem;
        color: var(--accent-color);
        opacity: 0.5;
    }

    .sortable.asc:after {
        content: '↑';
        opacity: 1;
    }

    .sortable.desc:after {
        content: '↓';
        opacity: 1;
    }

    .actions-cell {
        text-align: right;
        white-space: nowrap;
    }

    /* Button Styles */
    .btn-icon {
        background: none;
        border: none;
        padding: 0.5rem;
        margin-left: 0.25rem;
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

    /* Export Options */
    .export-options {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-top: 1rem;
    }

    .export-option {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.5rem 1rem;
        background-color: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(229, 231, 235, 0.1);
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.3s;
    }

    .export-option:hover {
        background-color: rgba(30, 144, 255, 0.05);
        border-color: var(--primary-color);
    }

    .export-option.selected {
        background-color: rgba(30, 144, 255, 0.1);
        border-color: var(--primary-color);
    }

    .export-option i {
        font-size: 2rem;
        margin-bottom: 0.75rem;
        color: var(--primary-color);
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        margin-bottom: 0.5rem;
    }

    .mt-4 {
        margin-top: 1rem;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            gap: 1rem;
        }

        .header-actions {
            width: 100%;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .export-options {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('briefSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('.data-table tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // Sorting functionality
        const sortableHeaders = document.querySelectorAll('.sortable');
        sortableHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const sortBy = this.dataset.sort;
                const isAsc = this.classList.contains('asc');
                
                // Remove existing sort classes
                sortableHeaders.forEach(h => {
                    h.classList.remove('asc', 'desc');
                });
                
                // Set new sort class
                this.classList.add(isAsc ? 'desc' : 'asc');
                
                // Get table body and rows
                const tbody = this.closest('table').querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));
                
                // Sort rows
                rows.sort((a, b) => {
                    let aValue = a.children[Array.from(a.parentNode.children[0].children).indexOf(this)].textContent.trim();
                    let bValue = b.children[Array.from(b.parentNode.children[0].children).indexOf(this)].textContent.trim();
                    
                    // Handle numeric values
                    if (sortBy === 'submissions' || sortBy === 'evaluated' || sortBy === 'completion') {
                        aValue = parseFloat(aValue.replace('%', '')) || 0;
                        bValue = parseFloat(bValue.replace('%', '')) || 0;
                    }
                    
                    // Handle date values
                    if (sortBy === 'created') {
                        aValue = new Date(aValue);
                        bValue = new Date(bValue);
                    }
                    
                    // Compare values
                    if (aValue < bValue) {
                        return isAsc ? -1 : 1;
                    }
                    if (aValue > bValue) {
                        return isAsc ? 1 : -1;
                    }
                    return 0;
                });
                
                // Reappend sorted rows
                rows.forEach(row => {
                    tbody.appendChild(row);
                });
            });
        });

        // Export functionality
        const exportModal = document.getElementById('exportModal');
        const exportBtns = document.querySelectorAll('.export-btn');
        const exportOptions = document.querySelectorAll('.export-option');
        const exportCancel = document.querySelector('.modal-cancel');
        const exportConfirm = document.querySelector('.export-confirm');
        const closeModalBtn = document.querySelector('.close-modal');
        let currentBriefId = null;
        let selectedFormat = null;

        exportBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                currentBriefId = this.dataset.id;
                exportModal.style.display = 'flex';
                selectedFormat = null;
                
                // Reset selected format
                exportOptions.forEach(option => {
                    option.classList.remove('selected');
                });
            });
        });

        exportOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Remove selected class from all options
                exportOptions.forEach(opt => {
                    opt.classList.remove('selected');
                });
                
                // Add selected class to clicked option
                this.classList.add('selected');
                selectedFormat = this.dataset.format;
            });
        });

        if (exportCancel) {
            exportCancel.addEventListener('click', function() {
                exportModal.style.display = 'none';
                currentBriefId = null;
            });
        }

        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', function() {
                exportModal.style.display = 'none';
                currentBriefId = null;
            });
        }

        if (exportConfirm) {
            exportConfirm.addEventListener('click', function() {
                if (currentBriefId && selectedFormat) {
                    const includeComments = document.getElementById('includeComments').checked;
                    const anonymizeStudents = document.getElementById('anonymizeStudents').checked;
                    
                    // Build export URL
                    const url = `/teacher/results/${currentBriefId}/export?format=${selectedFormat}` +
                               `&include_comments=${includeComments}` +
                               `&anonymize=${anonymizeStudents}`;
                    
                    // Open export URL in new tab/window
                    window.open(url, '_blank');
                    
                    // Close modal
                    exportModal.style.display = 'none';
                    currentBriefId = null;
                } else {
                    alert('Please select an export format.');
                }
            });
        }

        // Close modals when clicking outside
        window.addEventListener('click', function(e) {
            if (e.target === exportModal) {
                exportModal.style.display = 'none';
                currentBriefId = null;
            }
        });
    });
</script>
@endsection 