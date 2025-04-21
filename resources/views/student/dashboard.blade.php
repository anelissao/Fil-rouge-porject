@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-2">Dashboard</h1>
    <p class="text-gray-600 mb-6">Welcome back, {{ Auth::user()->first_name }}!</p>

    <!-- Summary Statistics -->
    <div class="stats-grid mb-6">
        <div class="stat-card bg-blue-100">
            <div class="stat-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $totalSubmissions }}</div>
                <div class="stat-label">Total Submissions</div>
            </div>
        </div>
        <div class="stat-card bg-green-100">
            <div class="stat-icon">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $pendingSubmissions }}</div>
                <div class="stat-label">Pending Submissions</div>
            </div>
        </div>
        <div class="stat-card bg-yellow-100">
            <div class="stat-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $pendingEvaluations }}</div>
                <div class="stat-label">Pending Evaluations</div>
            </div>
        </div>
        <div class="stat-card bg-purple-100">
            <div class="stat-icon">
                <i class="fas fa-award"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $receivedEvaluations }}</div>
                <div class="stat-label">Received Evaluations</div>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Active Briefs Section -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2 class="text-xl font-semibold">Active Briefs</h2>
            </div>
            <div class="card-body">
                @if(count($activeBriefs) > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($activeBriefs as $brief)
                            <li class="py-3">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h3 class="font-medium">{{ $brief->title }}</h3>
                                        <p class="text-sm text-gray-500">
                                            Due: {{ $brief->end_date->format('M d, Y') }}
                                        </p>
                                    </div>
                                    <div class="flex space-x-2">
                                        @if($brief->hasSubmitted)
                                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Submitted</span>
                                        @else
                                            <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">Pending</span>
                                        @endif
                                        <a href="{{ route('briefs.show', $brief->id) }}" class="text-blue-600 hover:underline text-sm">View</a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 italic py-4">No active briefs available at the moment.</p>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('briefs.index') }}" class="text-blue-600 hover:underline">View all briefs</a>
            </div>
        </div>

        <!-- My Evaluations Section -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2 class="text-xl font-semibold">My Evaluations</h2>
            </div>
            <div class="card-body">
                @if(count($evaluations) > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($evaluations as $evaluation)
                            <li class="py-3">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h3 class="font-medium">{{ $evaluation->submission->brief->title }}</h3>
                                        <p class="text-sm text-gray-500">
                                            Student: {{ $evaluation->submission->user->username }}
                                        </p>
                                    </div>
                                    <div class="flex space-x-2">
                                        @if($evaluation->is_overdue)
                                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">Overdue</span>
                                        @else
                                            <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">Pending</span>
                                        @endif
                                        <a href="{{ route('student.evaluations.edit', $evaluation->id) }}" class="text-blue-600 hover:underline text-sm">Evaluate</a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 italic py-4">No evaluations assigned to you at the moment.</p>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('student.evaluations.index') }}" class="text-blue-600 hover:underline">View all evaluations</a>
            </div>
        </div>

        <!-- My Results Section -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2 class="text-xl font-semibold">My Results</h2>
            </div>
            <div class="card-body">
                @if(count($receivedEvaluationsList) > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($receivedEvaluationsList as $evaluation)
                            <li class="py-3">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h3 class="font-medium">{{ $evaluation->submission->brief->title }}</h3>
                                        <p class="text-sm text-gray-500">
                                            Evaluated by: {{ $evaluation->evaluator->username }}
                                        </p>
                                    </div>
                                    <div>
                                        <a href="{{ route('student.evaluations.show', $evaluation->id) }}" class="text-blue-600 hover:underline text-sm">View Results</a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 italic py-4">No evaluation results received yet.</p>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('student.evaluations.index') }}" class="text-blue-600 hover:underline">View all results</a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2 class="text-xl font-semibold">Quick Actions</h2>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 gap-3">
                    <a href="{{ route('briefs.index') }}" class="action-link">
                        <i class="fas fa-book"></i>
                        <span>View All Briefs</span>
                    </a>
                    <a href="{{ route('student.submissions.index') }}" class="action-link">
                        <i class="fas fa-file-upload"></i>
                        <span>My Submissions</span>
                    </a>
                    <a href="{{ route('student.evaluations.index') }}" class="action-link">
                        <i class="fas fa-clipboard-check"></i>
                        <span>My Evaluations</span>
                    </a>
                    <a href="{{ route('student.evaluations.index') }}" class="action-link">
                        <i class="fas fa-award"></i>
                        <span>View My Results</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 1rem;
    }
    
    @media (min-width: 640px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (min-width: 1024px) {
        .stats-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }
    
    .stat-card {
        display: flex;
        align-items: center;
        padding: 1.25rem;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .stat-icon {
        font-size: 1.5rem;
        margin-right: 1rem;
        color: rgba(0, 0, 0, 0.7);
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: rgba(0, 0, 0, 0.6);
    }
    
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 1.5rem;
    }
    
    @media (min-width: 1024px) {
        .dashboard-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    .dashboard-card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        background-color: rgba(0, 0, 0, 0.01);
    }
    
    .card-body {
        padding: 1.25rem;
    }
    
    .card-footer {
        padding: 0.75rem 1.25rem;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        background-color: rgba(0, 0, 0, 0.01);
    }
    
    .action-link {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        background-color: rgba(0, 0, 0, 0.03);
        border-radius: 0.375rem;
        transition: all 0.2s;
    }
    
    .action-link:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }
    
    .action-link i {
        margin-right: 0.75rem;
    }
</style>
@endsection 