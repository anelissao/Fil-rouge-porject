@extends('layouts.app')

@section('title', $brief->title)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h1 class="text-2xl md:text-3xl font-bold text-white">{{ $brief->title }}</h1>
                    <span class="px-3 py-1 rounded-full text-xs font-medium 
                        {{ $brief->status == 'draft' ? 'bg-gray-700 text-gray-300' : 
                        ($brief->status == 'active' ? 'bg-green-900/50 text-green-400' : 
                        'bg-red-900/50 text-red-400') }}">
                        {{ ucfirst($brief->status) }}
                    </span>
                </div>
                <p class="text-blue-400">Created {{ $brief->created_at->format('M d, Y') }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('teacher.briefs.edit', $brief->id) }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors duration-300 inline-flex items-center">
                    <i class="fas fa-edit mr-2"></i> Edit Brief
                </a>
                <a href="{{ route('teacher.briefs.index') }}" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-lg transition-colors duration-300 inline-flex items-center border border-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Briefs
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Brief Stats -->
                <div class="bg-gray-800 rounded-xl p-5 shadow-md">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <i class="fas fa-chart-bar mr-2 text-blue-400"></i>Brief Stats
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-700">
                            <span class="text-gray-400">Deadline</span>
                            <div class="text-right">
                                <div class="text-white flex items-center">
                                    <i class="fas fa-calendar-alt mr-2 text-blue-400"></i>
                                    {{ $brief->deadline->format('M d, Y') }}
                                </div>
                                <div class="text-white text-sm">{{ $brief->deadline->format('g:i A') }}</div>
                                <span class="mt-1 inline-block px-2 py-0.5 rounded text-xs
                                    {{ $brief->isExpired() ? 'bg-red-900/50 text-red-400' : 'bg-green-900/50 text-green-400' }}">
                                    {{ $brief->isExpired() ? 'Expired' : 'Active' }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center pb-3 border-b border-gray-700">
                            <span class="text-gray-400">Submissions</span>
                            <div class="text-white flex items-center">
                                <i class="fas fa-upload mr-2 text-green-400"></i>
                                {{ $submissionsCount ?? 0 }} total
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center pb-3 border-b border-gray-700">
                            <span class="text-gray-400">Assigned</span>
                            <div class="text-white flex items-center">
                                <i class="fas fa-users mr-2 text-purple-400"></i>
                                {{ $brief->assigned_students_count ?? 0 }} students
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Evaluations</span>
                            <div class="text-white flex items-center">
                                <i class="fas fa-star mr-2 text-yellow-400"></i>
                                {{ $brief->evaluations_count ?? 0 }} completed
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-gray-800 rounded-xl p-5 shadow-md">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <i class="fas fa-cog mr-2 text-blue-400"></i>Actions
                    </h3>
                    <div class="space-y-2">
                        <a href="{{ route('teacher.briefs.submissions', $brief->id) }}" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition-colors text-white">
                            <i class="fas fa-upload w-6 text-green-400"></i> View Submissions
                        </a>
                        <a href="{{ route('teacher.briefs.results', $brief->id) }}" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition-colors text-white">
                            <i class="fas fa-chart-bar w-6 text-blue-400"></i> View Results
                        </a>
                        <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition-colors text-white">
                            <i class="fas fa-share-alt w-6 text-purple-400"></i> Share with Students
                        </a>
                        <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition-colors text-white">
                            <i class="fas fa-download w-6 text-yellow-400"></i> Download Brief
                        </a>
                        
                        @if($brief->isDraft())
                            <form action="{{ route('teacher.briefs.publish', $brief->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center p-3 rounded-lg hover:bg-gray-700 transition-colors text-green-400 text-left">
                                    <i class="fas fa-share-square w-6"></i> Publish Brief
                                </button>
                            </form>
                        @elseif($brief->isActive())
                            <form action="{{ route('teacher.briefs.unpublish', $brief->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center p-3 rounded-lg hover:bg-gray-700 transition-colors text-yellow-400 text-left">
                                    <i class="fas fa-eye-slash w-6"></i> Unpublish Brief
                                </button>
                            </form>
                        @endif
                        
                        @if($submissionsCount == 0)
                            <form action="{{ route('teacher.briefs.destroy', $brief->id) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this brief? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full flex items-center p-3 rounded-lg hover:bg-gray-700 transition-colors text-red-400 text-left">
                                    <i class="fas fa-trash-alt w-6"></i> Delete Brief
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Brief Description -->
                <div class="bg-gray-800 rounded-xl p-6 shadow-md">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <i class="fas fa-file-alt mr-2 text-blue-400"></i>Brief Description
                    </h3>
                    <div class="text-gray-300 whitespace-pre-line">
                        {{ $brief->description }}
                    </div>
                </div>

                <!-- Attachment -->
                @if(isset($brief->attachment_path))
                    <div class="bg-gray-800 rounded-xl p-6 shadow-md">
                        <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                            <i class="fas fa-paperclip mr-2 text-blue-400"></i>Attachment
                        </h3>
                        <a href="{{ Storage::url($brief->attachment_path) }}" target="_blank" class="flex items-center p-4 bg-gray-700/50 rounded-lg border border-gray-700 hover:bg-gray-700 transition-colors">
                            <i class="fas fa-file-alt text-2xl text-blue-400 mr-3"></i>
                            <span class="text-white">View attached document</span>
                            <i class="fas fa-external-link-alt ml-auto text-gray-400"></i>
                        </a>
                    </div>
                @endif

                <!-- Evaluation Criteria -->
                <div class="bg-gray-800 rounded-xl p-6 shadow-md">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <i class="fas fa-clipboard-list mr-2 text-blue-400"></i>Evaluation Criteria
                    </h3>
                    
                    @if(isset($criteria) && count($criteria) > 0)
                        <div class="space-y-5">
                            @foreach($criteria as $criterion)
                                <div class="border border-gray-700 rounded-lg p-4 bg-gray-700/30">
                                    <h4 class="text-lg font-medium text-white mb-2">{{ $criterion->title }}</h4>
                                    <p class="text-gray-300 mb-4">{{ $criterion->description }}</p>
                                    
                                    @if($criterion->tasks->count() > 0)
                                        <div class="mt-3 space-y-2">
                                            @foreach($criterion->tasks as $task)
                                                <div class="flex items-start">
                                                    <i class="fas fa-check-circle text-green-400 mt-1 mr-2"></i>
                                                    <span class="text-gray-300">{{ $task->description }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-700/30 rounded-lg border border-dashed border-gray-600">
                            <i class="fas fa-clipboard-list text-3xl text-gray-500 mb-2"></i>
                            <p class="text-gray-400 mb-4">No evaluation criteria have been defined for this brief.</p>
                            <a href="{{ route('teacher.briefs.edit', $brief->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-300 inline-flex items-center">
                                Add Criteria
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Skills & Competencies -->
                @if(isset($brief->skills) && !empty($brief->skills))
                    <div class="bg-gray-800 rounded-xl p-6 shadow-md">
                        <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                            <i class="fas fa-code-branch mr-2 text-blue-400"></i>Skills & Competencies
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach(explode(',', $brief->skills) as $skill)
                                <span class="px-3 py-1 bg-blue-900/30 text-blue-400 rounded-full text-sm">{{ trim($skill) }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Assigned Students -->
                @if(isset($brief->assigned_students) && count($brief->assigned_students) > 0)
                    <div class="bg-gray-800 rounded-xl p-6 shadow-md">
                        <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                            <i class="fas fa-users mr-2 text-blue-400"></i>Assigned Students
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($brief->assigned_students as $student)
                                <div class="flex items-center p-3 bg-gray-700/30 rounded-lg">
                                    <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center mr-3 text-blue-400">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-white">{{ $student->first_name }} {{ $student->last_name }}</div>
                                        <div class="text-sm text-gray-400">{{ $student->email }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection 