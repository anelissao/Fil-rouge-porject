@extends('layouts.app')

@section('title', 'My Evaluations')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Page Header -->
        <div class="mb-10">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">My Evaluations</h1>
            <p class="mt-3 text-base text-gray-600 max-w-3xl">Manage and track your peer evaluations efficiently. Review submissions and provide valuable feedback to your peers.</p>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Pending</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ count($evaluations) }}</p>
                    </div>
                    <div class="rounded-full p-3 bg-yellow-50">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Completed</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">
                            {{ $evaluations->where('status', 'completed')->count() }}
                        </p>
                    </div>
                    <div class="rounded-full p-3 bg-green-50">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Received</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">
                            {{ isset($receivedEvaluations) ? count($receivedEvaluations) : 0 }}
                        </p>
                    </div>
                    <div class="rounded-full p-3 bg-indigo-50">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-8">
            <!-- Evaluations to Complete -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Evaluations to Complete</h2>
                            <p class="mt-1 text-sm text-gray-500">Review and evaluate your peers' work</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ count($evaluations) > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                            {{ count($evaluations) }} Pending
                        </span>
                    </div>
                </div>

                @if(count($evaluations) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brief</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted By</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($evaluations as $evaluation)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-medium text-gray-900">{{ Str::limit($evaluation->submission->brief->title, 40) }}</span>
                                                <span class="mt-0.5 text-xs text-gray-500">Brief #{{ $evaluation->submission->brief->id }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <span class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-indigo-700">
                                                        {{ strtoupper(substr($evaluation->submission->student->username, 0, 1)) }}
                                                    </span>
                                                </span>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">{{ $evaluation->submission->student->username }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm {{ $evaluation->is_overdue ? 'text-red-600 font-medium' : 'text-gray-900' }}">
                                                {{ $evaluation->due_at ? $evaluation->due_at->format('M d, Y') : 'No deadline' }}
                                                @if($evaluation->is_overdue)
                                                    <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-md text-xs font-medium bg-red-100 text-red-800">
                                                        Overdue
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($evaluation->status == 'completed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Completed
                                                </span>
                                            @elseif($evaluation->status == 'in_progress')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <svg class="w-3.5 h-3.5 mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                                    </svg>
                                                    In Progress
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @if($evaluation->status == 'completed')
                                                <a href="{{ route('student.evaluations.show', $evaluation->id) }}" 
                                                   class="inline-flex items-center px-3.5 py-1.5 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 ease-in-out">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    View
                                                </a>
                                            @else
                                                <a href="{{ route('student.evaluations.edit', $evaluation->id) }}" 
                                                   class="inline-flex items-center px-3.5 py-1.5 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 ease-in-out">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                    </svg>
                                                    Complete
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $evaluations->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="rounded-full bg-green-50 p-3 w-12 h-12 mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900">All Caught Up!</h3>
                        <p class="mt-1 text-sm text-gray-500">You have no pending evaluations at the moment.</p>
                    </div>
                @endif
            </div>

            <!-- Received Evaluations -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Received Evaluations</h2>
                            <p class="mt-1 text-sm text-gray-500">Feedback received from your peers</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                            {{ isset($receivedEvaluations) ? count($receivedEvaluations) : 0 }} Total
                        </span>
                    </div>
                </div>

                @if(isset($receivedEvaluations) && count($receivedEvaluations) > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($receivedEvaluations as $evaluation)
                            <div class="p-6 hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                                <div class="sm:flex sm:items-center sm:justify-between">
                                    <div class="mb-4 sm:mb-0">
                                        <div class="flex items-center">
                                            <span class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-indigo-700">
                                                    {{ strtoupper(substr($evaluation->evaluator->username, 0, 1)) }}
                                                </span>
                                            </span>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-gray-900">{{ $evaluation->submission->brief->title }}</h3>
                                                <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                                                    <span class="font-medium">{{ $evaluation->evaluator->username }}</span>
                                                    <span>•</span>
                                                    <span>{{ $evaluation->status === 'completed' ? $evaluation->completed_at->format('M d, Y') : 'Pending' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        @if($evaluation->status === 'completed')
                                            <a href="{{ route('student.evaluations.show', $evaluation->id) }}" 
                                               class="inline-flex items-center px-3.5 py-1.5 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 ease-in-out">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                                View Results
                                            </a>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                </svg>
                                                Pending
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($receivedEvaluations instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="px-6 py-4 border-t border-gray-100">
                            {{ $receivedEvaluations->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="rounded-full bg-gray-50 p-3 w-12 h-12 mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900">No Evaluations Yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Your submissions are waiting to be evaluated by your peers.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
