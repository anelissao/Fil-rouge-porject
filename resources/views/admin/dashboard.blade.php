@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Admin Dashboard Header -->
    <div class="bg-gradient-to-r from-gray-800 via-gray-700 to-gray-800 rounded-xl shadow-lg p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">Admin Dashboard</h1>
                <p class="text-blue-300">Welcome back, {{ Auth::user()->first_name }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('home') }}" class="px-4 py-2 bg-gray-700/50 hover:bg-gray-600/50 text-white rounded-lg transition-colors duration-300 inline-flex items-center backdrop-blur-sm border border-gray-600/30">
                    <i class="fas fa-home mr-2"></i> Home
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-gray-800 rounded-xl p-6 shadow-md flex items-center">
            <div class="w-12 h-12 rounded-full bg-blue-600/20 flex items-center justify-center mr-4 text-blue-400">
                <i class="fas fa-users text-xl"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-white">{{ \App\Models\User::count() }}</h3>
                <p class="text-gray-400">Total Users</p>
            </div>
        </div>
        
        <div class="bg-gray-800 rounded-xl p-6 shadow-md flex items-center">
            <div class="w-12 h-12 rounded-full bg-green-600/20 flex items-center justify-center mr-4 text-green-400">
                <i class="fas fa-user-graduate text-xl"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-white">{{ \App\Models\User::where('role', 'student')->count() }}</h3>
                <p class="text-gray-400">Students</p>
            </div>
        </div>
        
        <div class="bg-gray-800 rounded-xl p-6 shadow-md flex items-center">
            <div class="w-12 h-12 rounded-full bg-purple-600/20 flex items-center justify-center mr-4 text-purple-400">
                <i class="fas fa-chalkboard-teacher text-xl"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-white">{{ \App\Models\User::where('role', 'teacher')->count() }}</h3>
                <p class="text-gray-400">Teachers</p>
            </div>
        </div>
        
        <div class="bg-gray-800 rounded-xl p-6 shadow-md flex items-center">
            <div class="w-12 h-12 rounded-full bg-yellow-600/20 flex items-center justify-center mr-4 text-yellow-400">
                <i class="fas fa-file-alt text-xl"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-white">{{ \App\Models\Brief::count() }}</h3>
                <p class="text-gray-400">Total Briefs</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Quick Actions -->
        <div class="bg-gray-800 rounded-xl shadow-md p-6">
            <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                <i class="fas fa-bolt mr-2 text-blue-400"></i>Quick Actions
            </h3>
            <div class="space-y-2">
                <a href="{{ route('admin.users.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition-colors text-white">
                    <i class="fas fa-users w-6 text-blue-400"></i> Manage Users
                </a>
                <a href="{{ route('admin.briefs.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition-colors text-white">
                    <i class="fas fa-file-alt w-6 text-green-400"></i> Manage Briefs
                </a>
                <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition-colors text-white">
                    <i class="fas fa-cog w-6 text-purple-400"></i> System Settings
                </a>
                <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition-colors text-white">
                    <i class="fas fa-chart-bar w-6 text-yellow-400"></i> Analytics
                </a>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-gray-800 rounded-xl shadow-md p-6 lg:col-span-2">
            <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                <i class="fas fa-user-clock mr-2 text-blue-400"></i>Recent Users
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-900/60 border-b border-gray-700">
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-300">User</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-300">Role</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-300">Joined</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(\App\Models\User::orderBy('created_at', 'desc')->take(5)->get() as $user)
                            <tr class="border-b border-gray-700 hover:bg-gray-700/50 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center mr-3 text-blue-400 overflow-hidden">
                                            @if($user->avatar)
                                                <img src="{{ $user->avatar_url }}" alt="{{ $user->username }}" class="w-full h-full object-cover">
                                            @else
                                                <i class="fas fa-user"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-medium text-white">{{ $user->first_name }} {{ $user->last_name }}</div>
                                            <div class="text-sm text-gray-400">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                        {{ $user->role == 'admin' ? 'bg-red-900/50 text-red-400' : 
                                        ($user->role == 'teacher' ? 'bg-purple-900/50 text-purple-400' : 
                                        'bg-green-900/50 text-green-400') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-gray-300">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex gap-2">
                                        <a href="#" class="p-1 bg-blue-600/20 hover:bg-blue-600/40 text-blue-400 rounded transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="p-1 bg-yellow-600/20 hover:bg-yellow-600/40 text-yellow-400 rounded transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex justify-end">
                <a href="{{ route('admin.users.index') }}" class="text-blue-400 hover:text-blue-300 text-sm flex items-center">
                    View all users <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
    
    <!-- System Status -->
    <div class="mt-6 bg-gray-800 rounded-xl shadow-md p-6">
        <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
            <i class="fas fa-server mr-2 text-blue-400"></i>System Status
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gray-700/50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-300">Laravel Version</span>
                    <span class="text-blue-400">{{ app()->version() }}</span>
                </div>
                <div class="w-full bg-gray-600 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: 100%"></div>
                </div>
            </div>
            
            <div class="bg-gray-700/50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-300">PHP Version</span>
                    <span class="text-green-400">{{ phpversion() }}</span>
                </div>
                <div class="w-full bg-gray-600 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: 100%"></div>
                </div>
            </div>
            
            <div class="bg-gray-700/50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-300">Database</span>
                    <span class="text-purple-400">PostgreSQL</span>
                </div>
                <div class="w-full bg-gray-600 rounded-full h-2">
                    <div class="bg-purple-500 h-2 rounded-full" style="width: 100%"></div>
                </div>
            </div>
            
            <div class="bg-gray-700/50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-300">Environment</span>
                    <span class="text-yellow-400">{{ app()->environment() }}</span>
                </div>
                <div class="w-full bg-gray-600 rounded-full h-2">
                    <div class="bg-yellow-500 h-2 rounded-full" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 