@extends('layouts.app')

@section('title', 'My Submissions')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-3xl font-bold mb-2">My Submissions</h1>
        <p class="text-gray-600 mb-6">View and manage your submissions to briefs.</p>

        <!-- Submissions List -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-6">
                @if(isset($submissions) && count($submissions) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brief</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted On</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evaluations</th>
                                    <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($submissions as $submission)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $submission->brief->title }}</div>
                                            <div class="text-sm text-gray-500">by {{ $submission->brief->teacher->username }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $submission->created_at->format('M d, Y') }}</div>
                                            <div class="text-sm text-gray-500">{{ $submission->created_at->format('h:i A') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Submitted
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $submission->completed_evaluations }} / {{ $submission->total_evaluations }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('student.submissions.show', $submission->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $submissions->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 mb-2">
                            <i class="fas fa-file-upload text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">No submissions yet</h3>
                        <p class="text-gray-500 mb-4">You haven't submitted any work to briefs yet.</p>
                        <a href="{{ route('briefs.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            View Available Briefs
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection 