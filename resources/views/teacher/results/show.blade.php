@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Results for: {{ $brief->title }}</h1>
            <p class="text-gray-600">Detailed analysis of student performance and evaluation metrics</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('teacher.results.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to Results
            </a>
            <button 
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition" 
                onclick="document.getElementById('exportModal').classList.remove('hidden')"
            >
                <i class="fas fa-download mr-2"></i>Export Results
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Submissions</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalSubmissions }}</p>
                </div>
                <div class="bg-blue-100 p-2 rounded-md">
                    <i class="fas fa-file-alt text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Evaluated Submissions</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $evaluatedSubmissions }}</p>
                </div>
                <div class="bg-green-100 p-2 rounded-md">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Completion Rate</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $completionRate }}%</p>
                </div>
                <div class="bg-purple-100 p-2 rounded-md">
                    <i class="fas fa-chart-pie text-purple-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Brief Status</p>
                    <p class="text-2xl font-bold text-{{ $brief->is_published ? ($brief->end_date && $brief->end_date < now() ? 'gray' : 'green') : 'yellow' }}-600">
                        {{ $brief->is_published ? ($brief->end_date && $brief->end_date < now() ? 'Ended' : 'Active') : 'Draft' }}
                    </p>
                </div>
                <div class="bg-{{ $brief->is_published ? ($brief->end_date && $brief->end_date < now() ? 'gray' : 'green') : 'yellow' }}-100 p-2 rounded-md">
                    <i class="fas fa-{{ $brief->is_published ? ($brief->end_date && $brief->end_date < now() ? 'hourglass-end' : 'play-circle') : 'exclamation-circle' }} text-{{ $brief->is_published ? ($brief->end_date && $brief->end_date < now() ? 'gray' : 'green') : 'yellow' }}-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Criteria Performance Section -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-lg font-medium text-gray-800">Criteria Performance</h2>
            <p class="text-sm text-gray-600">Average scores across all evaluations for each criterion</p>
        </div>
        
        <div class="p-6">
            @if(isset($criteriaData) && count($criteriaData) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criterion</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Score</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($criteriaData as $criterion)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $criterion['title'] }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($criterion['average'], 1) }}/10</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $criterion['min'] }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $criterion['max'] }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 w-1/4">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-{{ $criterion['average'] >= 7 ? 'green' : ($criterion['average'] >= 5 ? 'yellow' : 'red') }}-600 h-2.5 rounded-full" style="width: {{ ($criterion['average'] / 10) * 100 }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-10">
                    <p class="text-gray-500">No evaluation data available for criteria analysis.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Student Performance Section -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-lg font-medium text-gray-800">Student Performance</h2>
            <p class="text-sm text-gray-600">Detailed breakdown of individual student results</p>
        </div>
        
        <div class="p-6">
            @if(isset($studentPerformance) && count($studentPerformance) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submission Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evaluations</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Score</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($studentPerformance as $performance)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $performance['student']->username }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $performance['submission_date']->format('M d, Y H:i') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $performance['evaluations'] }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($performance['average_score'], 1) }}/10</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 w-1/4">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-{{ $performance['average_score'] >= 7 ? 'green' : ($performance['average_score'] >= 5 ? 'yellow' : 'red') }}-600 h-2.5 rounded-full" style="width: {{ ($performance['average_score'] / 10) * 100 }}%"></div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <a href="#" class="text-blue-600 hover:text-blue-800 transition">
                                            <i class="fas fa-eye mr-1"></i>View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-10">
                    <p class="text-gray-500">No student performance data available.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Brief Details Section -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-lg font-medium text-gray-800">Brief Details</h2>
            <p class="text-sm text-gray-600">Information about this brief</p>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-md font-medium text-gray-700 mb-2">Title</h3>
                    <p class="text-gray-900">{{ $brief->title }}</p>
                </div>
                
                <div>
                    <h3 class="text-md font-medium text-gray-700 mb-2">Status</h3>
                    <p class="text-{{ $brief->is_published ? ($brief->end_date && $brief->end_date < now() ? 'gray' : 'green') : 'yellow' }}-600 font-medium">
                        {{ $brief->is_published ? ($brief->end_date && $brief->end_date < now() ? 'Ended' : 'Active') : 'Draft' }}
                    </p>
                </div>
                
                <div>
                    <h3 class="text-md font-medium text-gray-700 mb-2">Created On</h3>
                    <p class="text-gray-900">{{ $brief->created_at->format('M d, Y H:i') }}</p>
                </div>
                
                <div>
                    <h3 class="text-md font-medium text-gray-700 mb-2">Deadline</h3>
                    <p class="text-gray-900">
                        {{ $brief->end_date ? $brief->end_date->format('M d, Y H:i') : 'No deadline set' }}
                    </p>
                </div>
                
                @if($brief->description)
                <div class="md:col-span-2">
                    <h3 class="text-md font-medium text-gray-700 mb-2">Description</h3>
                    <div class="p-4 bg-gray-50 rounded-md">
                        <p class="text-gray-900">{{ $brief->description }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div id="exportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full">
            <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-800">Export Results</h3>
                <button onclick="document.getElementById('exportModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('teacher.results.export', $brief->id) }}" method="GET" class="p-6">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Format</label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="format" value="csv" class="form-radio text-blue-600" checked>
                            <span class="ml-2 text-gray-700">CSV</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="format" value="excel" class="form-radio text-blue-600">
                            <span class="ml-2 text-gray-700">Excel</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="format" value="pdf" class="form-radio text-blue-600">
                            <span class="ml-2 text-gray-700">PDF</span>
                        </label>
                    </div>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Options</label>
                    <div class="space-y-3">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="include_comments" value="1" class="form-checkbox text-blue-600" checked>
                            <span class="ml-2 text-gray-700">Include comments</span>
                        </label>
                        <div class="block">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="anonymize" value="1" class="form-checkbox text-blue-600">
                                <span class="ml-2 text-gray-700">Anonymize student data</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button 
                        type="button" 
                        onclick="document.getElementById('exportModal').classList.add('hidden')" 
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                    >
                        Export
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Add any custom JavaScript for the results page
    document.addEventListener('DOMContentLoaded', function() {
        // Example of chart initialization if you add charts later
        // const criteriaChart = document.getElementById('criteriaChart');
        // if (criteriaChart) {
        //     // Initialize chart
        // }
    });
</script>
@endsection 