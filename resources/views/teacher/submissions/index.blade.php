@extends('layouts.app')

@section('title', 'Submissions')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section with Gradient Background -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-lg mb-8 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div class="mb-4 md:mb-0">
                <h1 class="text-3xl font-bold text-white mb-2">Submissions Management</h1>
                <p class="text-blue-100">View and manage student submissions</p>
            </div>
            <div>
                <a href="{{ route('teacher.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg backdrop-blur-sm transition-all duration-300 border border-white/20">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700">
        <div class="border-b border-gray-700 px-6 py-4">
            <div class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1">
                    <div class="relative">
                        <input 
                            type="text" 
                            id="submissionSearch" 
                            placeholder="Search submissions..." 
                            class="pl-10 pr-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full text-white"
                        >
                        <div class="absolute left-3 top-2.5 text-gray-500">
                            <i class="fas fa-search"></i>
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
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Brief</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Submitted At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Evaluations</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($submissions as $submission)
                        <tr class="hover:bg-gray-750 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full bg-gray-700 text-gray-400">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-white">{{ $submission->student->username }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-white">{{ $submission->brief->title }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-200">{{ $submission->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center bg-green-900/30 text-green-400">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-green-400"></span>
                                    Submitted
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                {{ $submission->evaluations_count ?? $submission->evaluations->count() }} evaluations
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('teacher.submissions.show', $submission->id) }}" class="text-blue-400 hover:text-blue-300 transition-colors">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-700 text-gray-500 mb-4">
                                    <i class="fas fa-file-upload text-3xl"></i>
                                </div>
                                <p class="text-lg font-medium text-white mb-2">No submissions found</p>
                                <p class="text-gray-400 max-w-md mx-auto">There are no student submissions available at the moment.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-700">
            {{ $submissions->links() }}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('submissionSearch');
        const briefFilter = document.getElementById('briefFilter');
        const tableRows = document.querySelectorAll('tbody tr');
        
        // Search functionality
        searchInput.addEventListener('keyup', filterSubmissions);
        briefFilter.addEventListener('change', filterSubmissions);
        
        function filterSubmissions() {
            const searchTerm = searchInput.value.toLowerCase();
            const briefId = briefFilter.value;
            
            tableRows.forEach(row => {
                const studentName = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                const briefTitle = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const rowBriefId = row.dataset.briefId;
                
                const matchesSearch = studentName.includes(searchTerm) || briefTitle.includes(searchTerm);
                const matchesBrief = briefId === 'all' || rowBriefId === briefId;
                
                if (matchesSearch && matchesBrief) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    });
</script>
@endsection 