@extends('layouts.app')

@section('title', 'My Evaluations')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section with Gradient Background -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-lg mb-8 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div class="mb-4 md:mb-0">
                <h1 class="text-3xl font-bold text-white mb-2">My Evaluations</h1>
                <p class="text-blue-100">Manage and complete your assigned peer evaluations</p>
            </div>
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg backdrop-blur-sm transition-all duration-300 border border-white/20">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="mb-6 border-b border-gray-700">
        <nav class="flex flex-wrap -mb-px">
            <button class="tab-button mr-2 py-3 px-4 border-b-2 border-transparent text-gray-400 hover:text-blue-400 hover:border-blue-400 transition-colors duration-200 font-medium active" data-target="to-complete">
                <span class="inline-flex items-center">
                    <i class="fas fa-tasks mr-2"></i>
                    <span>To Complete</span>
                    @if(isset($assignedEvaluations) && count($assignedEvaluations) > 0)
                    <span class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-blue-600 rounded-full">{{ count($assignedEvaluations) }}</span>
                    @endif
                </span>
            </button>
            <button class="tab-button mr-2 py-3 px-4 border-b-2 border-transparent text-gray-400 hover:text-blue-400 hover:border-blue-400 transition-colors duration-200 font-medium" data-target="received">
                <span class="inline-flex items-center">
                    <i class="fas fa-inbox mr-2"></i>
                    <span>Received</span>
                    @if(isset($receivedEvaluations) && count($receivedEvaluations) > 0)
                    <span class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-green-600 rounded-full">{{ count($receivedEvaluations) }}</span>
                    @endif
                </span>
            </button>
            <button class="tab-button py-3 px-4 border-b-2 border-transparent text-gray-400 hover:text-blue-400 hover:border-blue-400 transition-colors duration-200 font-medium" data-target="all">
                <span class="inline-flex items-center">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    <span>All Evaluations</span>
                </span>
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- To Complete Tab -->
        <div id="to-complete" class="tab-pane">
            @if(isset($assignedEvaluations) && count($assignedEvaluations) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($assignedEvaluations as $evaluation)
                        <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden transform hover:scale-[1.02] transition-all duration-300 border-l-4 
                            {{ $evaluation->due_at && Carbon\Carbon::parse($evaluation->due_at)->isPast() ? 'border-red-500' : 'border-yellow-500' }}">
                            <div class="p-6">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-xl font-bold text-white mb-2 truncate">{{ $evaluation->submission->brief->title }}</h3>
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center
                                        {{ $evaluation->due_at && Carbon\Carbon::parse($evaluation->due_at)->isPast() ? 'bg-red-900/30 text-red-400' : 'bg-yellow-900/30 text-yellow-400' }}">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 
                                            {{ $evaluation->due_at && Carbon\Carbon::parse($evaluation->due_at)->isPast() ? 'bg-red-400' : 'bg-yellow-400' }}"></span>
                                        {{ $evaluation->due_at && Carbon\Carbon::parse($evaluation->due_at)->isPast() ? 'Overdue' : 'Pending' }}
                                    </span>
                                </div>
                                
                                <div class="mt-3 mb-4">
                                    <div class="flex items-center mb-2">
                                        <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full bg-gray-700 text-gray-400">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="ml-2">
                                            <p class="text-sm font-medium text-white">{{ $evaluation->submission->student->username }}</p>
                                            <p class="text-xs text-gray-400">Submitted {{ $evaluation->submission->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    
                                    @if($evaluation->due_at)
                                    <div class="text-sm text-gray-400">
                                        <span class="inline-flex items-center">
                                            <i class="far fa-calendar-alt mr-1"></i>
                                            <span>Due: {{ Carbon\Carbon::parse($evaluation->due_at)->format('M d, Y') }}</span>
                                        </span>
                                    </div>
                                    @endif
                                </div>
                                
                                <div class="mt-4 flex justify-between items-center">
                                    <span class="text-xs text-gray-400">{{ count($evaluation->submission->brief->criteria) }} criteria to evaluate</span>
                                    <a href="{{ route('student.evaluations.edit', $evaluation->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition-colors duration-300">
                                        <i class="fas fa-edit mr-2"></i>
                                        <span>Complete</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-gray-800 rounded-xl border border-gray-700">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-700 text-gray-500 mb-4">
                        <i class="fas fa-clipboard-check text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">No pending evaluations</h3>
                    <p class="text-gray-400 max-w-md mx-auto">You've completed all your assigned evaluations. Great job!</p>
                </div>
            @endif
        </div>

        <!-- Received Tab -->
        <div id="received" class="tab-pane hidden">
            @if(isset($receivedEvaluations) && count($receivedEvaluations) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($receivedEvaluations as $evaluation)
                        <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden transform hover:scale-[1.02] transition-all duration-300 border-l-4 border-green-500">
                <div class="p-6">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-xl font-bold text-white mb-2 truncate">{{ $evaluation->submission->brief->title }}</h3>
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center bg-green-900/30 text-green-400">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-green-400"></span>
                                        {{ $evaluation->status === 'completed' ? 'Completed' : 'Pending' }}
                                    </span>
                                </div>
                                
                                <div class="mt-3 mb-4">
                                    <div class="flex items-center mb-2">
                                        <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full bg-gray-700 text-gray-400">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="ml-2">
                                            <p class="text-sm font-medium text-white">{{ $evaluation->evaluator->username }}</p>
                                            <p class="text-xs text-gray-400">
                                                {{ $evaluation->status === 'completed' ? 
                                                    'Evaluated ' . Carbon\Carbon::parse($evaluation->completed_at)->diffForHumans() : 
                                                    'Assigned ' . $evaluation->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    @if($evaluation->status === 'completed')
                                    <div class="flex items-center mt-2">
                                        <span class="px-2.5 py-1 text-sm font-medium rounded-lg inline-flex items-center
                                            {{ $evaluation->average_score >= 4 ? 
                                                'bg-green-900/30 text-green-400' : 
                                                ($evaluation->average_score >= 3 ? 
                                                    'bg-blue-900/30 text-blue-400' : 
                                                    ($evaluation->average_score >= 2 ? 
                                                        'bg-yellow-900/30 text-yellow-400' : 
                                                        'bg-red-900/30 text-red-400'))
                                            }}">
                                            <i class="fas fa-star mr-1"></i>
                                            {{ number_format($evaluation->average_score, 1) }}
                                        </span>
                                        <span class="text-sm text-gray-400 ml-2">Average score</span>
                                    </div>
                                    @endif
                                </div>
                                
                                <div class="mt-4 flex justify-end">
                                    @if($evaluation->status === 'completed')
                                    <a href="{{ route('student.evaluations.show', $evaluation->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition-colors duration-300">
                                        <i class="fas fa-eye mr-2"></i>
                                        <span>View</span>
                                    </a>
                                    @else
                                    <span class="px-4 py-2 bg-gray-700 text-gray-400 rounded-lg cursor-not-allowed">
                                        <i class="fas fa-hourglass-half mr-2"></i>
                                        <span>Awaiting Completion</span>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-gray-800 rounded-xl border border-gray-700">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-700 text-gray-500 mb-4">
                        <i class="fas fa-inbox text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">No evaluations received</h3>
                    <p class="text-gray-400 max-w-md mx-auto">You haven't received any peer evaluations yet. Check back later!</p>
                </div>
            @endif
        </div>
        
        <!-- All Evaluations Tab -->
        <div id="all" class="tab-pane hidden">
                    @if(count($evaluations) > 0)
                <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700">
                    <div class="border-b border-gray-700 px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-white">All Evaluations</h2>
                            <p class="text-sm text-gray-400">Complete overview of your evaluations</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <div class="relative">
                                <input type="text" id="evaluation-search" placeholder="Search evaluations..." class="pl-10 pr-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full md:w-auto text-white">
                                <div class="absolute left-3 top-2.5 text-gray-500">
                                    <i class="fas fa-search"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Brief</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Student</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Role</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Due Date</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-700 evaluation-rows">
                                    @foreach($evaluations as $evaluation)
                                        <tr class="hover:bg-gray-750 transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-lg bg-blue-900/30 text-blue-400">
                                                        <i class="fas fa-file-alt"></i>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-white">{{ Str::limit($evaluation->submission->brief->title, 40) }}</div>
                                                        <div class="text-xs text-gray-400">{{ $evaluation->submission->brief->created_at->format('M d, Y') }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-white">{{ $evaluation->submission->student->username }}</div>
                                                <div class="text-xs text-gray-400">Submission owner</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center
                                                    {{ $evaluation === $evaluation->submission->student ? 'bg-purple-900/30 text-purple-400' : 'bg-blue-900/30 text-blue-400' }}">
                                                    <i class="fas {{ $evaluation === $evaluation->submission->student ? 'fa-user-check' : 'fa-user-edit' }} mr-1"></i>
                                                    {{ $evaluation === $evaluation->submission->student ? 'Receiver' : 'Evaluator' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center
                                                    {{ $evaluation->status == 'completed' ? 
                                                        'bg-green-900/30 text-green-400' : 
                                                        ($evaluation->is_overdue ? 
                                                            'bg-red-900/30 text-red-400' : 
                                                            ($evaluation->status == 'in_progress' ? 
                                                                'bg-blue-900/30 text-blue-400' : 
                                                                'bg-yellow-900/30 text-yellow-400'))
                                                    }}">
                                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 
                                                        {{ $evaluation->status == 'completed' ? 
                                                            'bg-green-400' : 
                                                            ($evaluation->is_overdue ? 
                                                                'bg-red-400' : 
                                                                ($evaluation->status == 'in_progress' ? 
                                                                    'bg-blue-400' : 
                                                                    'bg-yellow-400'))
                                                        }}"></span>
                                                    {{ $evaluation->status == 'completed' ? 'Completed' : 
                                                        ($evaluation->is_overdue ? 'Overdue' : 
                                                            ($evaluation->status == 'in_progress' ? 'In Progress' : 'Pending')) 
                                                    }}
                                                    </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm 
                                                {{ $evaluation->is_overdue ? 'text-red-400' : 'text-gray-200' }}">
                                                {{ $evaluation->due_at ? Carbon\Carbon::parse($evaluation->due_at)->format('M d, Y') : 'No deadline' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                                @if($evaluation->status == 'completed')
                                                    <a href="{{ route('student.evaluations.show', $evaluation->id) }}" class="inline-block text-blue-400 hover:text-blue-300 transition">
                                                        <i class="fas fa-eye mr-1"></i>View
                                                    </a>
                                                @else
                                                    <a href="{{ route('student.evaluations.edit', $evaluation->id) }}" class="inline-block text-blue-400 hover:text-blue-300 transition">
                                                        <i class="fas fa-edit mr-1"></i>Complete
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($evaluations->hasPages())
                            <div class="mt-6">
                            {{ $evaluations->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="text-center py-12 bg-gray-800 rounded-xl border border-gray-700">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-700 text-gray-500 mb-4">
                        <i class="fas fa-clipboard-list text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">No evaluations found</h3>
                    <p class="text-gray-400 max-w-md mx-auto">You don't have any evaluations assigned to you or received yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab Navigation
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabPanes = document.querySelectorAll('.tab-pane');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active state from all tabs
                tabButtons.forEach(btn => {
                    btn.classList.remove('active', 'text-blue-500', 'border-blue-500');
                    btn.classList.add('text-gray-400', 'border-transparent');
                });
                
                tabPanes.forEach(pane => {
                    pane.classList.add('hidden');
                });
                
                // Add active state to current tab
                this.classList.add('active', 'text-blue-500', 'border-blue-500');
                this.classList.remove('text-gray-400', 'border-transparent');
                
                const targetPane = document.getElementById(this.dataset.target);
                if (targetPane) {
                    targetPane.classList.remove('hidden');
                }
            });
        });
        
        // Initialize active tab
        const activeTab = document.querySelector('.tab-button.active');
        if (activeTab) {
            activeTab.classList.add('text-blue-500', 'border-blue-500');
            activeTab.classList.remove('text-gray-400', 'border-transparent');
            
            const targetId = activeTab.dataset.target;
            const targetPane = document.getElementById(targetId);
            if (targetPane) {
                targetPane.classList.remove('hidden');
            }
        }
        
        // Evaluation search functionality for the "All" tab
        const evaluationSearch = document.getElementById('evaluation-search');
        if (evaluationSearch) {
            evaluationSearch.addEventListener('keyup', function() {
                const searchValue = this.value.toLowerCase();
                const evaluationRows = document.querySelectorAll('.evaluation-rows tr');
                
                evaluationRows.forEach(row => {
                    const briefTitle = row.querySelector('.text-sm.font-medium.text-white').textContent.toLowerCase();
                    const studentName = row.querySelector('td:nth-child(2) .text-sm.font-medium.text-white').textContent.toLowerCase();
                    
                    if (briefTitle.includes(searchValue) || studentName.includes(searchValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endsection
