@extends('layouts.app')

@section('title', 'Manage Briefs')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-gray-800 via-gray-700 to-gray-800 rounded-xl shadow-lg p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">Manage Briefs</h1>
                <p class="text-blue-300">View and manage all briefs in the system</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-700/50 hover:bg-gray-600/50 text-white rounded-lg transition-colors duration-300 inline-flex items-center backdrop-blur-sm border border-gray-600/30">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-gray-800 rounded-xl p-5 shadow-md mb-6">
        <form action="{{ route('admin.briefs.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Search by title or description" 
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg py-2 pl-10 pr-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        value="{{ request('search') }}"
                    >
                    @if(request('search'))
                        <a href="{{ route('admin.briefs.index') }}" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>
            
            <div class="w-full sm:w-auto">
                <select name="status" class="bg-gray-700 border border-gray-600 rounded-lg py-2 px-4 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
            
            <div class="w-full sm:w-auto">
                <select name="teacher" class="bg-gray-700 border border-gray-600 rounded-lg py-2 px-4 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full">
                    <option value="">All Teachers</option>
                    @foreach(\App\Models\User::where('role', 'teacher')->get() as $teacher)
                        <option value="{{ $teacher->id }}" {{ request('teacher') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->first_name }} {{ $teacher->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-300">
                    Apply Filters
                </button>
                
                @if(request()->anyFilled(['search', 'status', 'teacher']))
                    <a href="{{ route('admin.briefs.index') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors duration-300">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Briefs Table -->
    <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-900/60 border-b border-gray-700">
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-300">Brief</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-300">Teacher</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-300">Status</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-300">Deadline</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-300">Submissions</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\App\Models\Brief::with('teacher')->orderBy('created_at', 'desc')->paginate(10) as $brief)
                        <tr class="border-b border-gray-700 hover:bg-gray-700/50 transition-colors">
                            <td class="py-3 px-4">
                                <div>
                                    <div class="font-medium text-white">{{ $brief->title }}</div>
                                    <div class="text-sm text-gray-400">Created {{ $brief->created_at->format('M d, Y') }}</div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center mr-3 text-purple-400 overflow-hidden">
                                        @if($brief->teacher && $brief->teacher->avatar)
                                            <img src="{{ $brief->teacher->avatar_url }}" alt="{{ $brief->teacher->username }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-user"></i>
                                        @endif
                                    </div>
                                    <div class="text-gray-300">
                                        {{ $brief->teacher ? $brief->teacher->first_name . ' ' . $brief->teacher->last_name : 'Unknown' }}
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium 
                                    {{ $brief->status == 'draft' ? 'bg-gray-700 text-gray-300' : 
                                    ($brief->status == 'active' ? 'bg-green-900/50 text-green-400' : 
                                    'bg-red-900/50 text-red-400') }}">
                                    {{ ucfirst($brief->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-gray-300">
                                    {{ $brief->deadline->format('M d, Y') }}
                                </div>
                                <div class="text-sm text-gray-400">
                                    {{ $brief->deadline->format('g:i A') }}
                                </div>
                            </td>
                            <td class="py-3 px-4 text-gray-300">
                                {{ $brief->submissions->count() }} submissions
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex gap-2">
                                    <a href="#" class="p-2 bg-blue-600/20 hover:bg-blue-600/40 text-blue-400 rounded transition-colors">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="p-2 bg-yellow-600/20 hover:bg-yellow-600/40 text-yellow-400 rounded transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="p-2 bg-red-600/20 hover:bg-red-600/40 text-red-400 rounded transition-colors">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
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
        {{ \App\Models\Brief::orderBy('created_at', 'desc')->paginate(10)->links() }}
    </div>
</div>
@endsection 