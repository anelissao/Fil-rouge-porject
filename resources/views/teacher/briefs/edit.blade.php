@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Edit Brief</h1>
            <p class="text-sm text-blue-400">Update your assignment details</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('teacher.briefs.show', $brief->id) }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-white flex items-center gap-2 transition-colors duration-300">
                <i class="fas fa-eye"></i>
                View Brief
            </a>
            <a href="{{ route('teacher.briefs.index') }}" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 rounded-lg text-white flex items-center gap-2 transition-colors duration-300 border border-gray-700">
                <i class="fas fa-arrow-left"></i>
                Back to Briefs
            </a>
        </div>
    </div>

    <div class="bg-gray-800 rounded-lg shadow-md p-6">
        <form action="{{ route('teacher.briefs.update', $brief->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-300 mb-1">Brief Title</label>
                    <input type="text" name="title" id="title" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Enter a descriptive title" value="{{ old('title', $brief->title) }}" required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deadline -->
                <div>
                    <label for="deadline" class="block text-sm font-medium text-gray-300 mb-1">Deadline</label>
                    <input type="datetime-local" name="deadline" id="deadline" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('deadline', $brief->deadline ? $brief->deadline->format('Y-m-d\TH:i') : '') }}" required>
                    @error('deadline')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-300 mb-1">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="draft" {{ old('status', $brief->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status', $brief->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="expired" {{ old('status', $brief->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-300 mb-1">Description</label>
                    <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Provide a detailed description of the assignment">{{ old('description', $brief->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Attachment -->
                <div class="col-span-2">
                    <label for="attachment" class="block text-sm font-medium text-gray-300 mb-1">Attachment (optional)</label>
                    @if(isset($brief->attachment_path))
                        <div class="flex items-center mb-2 bg-gray-700/50 p-2 rounded-md">
                            <i class="fas fa-file-alt text-blue-400 mr-2"></i>
                            <span class="text-sm text-gray-300">Current file: {{ basename($brief->attachment_path) }}</span>
                        </div>
                    @endif
                    <div class="mt-1 flex items-center">
                        <label class="block w-full">
                            <span class="sr-only">Choose file</span>
                            <input type="file" name="attachment" id="attachment" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-600/20 file:text-blue-400 hover:file:bg-blue-600/30 cursor-pointer">
                        </label>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Upload a new file to replace the current one (PDF, DOCX, or other document, max 10MB)</p>
                    @error('attachment')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Skills/Competencies -->
                <div class="col-span-2">
                    <label for="skills" class="block text-sm font-medium text-gray-300 mb-1">Skills/Competencies (optional)</label>
                    <input type="text" name="skills" id="skills" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. HTML, CSS, JavaScript, Design" value="{{ old('skills', $brief->skills ?? '') }}">
                    <p class="text-xs text-gray-400 mt-1">Comma-separated list of skills this brief will help develop</p>
                    @error('skills')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Assign to Classes -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Assign to Classes</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($classes as $class)
                            <label class="flex items-start p-3 border border-gray-600 rounded-md bg-gray-700/50 hover:bg-gray-700 transition-colors">
                                <input type="checkbox" name="classes[]" value="{{ $class->id }}" 
                                       class="mt-1 h-4 w-4 rounded border-gray-500 text-blue-600 focus:ring-blue-500 focus:ring-offset-gray-800" 
                                       {{ in_array($class->id, old('classes', $assignedClasses ?? [])) ? 'checked' : '' }}>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-white">{{ $class->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $class->students && is_object($class->students) ? $class->students->count() : 0 }} students</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('classes')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Criteria Section -->
                <div class="col-span-2 border-t border-gray-700 pt-5">
                    <h3 class="text-lg font-medium text-white mb-3">Evaluation Criteria</h3>
                    
                    @if(isset($criteria) && count($criteria) > 0)
                        <div class="space-y-4">
                            @foreach($criteria as $criterion)
                                <div class="border border-gray-700 rounded-lg p-4 bg-gray-800/50">
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-300 mb-1">Criterion Title</label>
                                        <input type="text" value="{{ $criterion->title }}" class="mt-1 block w-full rounded-md bg-gray-700/50 border-gray-600 text-white shadow-sm" readonly>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-300 mb-1">Description</label>
                                        <textarea class="mt-1 block w-full rounded-md bg-gray-700/50 border-gray-600 text-white shadow-sm" rows="2" readonly>{{ $criterion->description }}</textarea>
                                    </div>
                                    
                                    @if($criterion->tasks->count() > 0)
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-300 mb-1">Tasks</label>
                                            <ul class="list-disc pl-5 space-y-1 text-sm text-gray-300">
                                                @foreach($criterion->tasks as $task)
                                                    <li>{{ $task->description }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    
                                    <div class="flex justify-end">
                                        <button type="button" class="px-3 py-1 border border-gray-600 rounded-md shadow-sm text-xs font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
                                            Edit Criterion
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-700/30 rounded-lg border border-dashed border-gray-600">
                            <i class="fas fa-clipboard-list text-3xl text-gray-500 mb-2"></i>
                            <p class="text-sm text-gray-400">No evaluation criteria have been defined for this brief.</p>
                            <button type="button" class="mt-4 px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                                Add Criteria
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="pt-5 border-t border-gray-700 mt-6">
                <div class="flex justify-end gap-3">
                    <a href="{{ route('teacher.briefs.show', $brief->id) }}" class="px-4 py-2 border border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-blue-500 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" name="save_draft" value="1" class="px-4 py-2 border border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-blue-500 transition-colors">
                        Save as Draft
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-blue-500 transition-colors">
                        Update Brief
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection 