@extends('layouts.app')

@section('title', 'Submissions for ' . $brief->title)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white mb-1">Submissions for Brief</h1>
                <p class="text-blue-400">{{ $brief->title }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('teacher.briefs.show', $brief->id) }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors duration-300 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Brief
                </a>
                <a href="{{ route('teacher.briefs.index') }}" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-lg transition-colors duration-300 inline-flex items-center border border-gray-700">
                    <i class="fas fa-list mr-2"></i> All Briefs
                </a>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Stats Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gray-800 rounded-xl p-4 shadow-md flex items-center">
                    <div class="w-12 h-12 rounded-full bg-blue-600/20 flex items-center justify-center mr-4 text-blue-500">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">{{ $brief->assigned_students_count ?? 0 }}</h3>
                        <p class="text-gray-400">Assigned Students</p>
                    </div>
                </div>
                
                <div class="bg-gray-800 rounded-xl p-4 shadow-md flex items-center">
                    <div class="w-12 h-12 rounded-full bg-green-600/20 flex items-center justify-center mr-4 text-green-500">
                        <i class="fas fa-upload text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">{{ $submissions->total() ?? 0 }}</h3>
                        <p class="text-gray-400">Total Submissions</p>
                    </div>
                </div>
                
                <div class="bg-gray-800 rounded-xl p-4 shadow-md flex items-center">
                    <div class="w-12 h-12 rounded-full bg-purple-600/20 flex items-center justify-center mr-4 text-purple-500">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">
                            @if($brief->assigned_students_count > 0)
                                {{ round(($submissions->total() / $brief->assigned_students_count) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </h3>
                        <p class="text-gray-400">Submission Rate</p>
                    </div>
                </div>
                
                <div class="bg-gray-800 rounded-xl p-4 shadow-md flex items-center">
                    <div class="w-12 h-12 rounded-full bg-yellow-600/20 flex items-center justify-center mr-4 text-yellow-500">
                        <i class="fas fa-clipboard-check text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">{{ $evaluatedCount ?? 0 }}</h3>
                        <p class="text-gray-400">Evaluated</p>
                    </div>
                </div>
            </div>

            <!-- Filter Container -->
            <div class="bg-gray-800 rounded-xl p-5 shadow-md">
                <form action="{{ route('teacher.briefs.submissions', $brief->id) }}" method="GET" class="flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Search by student name or username" 
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg py-2 pl-10 pr-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                value="{{ request('search') }}"
                            >
                            @if(request('search'))
                                <a href="{{ route('teacher.briefs.submissions', $brief->id) }}" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                    
                    <div class="w-full sm:w-auto">
                        <select name="status" class="bg-gray-700 border border-gray-600 rounded-lg py-2 px-4 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full">
                            <option value="">All Status</option>
                            <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="evaluated" {{ request('status') == 'evaluated' ? 'selected' : '' }}>Evaluated</option>
                            <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late Submissions</option>
                        </select>
                    </div>
                    
                    <div class="w-full sm:w-auto">
                        <select name="sort" class="bg-gray-700 border border-gray-600 rounded-lg py-2 px-4 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            <option value="student_asc" {{ request('sort') == 'student_asc' ? 'selected' : '' }}>Student (A-Z)</option>
                            <option value="student_desc" {{ request('sort') == 'student_desc' ? 'selected' : '' }}>Student (Z-A)</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-300">
                            Apply Filters
                        </button>
                        
                        @if(request()->anyFilled(['search', 'status', 'sort']))
                            <a href="{{ route('teacher.briefs.submissions', $brief->id) }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors duration-300">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Submissions List -->
            @if(isset($submissions) && count($submissions) > 0)
                <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-900/60 border-b border-gray-700">
                                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-300">Student</th>
                                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-300">Submission Date</th>
                                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-300">Status</th>
                                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-300">Evaluation</th>
                                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($submissions as $submission)
                                    <tr class="border-b border-gray-700 hover:bg-gray-700/50 transition-colors">
                                        <td class="py-3 px-4">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center mr-3 text-blue-400">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-white">{{ $submission->student->first_name }} {{ $submission->student->last_name }}</div>
                                                    <div class="text-sm text-gray-400">{{ $submission->student->username }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="text-white">
                                                {{ $submission->created_at->format('M d, Y') }}
                                            </div>
                                            <div class="text-sm text-gray-400 flex items-center">
                                                {{ $submission->created_at->format('g:i A') }}
                                                @if($submission->created_at > $brief->deadline)
                                                    <span class="ml-2 px-2 py-0.5 bg-red-900/50 text-red-400 text-xs rounded">Late</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium 
                                                {{ $submission->status == 'submitted' ? 'bg-blue-900/50 text-blue-400' : 
                                                ($submission->status == 'evaluated' ? 'bg-green-900/50 text-green-400' : 
                                                'bg-yellow-900/50 text-yellow-400') }}">
                                                {{ ucfirst($submission->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            @if($submission->evaluations->count() > 0)
                                                <div class="flex items-center text-green-400">
                                                    <i class="fas fa-check-circle mr-2"></i> 
                                                    {{ $submission->evaluations->count() }} evaluations
                                                </div>
                                            @else
                                                <div class="flex items-center text-yellow-400">
                                                    <i class="fas fa-hourglass-half mr-2"></i> Pending
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="flex gap-2">
                                                <a href="{{ route('teacher.submissions.show', $submission->id) }}" class="p-2 bg-blue-600/20 hover:bg-blue-600/40 text-blue-400 rounded transition-colors">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="#" class="p-2 bg-yellow-600/20 hover:bg-yellow-600/40 text-yellow-400 rounded transition-colors">
                                                    <i class="fas fa-star"></i>
                                                </a>
                                                <a href="#" class="p-2 bg-green-600/20 hover:bg-green-600/40 text-green-400 rounded transition-colors">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination -->
                <div class="mt-6">
                    {{ $submissions->appends(request()->query())->links() }}
                </div>
            @else
                <div class="bg-gray-800 rounded-xl p-12 shadow-md text-center">
                    <div class="w-16 h-16 rounded-full bg-gray-700 flex items-center justify-center mx-auto mb-4 text-gray-400">
                        <i class="fas fa-file-upload text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">No submissions found</h3>
                    @if(request()->anyFilled(['search', 'status', 'sort']))
                        <p class="text-gray-400 mb-4">Try adjusting your search filters</p>
                        <a href="{{ route('teacher.briefs.submissions', $brief->id) }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors duration-300 inline-flex items-center">
                            Reset Filters
                        </a>
                    @else
                        <p class="text-gray-400 mb-4">There are no submissions for this brief yet.</p>
                        <a href="{{ route('teacher.briefs.show', $brief->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-300 inline-flex items-center">
                            Back to Brief
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection 