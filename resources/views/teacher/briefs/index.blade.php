@extends('layouts.app')

@section('title', 'Manage Briefs')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section with Gradient Background -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-lg mb-8 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div class="mb-4 md:mb-0">
                <h1 class="text-3xl font-bold text-white mb-2">Manage Briefs</h1>
                <p class="text-blue-100">Create, edit and manage student assignments</p>
            </div>
        <div>
                <a href="{{ route('teacher.briefs.create') }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg backdrop-blur-sm transition-all duration-300 border border-white/20">
                    <i class="fas fa-plus-circle mr-2"></i>Create Brief
                </a>
        </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-gray-800 rounded-xl shadow-md p-4 mb-8 border border-gray-700">
        <form action="{{ route('teacher.briefs.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Search by title or description" 
                        class="pl-10 pr-10 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full text-white"
                        value="{{ request('search') }}"
                    >
                    <div class="absolute left-3 top-2.5 text-gray-500">
                        <i class="fas fa-search"></i>
                    </div>
                    @if(request('search'))
                        <a href="{{ route('teacher.briefs.index', array_filter(request()->except('search'))) }}" class="absolute right-3 top-2.5 text-gray-500 hover:text-white">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>
            
            <div class="w-full md:w-48">
                <select name="status" class="py-2 px-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full text-white">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
            
            <div class="w-full md:w-48">
                <select name="sort" class="py-2 px-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full text-white">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="deadline_asc" {{ request('sort') == 'deadline_asc' ? 'selected' : '' }}>Deadline (Soonest)</option>
                    <option value="deadline_desc" {{ request('sort') == 'deadline_desc' ? 'selected' : '' }}>Deadline (Latest)</option>
                    <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Title (A-Z)</option>
                    <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Title (Z-A)</option>
                </select>
            </div>

            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">Apply Filters</button>
            
            @if(request()->anyFilled(['search', 'status', 'sort']))
                <a href="{{ route('teacher.briefs.index') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">Reset</a>
            @endif
        </form>
    </div>

    <!-- Briefs List -->
    <div>
        @if(isset($briefs) && count($briefs) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($briefs as $brief)
                    <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden transform hover:shadow-lg transition-all duration-300 border border-gray-700 relative">
                        <div class="absolute top-4 right-4 z-10">
                            <div class="relative inline-block text-left">
                                <button class="p-2 rounded-full bg-gray-700 hover:bg-gray-600 text-white" id="dropdown-toggle-{{ $brief->id }}" onclick="toggleDropdown('{{ $brief->id }}')">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div id="dropdown-menu-{{ $brief->id }}" class="hidden absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-gray-800 border border-gray-700 z-20">
                                    <div class="py-1">
                                        <a href="{{ route('teacher.briefs.edit', $brief->id) }}" class="flex items-center px-4 py-2 text-white hover:bg-gray-750 hover:text-primary transition-colors">
                                            <i class="fas fa-edit mr-2"></i> Edit
                                        </a>
                                        <a href="{{ route('teacher.briefs.show', $brief->id) }}" class="flex items-center px-4 py-2 text-white hover:bg-gray-750 hover:text-primary transition-colors">
                                            <i class="fas fa-eye mr-2"></i> View
                                        </a>
                                        <a href="{{ route('teacher.briefs.submissions', $brief->id) }}" class="flex items-center px-4 py-2 text-white hover:bg-gray-750 hover:text-primary transition-colors">
                                            <i class="fas fa-upload mr-2"></i> Submissions
                                        </a>
                                        <a href="{{ route('teacher.briefs.results', $brief->id) }}" class="flex items-center px-4 py-2 text-white hover:bg-gray-750 hover:text-primary transition-colors">
                                            <i class="fas fa-chart-bar mr-2"></i> Results
                                        </a>
                                        <div class="border-t border-gray-700 my-1"></div>
                                    @if($brief->status == 'draft')
                                            <form action="{{ route('teacher.briefs.publish', $brief->id) }}" method="POST">
                                            @csrf
                                                <button type="submit" class="flex w-full items-center px-4 py-2 text-white hover:bg-gray-750 hover:text-primary transition-colors">
                                                    <i class="fas fa-share-square mr-2"></i> Publish
                                            </button>
                                        </form>
                                    @elseif($brief->status == 'active')
                                            <form action="{{ route('teacher.briefs.unpublish', $brief->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="flex w-full items-center px-4 py-2 text-white hover:bg-gray-750 hover:text-primary transition-colors">
                                                    <i class="fas fa-eye-slash mr-2"></i> Unpublish
                                                </button>
                                            </form>
                                        @endif
                                        <div class="border-t border-gray-700 my-1"></div>
                                        <form action="{{ route('teacher.briefs.destroy', $brief->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex w-full items-center px-4 py-2 text-red-400 hover:bg-gray-750 transition-colors" onclick="return confirm('Are you sure you want to delete this brief?')">
                                                <i class="fas fa-trash-alt mr-2"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h2 class="text-xl font-bold text-white mb-2 pr-8">{{ $brief->title }}</h2>
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center
                                    {{ $brief->status == 'active' ? 'bg-green-900/30 text-green-400' : 
                                      ($brief->status == 'draft' ? 'bg-yellow-900/30 text-yellow-400' : 
                                      'bg-gray-700 text-gray-300') }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 
                                        {{ $brief->status == 'active' ? 'bg-green-400' : 
                                          ($brief->status == 'draft' ? 'bg-yellow-400' : 
                                          'bg-gray-400') }}"></span>
                                    {{ ucfirst($brief->status) }}
                                </span>
                            </div>
                            
                            <div class="flex flex-col space-y-2 mb-4 text-sm text-gray-400">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt w-5 text-center mr-2"></i>
                                    <span>Created: {{ $brief->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock w-5 text-center mr-2"></i>
                                <span>Deadline: {{ $brief->deadline ? $brief->deadline->format('M d, Y') : 'No deadline' }}</span>
                            </div>
                        </div>
                        
                            <div class="mb-4">
                                <p class="text-gray-300">
                                {{ Str::limit($brief->description, 150) }}
                            </p>
                        </div>
                        
                            <div class="grid grid-cols-3 gap-2 mb-6">
                                <div class="flex flex-col items-center p-2 bg-gray-750 rounded-lg">
                                    <div class="text-xs text-gray-400">Students</div>
                                    <div class="font-bold text-white">{{ $brief->assigned_students_count ?? 0 }}</div>
                                </div>
                                <div class="flex flex-col items-center p-2 bg-gray-750 rounded-lg">
                                    <div class="text-xs text-gray-400">Submissions</div>
                                    <div class="font-bold text-white">{{ $brief->submissions_count ?? 0 }}</div>
                                </div>
                                <div class="flex flex-col items-center p-2 bg-gray-750 rounded-lg">
                                    <div class="text-xs text-gray-400">Evaluations</div>
                                    <div class="font-bold text-white">{{ $brief->evaluations_count ?? 0 }}</div>
                            </div>
                            </div>
                            
                            <div class="flex justify-between space-x-4">
                                <a href="{{ route('teacher.briefs.edit', $brief->id) }}" class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                                    <i class="fas fa-edit mr-2"></i>Edit
                                </a>
                                <a href="{{ route('teacher.briefs.submissions', $brief->id) }}" class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                    <i class="fas fa-upload mr-2"></i>Submissions
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-8">
                {{ $briefs->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-16 bg-gray-800 rounded-xl border border-gray-700">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-700 text-gray-500 mb-4">
                    <i class="fas fa-file-alt text-3xl"></i>
                </div>
                <h3 class="text-lg font-medium text-white mb-2">No briefs found</h3>
                @if(request()->anyFilled(['search', 'status', 'sort']))
                    <p class="text-gray-400 max-w-md mx-auto mb-6">Try adjusting your search filters</p>
                    <a href="{{ route('teacher.briefs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">Reset Filters</a>
                @else
                    <p class="text-gray-400 max-w-md mx-auto mb-6">Get started by creating your first brief</p>
                    <a href="{{ route('teacher.briefs.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-plus-circle mr-2"></i>Create Brief
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<script>
    function toggleDropdown(id) {
        const menu = document.getElementById(`dropdown-menu-${id}`);
        const allMenus = document.querySelectorAll('[id^="dropdown-menu-"]');
        
        // Close all other menus
        allMenus.forEach(item => {
            if (item.id !== `dropdown-menu-${id}`) {
                item.classList.add('hidden');
            }
        });
        
        // Toggle current menu
        menu.classList.toggle('hidden');
        
        // Close menu when clicking outside
        document.addEventListener('click', function closeDropdown(event) {
            const toggle = document.getElementById(`dropdown-toggle-${id}`);
            if (!menu.contains(event.target) && !toggle.contains(event.target)) {
                menu.classList.add('hidden');
                document.removeEventListener('click', closeDropdown);
            }
        });
    }
</script>
@endsection 