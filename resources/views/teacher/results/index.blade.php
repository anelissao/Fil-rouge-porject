@extends('layouts.app')

@section('title', 'Results Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section with Gradient Background -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-lg mb-8 p-6">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0">
                <h1 class="text-3xl font-bold text-white mb-2">Brief Results Overview</h1>
                <p class="text-blue-100">Comprehensive overview of all briefs and their evaluation results</p>
            </div>
            <div>
                <a href="{{ route('teacher.dashboard') }}" class="inline-flex items-center px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-lg backdrop-blur-sm transition-all duration-300 border border-white/20">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Briefs Card -->
        <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden transform hover:shadow-lg transition-all duration-300 border border-gray-700">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="bg-blue-900/30 p-3 rounded-lg mr-4">
                        <i class="fas fa-file-alt text-blue-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">Total Briefs</p>
                        <div class="flex items-end">
                            <p class="text-3xl font-bold text-white">{{ $totalBriefs }}</p>
                            <p class="ml-2 text-green-400 text-sm font-medium">
                                <i class="fas fa-arrow-up mr-1"></i>
                                <span>4.5%</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 h-1 w-full bg-gray-700 rounded-full overflow-hidden">
                    <div class="bg-blue-600 h-1 rounded-full" style="width: 75%"></div>
                </div>
            </div>
        </div>
        
        <!-- Total Submissions Card -->
        <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden transform hover:shadow-lg transition-all duration-300 border border-gray-700">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="bg-green-900/30 p-3 rounded-lg mr-4">
                        <i class="fas fa-paper-plane text-green-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">Total Submissions</p>
                        <div class="flex items-end">
                            <p class="text-3xl font-bold text-white">{{ $totalSubmissions }}</p>
                            <p class="ml-2 text-green-400 text-sm font-medium">
                                <i class="fas fa-arrow-up mr-1"></i>
                                <span>12.7%</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 h-1 w-full bg-gray-700 rounded-full overflow-hidden">
                    <div class="bg-green-600 h-1 rounded-full" style="width: 68%"></div>
                </div>
            </div>
        </div>
        
        <!-- Total Evaluations Card -->
        <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden transform hover:shadow-lg transition-all duration-300 border border-gray-700">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="bg-purple-900/30 p-3 rounded-lg mr-4">
                        <i class="fas fa-clipboard-check text-purple-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">Total Evaluations</p>
                        <div class="flex items-end">
                            <p class="text-3xl font-bold text-white">{{ $totalEvaluations }}</p>
                            <p class="ml-2 text-green-400 text-sm font-medium">
                                <i class="fas fa-arrow-up mr-1"></i>
                                <span>8.3%</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 h-1 w-full bg-gray-700 rounded-full overflow-hidden">
                    <div class="bg-purple-600 h-1 rounded-full" style="width: 82%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Completion Rate Card -->
    <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden mb-8 border border-gray-700">
        <div class="p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                <div>
                    <h2 class="text-xl font-bold text-white mb-1">Completion Rate</h2>
                    <p class="text-sm text-gray-400">Overall evaluation completion for all briefs</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-900/40 text-blue-300">
                        <i class="fas fa-chart-line mr-1"></i> {{ $completionRate }}% completion
                    </div>
                </div>
            </div>
            
            <div class="w-full bg-gray-700 rounded-full h-4 mb-2">
                <div class="h-4 rounded-full bg-gradient-to-r from-blue-600 to-blue-400" style="width: {{ $completionRate }}%"></div>
            </div>
            
            <div class="flex justify-between text-xs text-gray-400 mt-1">
                <span>0%</span>
                <span>25%</span>
                <span>50%</span>
                <span>75%</span>
                <span>100%</span>
            </div>
        </div>
    </div>

    <!-- Briefs Results Table with Modern Design -->
    <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700">
        <div class="border-b border-gray-700 px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-white">All Briefs</h2>
                <p class="text-sm text-gray-400">Click on a brief to view detailed results</p>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="relative">
                    <input type="text" placeholder="Search briefs..." class="pl-10 pr-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full md:w-auto text-white">
                    <div class="absolute left-3 top-2.5 text-gray-500">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            @if(count($briefs) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Brief Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Submissions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Completion</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Last Updated</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($briefs as $brief)
                                <tr class="hover:bg-gray-750 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-blue-900/30 text-blue-400">
                                                <i class="fas fa-file-alt"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-white">{{ $brief->title }}</div>
                                                <div class="text-xs text-gray-400">{{ $brief->submissions_count ?? 0 }} submissions</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center
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
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                        {{ $brief->submissions_count ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(isset($brief->completion_percentage))
                                            <div class="flex items-center">
                                                <div class="w-full bg-gray-700 rounded-full h-2 mr-2 overflow-hidden">
                                                    <div class="h-2 rounded-full 
                                                        {{ $brief->completion_percentage >= 75 ? 
                                                            'bg-green-500' : 
                                                            ($brief->completion_percentage >= 50 ? 
                                                                'bg-blue-500' : 
                                                                ($brief->completion_percentage >= 25 ? 
                                                                    'bg-yellow-500' : 
                                                                    'bg-red-500')) 
                                                        }}" 
                                                        style="width: {{ $brief->completion_percentage }}%">
                                                    </div>
                                                </div>
                                                <span class="text-sm text-gray-200 whitespace-nowrap">{{ $brief->completion_percentage }}%</span>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                        {{ $brief->updated_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm space-x-3">
                                        <a href="{{ route('teacher.results.show', $brief->id) }}" class="inline-block text-blue-400 hover:text-blue-300 transition">
                                            <span class="inline-flex items-center">
                                                <i class="fas fa-eye mr-1"></i>
                                                <span>View</span>
                                            </span>
                                        </a>
                                        
                                        <a href="{{ route('teacher.results.export', $brief->id) }}" class="inline-block text-green-400 hover:text-green-300 transition">
                                            <span class="inline-flex items-center">
                                                <i class="fas fa-download mr-1"></i>
                                                <span>Export</span>
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
                        <i class="fas fa-folder-open text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">No briefs found</h3>
                    <p class="text-gray-400 max-w-md mx-auto mb-6">Create a brief to get started with student evaluations and track their progress.</p>
                    <a href="{{ route('teacher.briefs.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-lg shadow-sm transition-colors duration-300">
                        <i class="fas fa-plus mr-2"></i>
                        <span>Create Brief</span>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 