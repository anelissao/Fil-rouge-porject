@extends('layouts.app')

@section('title', 'My Submissions')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section with Gradient Background -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-lg mb-8 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div class="mb-4 md:mb-0">
                <h1 class="text-3xl font-bold text-white mb-2">My Submissions</h1>
                <p class="text-blue-100">View and manage your submissions to briefs</p>
            </div>
            <div>
                <a href="{{ route('briefs.index') }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg backdrop-blur-sm transition-all duration-300 border border-white/20">
                    <i class="fas fa-file-alt mr-2"></i> View Briefs
                </a>
            </div>
        </div>
    </div>

    <!-- Submissions List -->
    <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700">
        <div class="px-6 py-4">
            @if(isset($submissions) && count($submissions) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Brief</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Submitted On</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Evaluations</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($submissions as $submission)
                                <tr class="hover:bg-gray-750 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-white">{{ $submission->brief->title }}</div>
                                        <div class="text-sm text-gray-400">by {{ $submission->brief->teacher->username }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-white">{{ $submission->created_at->format('M d, Y') }}</div>
                                        <div class="text-sm text-gray-400">{{ $submission->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center bg-green-900/30 text-green-400">
                                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-green-400"></span>
                                            Submitted
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm text-white">
                                                {{ $submission->completed_evaluations }} / {{ $submission->total_evaluations }}
                                            </div>
                                            @if($submission->completed_evaluations > 0)
                                                <div class="ml-2 text-xs px-2 py-0.5 bg-blue-900/30 text-blue-400 rounded-full">
                                                    {{ round(($submission->completed_evaluations / max(1, $submission->total_evaluations)) * 100) }}%
                                                </div>
                                            @endif
                                        </div>
                                        <div class="w-full bg-gray-700 rounded-full h-1.5 mt-2">
                                            <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ ($submission->completed_evaluations / max(1, $submission->total_evaluations)) * 100 }}%"></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <a href="{{ route('student.submissions.show', $submission->id) }}" class="text-blue-400 hover:text-blue-300 transition-colors font-medium">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-6 px-6 py-4 border-t border-gray-700">
                    {{ $submissions->links() }}
                </div>
            @else
                <div class="py-16 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-700 text-gray-500 mb-4">
                        <i class="fas fa-file-upload text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-medium text-white mb-2">No submissions yet</h3>
                    <p class="text-gray-400 max-w-md mx-auto mb-6">You haven't submitted any work to briefs yet.</p>
                    <a href="{{ route('briefs.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-search mr-2"></i> View Available Briefs
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 