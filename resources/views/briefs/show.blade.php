@extends('layouts.app')

@section('title', $brief->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section with Gradient Background -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-lg mb-8 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div class="mb-4 md:mb-0">
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('briefs.index') }}" class="text-blue-200 hover:text-white transition-colors">
                                Briefs
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-blue-200 mx-2 text-xs"></i>
                                <span class="text-white">{{ $brief->title }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-bold text-white mb-2">{{ $brief->title }}</h1>
                <div class="flex flex-wrap items-center gap-3">
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
                    <span class="text-blue-200 inline-flex items-center">
                        <i class="fas fa-user mr-1.5"></i> {{ $brief->teacher->username ?? 'Unknown Teacher' }}
                    </span>
                    @if($brief->status == 'published')
                        <span class="text-blue-200 inline-flex items-center">
                            <i class="fas fa-calendar mr-1.5"></i> Deadline: {{ $brief->deadline ? $brief->deadline->format('M d, Y') : 'No deadline' }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                @if(auth()->user()->isTeacher() && $brief->teacher_id == auth()->id())
                    <a href="{{ route('teacher.briefs.edit', $brief->id) }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg backdrop-blur-sm transition-all duration-300 border border-white/20">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                @endif
                
                @if(auth()->user()->isStudent() && $brief->status == 'published' && !$brief->isExpired())
                    @if($hasSubmitted)
                        <a href="{{ route('student.submissions.index', ['brief_id' => $brief->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600/20 hover:bg-green-600/30 text-green-400 rounded-lg transition-all duration-300 border border-green-600/20">
                            <i class="fas fa-check-circle mr-2"></i> Submitted
                        </a>
                    @else
                        <a href="{{ route('student.submissions.create', ['brief_id' => $brief->id]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-300">
                            <i class="fas fa-upload mr-2"></i> Submit
                        </a>
                    @endif
                @endif
                
                <a href="{{ route('briefs.index') }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg backdrop-blur-sm transition-all duration-300 border border-white/20">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Briefs
                </a>
            </div>
        </div>
    </div>

    <!-- Brief Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700 mb-8">
                <div class="border-b border-gray-700 px-6 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-white">Brief Description</h2>
                    @if($brief->status == 'published')
                        <span class="px-3 py-1 rounded-full text-sm font-medium inline-flex items-center
                            {{ $brief->isExpired() ? 'bg-red-900/30 text-red-400' : 'bg-green-900/30 text-green-400' }}">
                            <i class="fas fa-clock mr-2"></i> 
                            {{ $brief->isExpired() ? 'Expired' : ($brief->deadline ? 'Due ' . $brief->deadline->diffForHumans() : 'No deadline') }}
                        </span>
                    @endif
                </div>
                <div class="p-6">
                    <div class="text-gray-300 whitespace-pre-line">
                        {!! nl2br(e($brief->description)) !!}
                    </div>
                </div>
            </div>

            @if(count($brief->criteria) > 0)
                <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700 mb-8">
                    <div class="border-b border-gray-700 px-6 py-4">
                        <h2 class="text-xl font-bold text-white">Evaluation Criteria</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-6">
                            @foreach($brief->criteria as $index => $criterion)
                                <div class="{{ !$loop->last ? 'pb-6 border-b border-gray-700' : '' }}">
                                    <h3 class="text-lg font-bold text-white mb-2">{{ $index + 1 }}. {{ $criterion->title }}</h3>
                                    <p class="text-gray-400 mb-3">
                                        {{ $criterion->description }}
                                    </p>
                                    <div>
                                        <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center bg-blue-900/30 text-blue-400">
                                            Weight: {{ $criterion->weight }}%
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div>
            <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700 mb-8">
                <div class="border-b border-gray-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">Brief Details</h2>
                </div>
                <div class="divide-y divide-gray-700">
                    <div class="px-6 py-4 flex justify-between items-center">
                        <span class="text-gray-300 flex items-center"><i class="fas fa-clock mr-2 text-gray-500"></i> Created</span>
                        <span class="text-white">{{ $brief->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="px-6 py-4 flex justify-between items-center">
                        <span class="text-gray-300 flex items-center"><i class="fas fa-calendar-alt mr-2 text-gray-500"></i> Deadline</span>
                        <span class="text-white">{{ $brief->deadline ? $brief->deadline->format('M d, Y') : 'No deadline' }}</span>
                    </div>
                    <div class="px-6 py-4 flex justify-between items-center">
                        <span class="text-gray-300 flex items-center"><i class="fas fa-upload mr-2 text-gray-500"></i> Submissions</span>
                        <span class="text-white">{{ $brief->submissions_count ?? 0 }}</span>
                    </div>
                    <div class="px-6 py-4 flex justify-between items-center">
                        <span class="text-gray-300 flex items-center"><i class="fas fa-list-check mr-2 text-gray-500"></i> Criteria</span>
                        <span class="text-white">{{ $brief->criteria->count() }}</span>
                    </div>
                </div>
            </div>

            @if(auth()->user()->isTeacher() && $brief->teacher_id == auth()->id())
                <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700">
                    <div class="border-b border-gray-700 px-6 py-4">
                        <h2 class="text-xl font-bold text-white">Teacher Actions</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <a href="{{ route('teacher.briefs.submissions', $brief->id) }}" class="flex items-center justify-center px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors w-full">
                            <i class="fas fa-file-upload mr-2"></i> View Submissions
                        </a>
                        <a href="{{ route('teacher.briefs.results', $brief->id) }}" class="flex items-center justify-center px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors w-full">
                            <i class="fas fa-chart-bar mr-2"></i> View Results
                        </a>
                        
                        @if($brief->status == 'draft')
                            <form action="{{ route('teacher.briefs.publish', $brief->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors w-full">
                                    <i class="fas fa-globe mr-2"></i> Publish Brief
                                </button>
                            </form>
                        @elseif($brief->status == 'published')
                            <form action="{{ route('teacher.briefs.unpublish', $brief->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="flex items-center justify-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors w-full">
                                    <i class="fas fa-eye-slash mr-2"></i> Unpublish Brief
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 