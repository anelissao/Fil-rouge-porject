@extends('layouts.app')

@section('title', 'Evaluations')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section with Gradient Background -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-lg mb-8 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div class="mb-4 md:mb-0">
                <h1 class="text-3xl font-bold text-white mb-2">Evaluations Management</h1>
                <p class="text-blue-100">Manage and track peer evaluations</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('teacher.evaluations.assign') }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg backdrop-blur-sm transition-all duration-300 border border-white/20">
                    <i class="fas fa-plus mr-2"></i>Assign Evaluations
                </a>
                <a href="{{ route('teacher.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg backdrop-blur-sm transition-all duration-300 border border-white/20">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700">
        <div class="border-b border-gray-700 px-6 py-4">
            <div class="flex flex-col md:flex-row gap-4 items-end mb-6">
                <div class="flex-1">
                    <div class="relative">
                        <input 
                            type="text" 
                            id="evaluationSearch" 
                            placeholder="Search evaluations..." 
                            class="pl-10 pr-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full text-white"
                        >
                        <div class="absolute left-3 top-2.5 text-gray-500">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                </div>
                
                <div class="w-full md:w-48">
                    <div class="relative">
                        <select id="statusFilter" class="pl-3 pr-10 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full text-white appearance-none">
                            <option value="all">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-gray-500">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>
                
                <div class="w-full md:w-48">
                    <div class="relative">
                        <select id="briefFilter" class="pl-3 pr-10 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full text-white appearance-none">
                            <option value="all">All Briefs</option>
                            @foreach($briefs ?? [] as $brief)
                                <option value="{{ $brief->id }}">{{ $brief->title }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-gray-500">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gray-750 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-white">{{ $evaluationStats['total'] ?? 0 }}</div>
                    <div class="text-sm text-gray-400">Total</div>
                </div>
                <div class="bg-gray-750 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-yellow-400">{{ $evaluationStats['pending'] ?? 0 }}</div>
                    <div class="text-sm text-gray-400">Pending</div>
                </div>
                <div class="bg-gray-750 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-blue-400">{{ $evaluationStats['in_progress'] ?? 0 }}</div>
                    <div class="text-sm text-gray-400">In Progress</div>
                </div>
                <div class="bg-gray-750 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-green-400">{{ $evaluationStats['completed'] ?? 0 }}</div>
                    <div class="text-sm text-gray-400">Completed</div>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Evaluator</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Brief</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Assigned Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Completion Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($evaluations as $evaluation)
                        <tr class="hover:bg-gray-750 transition-colors duration-200" data-status="{{ $evaluation->status }}" data-brief="{{ $evaluation->submission->brief_id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full bg-gray-700 text-gray-400">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-white">{{ $evaluation->evaluator->username }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full bg-gray-700 text-gray-400">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-white">{{ $evaluation->submission->student->username }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-white">{{ $evaluation->submission->brief->title }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center
                                    {{ $evaluation->status == 'completed' ? 'bg-green-900/30 text-green-400' : 
                                       ($evaluation->status == 'in_progress' ? 'bg-blue-900/30 text-blue-400' : 
                                       'bg-yellow-900/30 text-yellow-400') }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 
                                        {{ $evaluation->status == 'completed' ? 'bg-green-400' : 
                                           ($evaluation->status == 'in_progress' ? 'bg-blue-400' : 
                                           'bg-yellow-400') }}"></span>
                                    {{ ucfirst($evaluation->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                {{ $evaluation->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                {{ $evaluation->due_date ? $evaluation->due_date->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                {{ $evaluation->completed_at ? $evaluation->completed_at->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('teacher.evaluations.show', $evaluation->id) }}" class="text-blue-400 hover:text-blue-300 transition-colors p-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="text-yellow-400 hover:text-yellow-300 transition-colors p-1 remind-btn" data-id="{{ $evaluation->id }}">
                                        <i class="fas fa-bell"></i>
                                    </button>
                                    <form action="{{ route('teacher.evaluations.cancel', $evaluation->id) }}" 
                                          method="POST" class="inline-block delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="text-red-400 hover:text-red-300 transition-colors p-1 delete-btn">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-700 text-gray-500 mb-4">
                                    <i class="fas fa-clipboard-check text-3xl"></i>
                                </div>
                                <p class="text-lg font-medium text-white mb-2">No evaluations found</p>
                                <p class="text-gray-400 max-w-md mx-auto mb-6">There are no evaluations assigned yet.</p>
                                <a href="{{ route('teacher.evaluations.assign') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                    <i class="fas fa-plus mr-2"></i>Assign Evaluations
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-700">
            {{ $evaluations->links() }}
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-gray-800 rounded-xl max-w-md w-full p-6 border border-gray-700">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-white">Confirm Deletion</h3>
                <button type="button" class="text-gray-400 hover:text-white close-modal">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="mb-6">
                <p class="text-gray-300 mb-2">Are you sure you want to delete this evaluation?</p>
                <p class="text-red-400 text-sm">This action cannot be undone.</p>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors modal-cancel">Cancel</button>
                <button type="button" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors modal-confirm">Delete</button>
            </div>
        </div>
    </div>

    <!-- Reminder Modal -->
    <div id="reminderModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-gray-800 rounded-xl max-w-md w-full p-6 border border-gray-700">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-white">Send Reminder</h3>
                <button type="button" class="text-gray-400 hover:text-white close-modal">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="mb-6">
                <p class="text-gray-300 mb-4">Send a reminder to the student about this evaluation?</p>
                <div class="mb-4">
                    <label for="reminderMessage" class="block text-sm font-medium text-gray-400 mb-2">Custom Message (Optional)</label>
                    <textarea id="reminderMessage" rows="3" placeholder="Add a personal message..." class="w-full px-3 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white"></textarea>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors modal-cancel">Cancel</button>
                <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors reminder-confirm">Send Reminder</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search and filter functionality
        const searchInput = document.getElementById('evaluationSearch');
        const statusFilter = document.getElementById('statusFilter');
        const briefFilter = document.getElementById('briefFilter');
        const tableRows = document.querySelectorAll('tbody tr:not([colspan])');
        
        function filterEvaluations() {
            const searchTerm = searchInput.value.toLowerCase();
            const statusValue = statusFilter.value;
            const briefValue = briefFilter.value;
            
            tableRows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                const rowStatus = row.dataset.status;
                const rowBrief = row.dataset.brief;
                
                const matchesSearch = rowText.includes(searchTerm);
                const matchesStatus = statusValue === 'all' || rowStatus === statusValue;
                const matchesBrief = briefValue === 'all' || rowBrief === briefValue;
                
                if (matchesSearch && matchesStatus && matchesBrief) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        if (searchInput) searchInput.addEventListener('keyup', filterEvaluations);
        if (statusFilter) statusFilter.addEventListener('change', filterEvaluations);
        if (briefFilter) briefFilter.addEventListener('change', filterEvaluations);
        
        // Modal functionality
        const deleteModal = document.getElementById('deleteModal');
        const reminderModal = document.getElementById('reminderModal');
        const deleteBtns = document.querySelectorAll('.delete-btn');
        const remindBtns = document.querySelectorAll('.remind-btn');
        const closeModalBtns = document.querySelectorAll('.close-modal');
        const modalCancelBtns = document.querySelectorAll('.modal-cancel');
        
        let currentForm = null;
        let currentEvaluationId = null;
        
        function openModal(modal) {
            modal.classList.remove('hidden');
        }
        
        function closeModal(modal) {
            modal.classList.add('hidden');
        }
        
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                currentForm = this.closest('form');
                openModal(deleteModal);
            });
        });
        
        remindBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                currentEvaluationId = this.dataset.id;
                openModal(reminderModal);
            });
        });
        
        closeModalBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                closeModal(this.closest('.fixed'));
            });
        });
        
        modalCancelBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                closeModal(this.closest('.fixed'));
            });
        });
        
        // Delete confirmation
        const confirmDeleteBtn = document.querySelector('.modal-confirm');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function() {
                if (currentForm) {
                    currentForm.submit();
                }
                closeModal(deleteModal);
            });
        }
        
        // Reminder confirmation
        const confirmReminderBtn = document.querySelector('.reminder-confirm');
        if (confirmReminderBtn) {
            confirmReminderBtn.addEventListener('click', function() {
                const message = document.getElementById('reminderMessage').value;
                
                // Send reminder via AJAX
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
                    } else {
                        // Show error notification
                        alert('Failed to send reminder: ' + data.message);
                    }
                    closeModal(reminderModal);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while sending the reminder.');
                    closeModal(reminderModal);
                });
            });
        }
    });
</script>
@endsection 