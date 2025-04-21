@extends('layouts.app')

@section('title', 'Create Submission')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-3xl font-bold mb-2">Create Submission</h1>
        
        @if(isset($brief))
            <p class="text-gray-600 mb-6">Submitting to: <span class="font-medium">{{ $brief->title }}</span></p>
            
            <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Brief Details</h2>
                    
                    <div class="mb-6 prose max-w-none">
                        {!! $brief->description !!}
                    </div>
                    
                    <div class="flex items-center text-sm text-gray-600 mb-6">
                        <div class="mr-6">
                            <span class="font-medium">Teacher:</span> {{ $brief->teacher->username }}
                        </div>
                        <div>
                            <span class="font-medium">Deadline:</span> {{ $brief->end_date->format('M d, Y') }}
                        </div>
                    </div>
                    
                    @if($brief->criteria->count() > 0)
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Evaluation Criteria</h3>
                            <ul class="list-disc list-inside space-y-1 text-gray-700">
                                @foreach($brief->criteria as $criterion)
                                    <li>{{ $criterion->description }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Your Submission</h2>
                    
                    <form action="{{ route('student.submissions.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="brief_id" value="{{ $brief->id }}">
                        
                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                            <textarea id="content" name="content" rows="6" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('content') border-red-500 @enderror" placeholder="Enter your submission content here. You can also upload a file below.">{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-6">
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">File Attachment (optional)</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <i class="fas fa-upload text-gray-400 text-3xl mb-2"></i>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="file" class="relative cursor-pointer rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Upload a file</span>
                                            <input id="file" name="file" type="file" class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        PDF, DOCX, PPTX, ZIP or other files up to 10MB
                                    </p>
                                </div>
                            </div>
                            @error('file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex items-center justify-end">
                            <a href="{{ route('briefs.show', $brief->id) }}" class="mr-4 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Submit Work
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-6 text-center">
                    <i class="fas fa-exclamation-circle text-yellow-500 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">No brief selected</h3>
                    <p class="text-gray-500 mb-4">Please select a brief to create a submission for.</p>
                    <a href="{{ route('briefs.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        View Available Briefs
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection 