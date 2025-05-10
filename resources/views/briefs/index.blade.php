@extends('layouts.app')

@section('title', 'All Briefs')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section with Gradient Background -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-lg mb-8 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div class="mb-4 md:mb-0">
                <h1 class="text-3xl font-bold text-white mb-2">All Briefs</h1>
                <p class="text-blue-100">View all available briefs and their details</p>
            </div>
            @if(auth()->user()->isTeacher())
            <div>
                <a href="{{ route('teacher.briefs.create') }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg backdrop-blur-sm transition-all duration-300 border border-white/20">
                    <i class="fas fa-plus-circle mr-2"></i> Create New Brief
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700 mb-8">
        <div class="px-6 py-4">
            <form action="{{ route('briefs.index') }}" method="GET" class="space-y-4 md:space-y-0 md:grid md:grid-cols-3 md:gap-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-400 mb-2">Status</label>
                    <div class="relative">
                        <select name="status" id="status" class="pl-3 pr-10 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full text-white appearance-none">
                            <option value="">All Statuses</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-gray-500">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-400 mb-2">Search</label>
                    <div class="relative">
                        <input type="text" name="search" id="search" class="pl-10 pr-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full text-white" placeholder="Search by title..." value="{{ request('search') }}">
                        <div class="absolute left-3 top-2.5 text-gray-500">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                </div>
                <div class="flex items-end space-x-3">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Apply Filters
                    </button>
                    <a href="{{ route('briefs.index') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Briefs List -->
    <div class="space-y-6">
        @if(isset($briefs) && count($briefs) > 0)
            @foreach($briefs as $brief)
                <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 
                    {{ $brief->status == 'published' ? 'border-l-4 border-l-green-500' : 
                      ($brief->status == 'draft' ? 'border-l-4 border-l-yellow-500' : 'border-l-4 border-l-gray-500') }}
                    {{ $brief->isExpired() ? 'border-l-4 border-l-red-500' : '' }}">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:justify-between md:items-start">
                            <div class="md:w-2/3 mb-4 md:mb-0 md:pr-6">
                                <h3 class="text-xl font-semibold mb-2">
                                    <a href="{{ auth()->user()->isTeacher() ? route('teacher.briefs.show', $brief->id) : route('briefs.show', $brief->id) }}" class="text-white hover:text-blue-400 transition-colors">
                                        {{ $brief->title }}
                                    </a>
                                </h3>
                                <div class="flex flex-wrap items-center gap-3 mb-3">
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center
                                        {{ $brief->status == 'published' ? 'bg-green-900/30 text-green-400' : 
                                          ($brief->status == 'draft' ? 'bg-yellow-900/30 text-yellow-400' : 
                                          'bg-gray-700 text-gray-400') }}">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 
                                            {{ $brief->status == 'published' ? 'bg-green-400' : 
                                              ($brief->status == 'draft' ? 'bg-yellow-400' : 
                                              'bg-gray-400') }}"></span>
                                        {{ ucfirst($brief->status) }}
                                    </span>
                                    <span class="text-gray-400 inline-flex items-center">
                                        <i class="fas fa-user mr-1.5"></i> {{ $brief->teacher->username ?? 'Unknown Teacher' }}
                                    </span>
                                    @if($brief->status == 'published')
                                        <span class="text-gray-400 inline-flex items-center">
                                            <i class="fas fa-calendar mr-1.5"></i> Deadline: {{ $brief->deadline ? $brief->deadline->format('M d, Y') : 'No deadline' }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-gray-300">
                                    {{ \Illuminate\Support\Str::limit($brief->description, 150) }}
                                </p>
                            </div>
                            <div class="md:w-1/3 flex flex-col items-start md:items-end">
                                @if($brief->status == 'published')
                                    <div class="mb-4">
                                        @if($brief->isExpired())
                                            <span class="px-3 py-1 bg-red-900/30 text-red-400 rounded-full text-sm font-medium inline-flex items-center">
                                                <i class="fas fa-clock mr-1.5"></i> Expired
                                            </span>
                                        @else
                                            <span class="px-3 py-1 bg-blue-900/30 text-blue-400 rounded-full text-sm font-medium inline-flex items-center">
                                                <i class="fas fa-clock mr-1.5"></i> 
                                                {{ $brief->deadline ? $brief->deadline->diffForHumans() : 'No deadline' }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                                
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ auth()->user()->isTeacher() ? route('teacher.briefs.show', $brief->id) : route('briefs.show', $brief->id) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 rounded-lg transition-colors border border-blue-600/20">
                                        <i class="fas fa-eye mr-1.5"></i> View
                                    </a>
                                    
                                    @if(auth()->user()->isTeacher() && $brief->teacher_id == auth()->id())
                                        <a href="{{ route('teacher.briefs.edit', $brief->id) }}" class="inline-flex items-center px-3 py-1.5 bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-lg transition-colors">
                                            <i class="fas fa-edit mr-1.5"></i> Edit
                                        </a>
                                    @endif
                                    
                                    @if(auth()->user()->isStudent() && $brief->status == 'published' && !$brief->isExpired())
                                        <a href="{{ route('student.submissions.create', ['brief_id' => $brief->id]) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                            <i class="fas fa-upload mr-1.5"></i> Submit
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            
            <!-- Pagination -->
            <div class="mt-8 flex justify-center">
                {{ $briefs->links() }}
            </div>
        @else
            <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700 p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-700 text-gray-500 mb-4">
                    <i class="fas fa-info-circle text-3xl"></i>
                </div>
                <h4 class="text-xl font-semibold text-white mb-2">No briefs found</h4>
                <p class="text-gray-400 max-w-md mx-auto mb-6">There are no briefs available matching your criteria.</p>
                @if(auth()->user()->isTeacher())
                    <a href="{{ route('teacher.briefs.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-plus-circle mr-2"></i> Create New Brief
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection 