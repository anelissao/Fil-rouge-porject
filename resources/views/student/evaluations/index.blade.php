@extends('layouts.app')

@section('title', 'My Evaluations')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-3xl font-bold mb-6">My Evaluations</h1>

        <!-- Tabs -->
        <div class="mb-6 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                <li class="mr-2">
                    <a href="#assigned-evaluations" class="inline-block p-4 border-b-2 border-indigo-600 rounded-t-lg text-indigo-600 active" aria-current="page">
                        Assigned Evaluations
                    </a>
                </li>
            </ul>
        </div>

        <!-- Assigned Evaluations -->
        <div id="assigned-evaluations">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-6">
                    @if(count($evaluations) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Brief
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Submitted By
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Due Date
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($evaluations as $evaluation)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ Str::limit($evaluation->submission->brief->title, 40) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $evaluation->submission->student->username }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm {{ $evaluation->is_overdue ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                                    {{ $evaluation->due_at ? $evaluation->due_at->format('M d, Y') : 'No deadline' }}
                                                    @if($evaluation->is_overdue)
                                                        <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Overdue</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($evaluation->status == 'completed')
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Completed
                                                    </span>
                                                @elseif($evaluation->status == 'in_progress')
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        In Progress
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                @if($evaluation->status == 'completed')
                                                    <a href="{{ route('evaluations.show', $evaluation->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                                @else
                                                    <a href="{{ route('evaluations.edit', $evaluation->id) }}" class="text-indigo-600 hover:text-indigo-900">Complete</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $evaluations->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-2">
                                <i class="fas fa-clipboard-check text-4xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">No evaluations assigned</h3>
                            <p class="text-gray-500">You haven't been assigned any evaluations yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Evaluations I Need to Complete -->
        <div class="bg-white shadow rounded-lg overflow-hidden mb-8 mt-8">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Evaluations To Complete</h2>
                <p class="text-sm text-gray-600">Evaluations assigned to you that need to be completed.</p>
            </div>
            
            <div class="p-6">
                @if(isset($assignedEvaluations) && count($assignedEvaluations) > 0)
                    <div class="space-y-4">
                        @foreach($assignedEvaluations as $evaluation)
                            <div class="flex items-center justify-between p-4 border rounded-lg {{ isset($evaluation->due_date) && $evaluation->due_date && $evaluation->due_date->isPast() ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-gray-50' }}">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $evaluation->submission->brief->title }}</h3>
                                    <p class="text-sm text-gray-500">
                                        Student: {{ $evaluation->submission->student->username }}
                                        <span class="mx-2">•</span>
                                        Assigned: {{ $evaluation->created_at->format('M d, Y') }}
                                    </p>
                                    @if(isset($evaluation->due_date) && $evaluation->due_date)
                                        <p class="text-sm {{ $evaluation->due_date->isPast() ? 'text-red-600 font-medium' : 'text-gray-500' }}">
                                            Due: {{ $evaluation->due_date->format('M d, Y') }}
                                            @if($evaluation->due_date->isPast())
                                                <span class="text-red-600 font-medium">(Overdue)</span>
                                            @endif
                                        </p>
                                    @endif
                                </div>
                                <a href="{{ route('evaluations.edit', $evaluation->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Complete Evaluation
                                </a>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4">
                        {{ $assignedEvaluations->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 mb-2">
                            <i class="fas fa-clipboard-check text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">No pending evaluations</h3>
                        <p class="text-gray-500">You don't have any evaluations assigned to you at the moment.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Evaluations I've Received -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Evaluations Received</h2>
                <p class="text-sm text-gray-600">Evaluations of your submissions completed by other students.</p>
            </div>
            
            <div class="p-6">
                @if(isset($receivedEvaluations) && count($receivedEvaluations) > 0)
                    <div class="space-y-4">
                        @foreach($receivedEvaluations as $evaluation)
                            <div class="flex items-center justify-between p-4 border rounded-lg border-gray-200 bg-gray-50">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $evaluation->submission->brief->title }}</h3>
                                    <p class="text-sm text-gray-500">
                                        Evaluator: {{ $evaluation->evaluator->username }}
                                        <span class="mx-2">•</span>
                                        {{ $evaluation->status === 'completed' ? 'Completed: ' . $evaluation->completed_at->format('M d, Y') : 'Status: Pending' }}
                                    </p>
                                </div>
                                <div>
                                    @if($evaluation->status === 'completed')
                                        <a href="{{ route('evaluations.show', $evaluation->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            View Results
                                        </a>
                                    @else
                                        <span class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-gray-100">
                                            Pending
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4">
                        {{ $receivedEvaluations->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 mb-2">
                            <i class="fas fa-award text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">No evaluations received</h3>
                        <p class="text-gray-500">You haven't received any evaluations on your submissions yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
