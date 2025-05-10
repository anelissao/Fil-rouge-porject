@extends('layouts.app')

@section('title', 'Results for ' . $brief->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-lg mb-8 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div class="mb-4 md:mb-0">
                <h1 class="text-3xl font-bold text-white mb-2">Results for: {{ $brief->title }}</h1>
                <p class="text-blue-100">Detailed analysis and evaluation results for this brief</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('teacher.results.index') }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg backdrop-blur-sm transition-all duration-300 border border-white/20">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Results
                </a>
                <button id="export-btn" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg backdrop-blur-sm transition-all duration-300 border border-white/20">
                    <i class="fas fa-download mr-2"></i>Export Results
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Submissions Card -->
        <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden transform hover:shadow-lg transition-all duration-300 border border-gray-700">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="bg-blue-900/30 p-3 rounded-lg mr-4">
                        <i class="fas fa-paper-plane text-blue-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">Total Submissions</p>
                        <p class="text-3xl font-bold text-white">{{ $submissionsCount }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Evaluated Submissions Card -->
        <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden transform hover:shadow-lg transition-all duration-300 border border-gray-700">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="bg-green-900/30 p-3 rounded-lg mr-4">
                        <i class="fas fa-check-circle text-green-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">Evaluated Submissions</p>
                        <p class="text-3xl font-bold text-white">{{ $evaluatedCount }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Completion Rate Card -->
        <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden transform hover:shadow-lg transition-all duration-300 border border-gray-700">
            <div class="p-6">
                <div class="flex items-center mb-2">
                    <div class="bg-purple-900/30 p-3 rounded-lg mr-4">
                        <i class="fas fa-chart-pie text-purple-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">Completion Rate</p>
                        <p class="text-3xl font-bold text-white">{{ $completionRate }}%</p>
                    </div>
                </div>
                <div class="mt-2 h-2 w-full bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-2 rounded-full {{ $completionRate >= 75 ? 'bg-green-500' : ($completionRate >= 50 ? 'bg-blue-500' : ($completionRate >= 25 ? 'bg-yellow-500' : 'bg-red-500')) }}" style="width: {{ $completionRate }}%"></div>
                </div>
            </div>
        </div>
        
        <!-- Brief Status Card -->
        <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden transform hover:shadow-lg transition-all duration-300 border border-gray-700">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-900/30 p-3 rounded-lg mr-4">
                        <i class="fas fa-flag text-yellow-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">Brief Status</p>
                        <div class="flex items-center">
                            <span class="px-2.5 py-1 text-sm font-medium rounded-full inline-flex items-center
                                {{ $brief->is_published ? 
                                    ($brief->end_date && $brief->end_date < now() ? 
                                        'bg-gray-700 text-gray-300' : 
                                        'bg-green-900/30 text-green-400') : 
                                    'bg-yellow-900/30 text-yellow-400' 
                                }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 
                                    {{ $brief->is_published ? 
                                        ($brief->end_date && $brief->end_date < now() ? 
                                            'bg-gray-400' : 
                                            'bg-green-400') : 
                                        'bg-yellow-400' 
                                    }}"></span>
                                {{ $brief->is_published ? 
                                    ($brief->end_date && $brief->end_date < now() ? 
                                        'Ended' : 
                                        'Active') : 
                                    'Draft' 
                                }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Criteria Performance Section -->
    <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden mb-8 border border-gray-700">
        <div class="border-b border-gray-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Criteria Performance</h2>
            <p class="text-sm text-gray-400">Average scores across all evaluations for each criterion</p>
        </div>
        
        <div class="p-6">
            @if(count($criteriaPerformance) > 0)
                <div class="flex mb-4">
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-900/40 text-blue-300">
                        <i class="fas fa-chart-bar mr-1"></i> Performance Metrics
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Criterion</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Avg. Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Min</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Max</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Performance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($criteriaPerformance as $criterion)
                                <tr class="hover:bg-gray-750 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-lg bg-blue-900/30 text-blue-400">
                                                <i class="fas fa-list-ul"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-white">{{ $criterion['name'] }}</div>
                                                <div class="text-xs text-gray-400">{{ $criterion['description'] ?? 'No description' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                        {{ number_format($criterion['average'], 1) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                        {{ $criterion['min'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                        {{ $criterion['max'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-full bg-gray-700 rounded-full h-2 mr-2 overflow-hidden" style="width: 120px;">
                                                <div class="h-2 rounded-full 
                                                    {{ $criterion['average'] >= 3.5 ? 'bg-green-500' : ($criterion['average'] >= 2.5 ? 'bg-blue-500' : 'bg-red-500') }}" 
                                                    style="width: {{ ($criterion['average'] / 5) * 100 }}%">
                                                </div>
                                            </div>
                                            <span class="text-sm text-gray-200 whitespace-nowrap">{{ number_format(($criterion['average'] / 5) * 100, 0) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-700 text-gray-500 mb-4">
                        <i class="fas fa-chart-pie text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">No criteria data available</h3>
                    <p class="text-gray-400 max-w-md mx-auto">Once evaluations are completed, criteria performance data will appear here.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Student Performance Section -->
    <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden mb-8 border border-gray-700">
        <div class="border-b border-gray-700 px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-white">Student Performance</h2>
                <p class="text-sm text-gray-400">Detailed breakdown of individual student results</p>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="relative">
                    <input type="text" id="student-search" placeholder="Search students..." class="pl-10 pr-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full md:w-auto text-white">
                    <div class="absolute left-3 top-2.5 text-gray-500">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            @if(count($studentPerformance) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Submission Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Evaluations</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Avg. Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Performance</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700 student-rows">
                            @foreach($studentPerformance as $student)
                                <tr class="hover:bg-gray-750 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full bg-gray-700 text-gray-400">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-white">{{ $student['name'] }}</div>
                                                <div class="text-xs text-gray-400">{{ $student['email'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                        {{ $student['submission_date'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                        {{ $student['evaluations_count'] }} of {{ $student['total_evaluations'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 text-sm font-medium rounded-lg inline-flex items-center
                                            {{ $student['average_score'] >= 4 ? 
                                                'bg-green-900/30 text-green-400' : 
                                                ($student['average_score'] >= 3 ? 
                                                    'bg-blue-900/30 text-blue-400' : 
                                                    ($student['average_score'] >= 2 ? 
                                                        'bg-yellow-900/30 text-yellow-400' : 
                                                        'bg-red-900/30 text-red-400'))
                                            }}">
                                            {{ number_format($student['average_score'], 1) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-full bg-gray-700 rounded-full h-2 mr-2 overflow-hidden" style="width: 120px;">
                                                <div class="h-2 rounded-full 
                                                    {{ $student['average_score'] >= 4 ? 'bg-green-500' : ($student['average_score'] >= 3 ? 'bg-blue-500' : ($student['average_score'] >= 2 ? 'bg-yellow-500' : 'bg-red-500')) }}" 
                                                    style="width: {{ ($student['average_score'] / 5) * 100 }}%">
                                                </div>
                                            </div>
                                            <span class="text-sm text-gray-200 whitespace-nowrap">{{ number_format(($student['average_score'] / 5) * 100, 0) }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm space-x-3">
                                        <a href="{{ route('teacher.submissions.show', $student['submission_id']) }}" class="inline-block text-blue-400 hover:text-blue-300 transition">
                                            <span class="inline-flex items-center">
                                                <i class="fas fa-eye mr-1"></i>
                                                <span>View</span>
                                            </span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-700 text-gray-500 mb-4">
                        <i class="fas fa-users text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">No student data available</h3>
                    <p class="text-gray-400 max-w-md mx-auto">Once students submit their work, performance data will appear here.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Brief Details Section -->
    <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden mb-8 border border-gray-700">
        <div class="border-b border-gray-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Brief Details</h2>
            <p class="text-sm text-gray-400">Information about this brief</p>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-gray-750 rounded-lg p-4 border border-gray-700">
                    <p class="text-sm text-gray-400 mb-1">Title</p>
                    <p class="text-lg text-white font-medium">{{ $brief->title }}</p>
                </div>
                <div class="bg-gray-750 rounded-lg p-4 border border-gray-700">
                    <p class="text-sm text-gray-400 mb-1">Status</p>
                    <span class="px-2.5 py-1 text-sm font-medium rounded-full inline-flex items-center
                        {{ $brief->is_published ? 
                            ($brief->end_date && $brief->end_date < now() ? 
                                'bg-gray-700 text-gray-300' : 
                                'bg-green-900/30 text-green-400') : 
                            'bg-yellow-900/30 text-yellow-400' 
                        }}">
                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 
                            {{ $brief->is_published ? 
                                ($brief->end_date && $brief->end_date < now() ? 
                                    'bg-gray-400' : 
                                    'bg-green-400') : 
                                'bg-yellow-400' 
                            }}"></span>
                        {{ $brief->is_published ? 
                            ($brief->end_date && $brief->end_date < now() ? 
                                'Ended' : 
                                'Active') : 
                            'Draft' 
                        }}
                    </span>
                </div>
                <div class="bg-gray-750 rounded-lg p-4 border border-gray-700">
                    <p class="text-sm text-gray-400 mb-1">Created On</p>
                    <p class="text-lg text-white font-medium">{{ $brief->created_at->format('M d, Y') }}</p>
                </div>
                <div class="bg-gray-750 rounded-lg p-4 border border-gray-700">
                    <p class="text-sm text-gray-400 mb-1">Deadline</p>
                    <p class="text-lg text-white font-medium">{{ $brief->end_date ? $brief->end_date->format('M d, Y') : 'No deadline' }}</p>
                </div>
            </div>
            
            <div class="bg-gray-750 rounded-lg p-4 border border-gray-700">
                <p class="text-sm text-gray-400 mb-1">Description</p>
                <div class="text-gray-200 prose prose-sm max-w-none">
                    {!! $brief->description !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div id="export-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black opacity-50"></div>
            <div class="bg-gray-800 rounded-xl shadow-xl max-w-md w-full z-10 relative border border-gray-700">
                <div class="border-b border-gray-700 p-5">
                    <h3 class="text-xl font-bold text-white">Export Results</h3>
                    <button class="absolute top-4 right-4 text-gray-400 hover:text-white" id="close-modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6">
                    <form action="{{ route('teacher.results.export', $brief->id) }}" method="POST">
                        @csrf
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-400 mb-2">Format</label>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <input type="radio" id="format-csv" name="format" value="csv" class="hidden format-radio" checked>
                                    <label for="format-csv" class="format-label flex flex-col items-center p-4 border border-gray-700 rounded-lg cursor-pointer hover:bg-gray-750 transition-colors duration-200">
                                        <div class="text-gray-400 h-8 w-8 flex items-center justify-center">
                                            <i class="fas fa-file-csv text-2xl"></i>
                                        </div>
                                        <span class="mt-2 text-sm text-gray-300">CSV</span>
                                    </label>
                                </div>
                                <div>
                                    <input type="radio" id="format-excel" name="format" value="excel" class="hidden format-radio">
                                    <label for="format-excel" class="format-label flex flex-col items-center p-4 border border-gray-700 rounded-lg cursor-pointer hover:bg-gray-750 transition-colors duration-200">
                                        <div class="text-gray-400 h-8 w-8 flex items-center justify-center">
                                            <i class="fas fa-file-excel text-2xl"></i>
                                        </div>
                                        <span class="mt-2 text-sm text-gray-300">Excel</span>
                                    </label>
                                </div>
                                <div>
                                    <input type="radio" id="format-pdf" name="format" value="pdf" class="hidden format-radio">
                                    <label for="format-pdf" class="format-label flex flex-col items-center p-4 border border-gray-700 rounded-lg cursor-pointer hover:bg-gray-750 transition-colors duration-200">
                                        <div class="text-gray-400 h-8 w-8 flex items-center justify-center">
                                            <i class="fas fa-file-pdf text-2xl"></i>
                                        </div>
                                        <span class="mt-2 text-sm text-gray-300">PDF</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-400 mb-2">Options</label>
                            <div class="space-y-3">
                                <label class="flex items-center text-gray-300">
                                    <input type="checkbox" name="include_comments" value="1" class="form-checkbox h-5 w-5 text-blue-600 rounded border-gray-700 bg-gray-700 focus:ring-offset-gray-800 focus:ring-blue-500">
                                    <span class="ml-2">Include comments</span>
                                </label>
                                <label class="flex items-center text-gray-300">
                                    <input type="checkbox" name="anonymize" value="1" class="form-checkbox h-5 w-5 text-blue-600 rounded border-gray-700 bg-gray-700 focus:ring-offset-gray-800 focus:ring-blue-500">
                                    <span class="ml-2">Anonymize students</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="button" id="cancel-export" class="px-4 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-750 hover:text-white transition-colors duration-200 mr-3">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-lg transition-colors duration-200">
                                <i class="fas fa-download mr-2"></i>Export
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Format selection
        const formatRadios = document.querySelectorAll('.format-radio');
        const formatLabels = document.querySelectorAll('.format-label');
        
        formatRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                formatLabels.forEach(label => {
                    label.classList.remove('border-blue-500', 'bg-blue-900/20');
                });
                
                if (this.checked) {
                    const label = document.querySelector(`label[for="${this.id}"]`);
                    label.classList.add('border-blue-500', 'bg-blue-900/20');
                }
            });
            
            if (radio.checked) {
                const label = document.querySelector(`label[for="${radio.id}"]`);
                label.classList.add('border-blue-500', 'bg-blue-900/20');
            }
        });
        
        // Export modal
        const exportBtn = document.getElementById('export-btn');
        const exportModal = document.getElementById('export-modal');
        const closeModal = document.getElementById('close-modal');
        const cancelExport = document.getElementById('cancel-export');
        
        exportBtn.addEventListener('click', function() {
            exportModal.classList.remove('hidden');
        });
        
        [closeModal, cancelExport].forEach(el => {
            el.addEventListener('click', function() {
                exportModal.classList.add('hidden');
            });
        });
        
        // Close modal when clicking outside
        exportModal.addEventListener('click', function(e) {
            if (e.target === exportModal.querySelector('.fixed')) {
                exportModal.classList.add('hidden');
            }
        });
        
        // Student search
        const studentSearch = document.getElementById('student-search');
        const studentRows = document.querySelectorAll('.student-rows tr');
        
        studentSearch.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            
            studentRows.forEach(row => {
                const studentName = row.querySelector('.text-sm.font-medium.text-white').textContent.toLowerCase();
                const studentEmail = row.querySelector('.text-xs.text-gray-400').textContent.toLowerCase();
                
                if (studentName.includes(searchValue) || studentEmail.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
</script>
@endsection 