@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create New Brief</h1>
            <p class="text-sm text-gray-600">Create a new assignment for your students</p>
        </div>
        <a href="{{ route('teacher.briefs.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg text-gray-700 flex items-center gap-2 hover:bg-gray-300 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            Back to Briefs
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('teacher.briefs.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Brief Title</label>
                    <input type="text" name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Enter a descriptive title" value="{{ old('title') }}" required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deadline -->
                <div>
                    <label for="deadline" class="block text-sm font-medium text-gray-700 mb-1">Deadline</label>
                    <input type="datetime-local" name="deadline" id="deadline" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('deadline') }}" required>
                    @error('deadline')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Provide a detailed description of the assignment">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Attachment -->
                <div class="col-span-2">
                    <label for="attachment" class="block text-sm font-medium text-gray-700 mb-1">Attachment (optional)</label>
                    <input type="file" name="attachment" id="attachment" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-500 mt-1">Upload a PDF, DOCX, or other document with detailed instructions (max 10MB)</p>
                    @error('attachment')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Skills/Competencies -->
                <div class="col-span-2">
                    <label for="skills" class="block text-sm font-medium text-gray-700 mb-1">Skills/Competencies (optional)</label>
                    <input type="text" name="skills" id="skills" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. HTML, CSS, JavaScript, Design" value="{{ old('skills') }}">
                    <p class="text-xs text-gray-500 mt-1">Comma-separated list of skills this brief will help develop</p>
                    @error('skills')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Assign to Classes -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Assign to Classes</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($classes as $class)
                            <label class="flex items-start p-3 border rounded-md hover:bg-gray-50">
                                <input type="checkbox" name="classes[]" value="{{ $class->id }}" class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ in_array($class->id, old('classes', [])) ? 'checked' : '' }}>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $class->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $class->students->count() }} students</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('classes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-5 border-t border-gray-200 mt-6">
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="window.location='{{ route('teacher.briefs.index') }}'" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit" name="save_draft" value="1" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Save as Draft
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Create Brief
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    /* Custom styling for file input */
    input[type="file"]::file-selector-button {
        border: 0;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        background-color: #EFF6FF;
        color: #2563EB;
        font-weight: 600;
        font-size: 0.875rem;
        margin-right: 1rem;
        cursor: pointer;
        transition: background-color 0.15s ease-in-out;
    }
    
    input[type="file"]::file-selector-button:hover {
        background-color: #DBEAFE;
    }
</style>
@endsection 