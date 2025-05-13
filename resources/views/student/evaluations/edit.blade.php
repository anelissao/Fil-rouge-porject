@extends('layouts.app')

@section('title', 'Complete Evaluation')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section with Gradient Background -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-lg mb-8 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div class="mb-4 md:mb-0">
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('home') }}" class="text-blue-200 hover:text-white transition-colors">
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-blue-200 mx-2 text-xs"></i>
                                <a href="{{ route('student.evaluations.index') }}" class="text-blue-200 hover:text-white transition-colors">
                                    My Evaluations
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-blue-200 mx-2 text-xs"></i>
                                <span class="text-white">Complete Evaluation</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-bold text-white mb-2">Complete Evaluation</h1>
                <p class="text-blue-100">Evaluate your peer's submission</p>
            </div>
            <div>
                <a href="{{ route('student.evaluations.index') }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg backdrop-blur-sm transition-all duration-300 border border-white/20">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Evaluations
                </a>
            </div>
        </div>
    </div>

    <!-- Debug Info - Will show any form errors -->
    @if ($errors->any())
    <div class="bg-red-500/75 text-white p-4 rounded-lg mb-8">
        <h3 class="font-bold">Form Errors:</h3>
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Current Method Info -->
    <div class="bg-blue-500/75 text-white p-4 rounded-lg mb-8">
        <p>Current Route: {{ Request::route()->getName() }}</p>
        <p>Current Method: {{ Request::method() }}</p>
    </div>

    <!-- Submission Info -->
    <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700 mb-8">
        <div class="border-b border-gray-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Submission Details</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-white mb-2">Brief</h3>
                    <p class="text-gray-300 mb-4">{{ $evaluation->submission->brief->title }}</p>

                    <h3 class="text-lg font-medium text-white mb-2">Submitted By</h3>
                    <p class="text-gray-300 mb-4">{{ $evaluation->submission->student->username }}</p>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-white mb-2">Submission Date</h3>
                    <p class="text-gray-300 mb-4">{{ $evaluation->submission->submission_date->format('M d, Y') }}</p>

                    <h3 class="text-lg font-medium text-white mb-2">Due Date</h3>
                    <p class="text-gray-300 mb-4 {{ $evaluation->due_at && $evaluation->due_at->isPast() ? 'text-red-400 font-semibold' : '' }}">
                        {{ $evaluation->due_at ? $evaluation->due_at->format('M d, Y') : 'No deadline set' }}
                        @if($evaluation->due_at && $evaluation->due_at->isPast())
                            <span class="ml-2 px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center bg-red-900/30 text-red-400">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-red-400"></span>
                                Overdue
                            </span>
                        @elseif($evaluation->due_at)
                            <span class="text-sm text-gray-400">({{ $evaluation->due_at->diffForHumans() }})</span>
                        @endif
                    </p>
                </div>
            </div>

            @if($evaluation->submission->content)
                <div class="mt-6">
                    <h3 class="text-lg font-medium text-white mb-2">Submission Content</h3>
                    <div class="bg-gray-750 p-4 rounded-lg border border-gray-700">
                        <div class="text-gray-300 whitespace-pre-line">
                            {!! nl2br(e($evaluation->submission->content)) !!}
                        </div>
                    </div>
                </div>
            @endif

            @if($evaluation->submission->file_path)
                <div class="mt-6">
                    <h3 class="text-lg font-medium text-white mb-2">Attached File</h3>
                    <a href="{{ route('student.submissions.download', $evaluation->submission->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-download mr-2"></i> Download Submission File
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Evaluation Form -->
    <div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700">
        <div class="border-b border-gray-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Evaluation Form</h2>
        </div>
        <div class="p-6">
            <form id="evaluationForm" action="{{ route('student.evaluations.update', $evaluation->id) }}" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PUT">

                <!-- Criteria Evaluation -->
                <h3 class="text-lg font-medium text-white mb-4">Evaluation Criteria</h3>
                <p class="text-gray-400 mb-2">Please evaluate the submission against each of the following criteria:</p>
                <p class="text-red-400 mb-6"><span class="text-red-500">*</span> All criteria must be evaluated to complete the evaluation.</p>

                @foreach($evaluation->submission->brief->criteria as $criterion)
                    <div class="criterion-card bg-gray-750 p-5 rounded-lg border border-gray-700 mb-6 hover:border-blue-500 transition-colors">
                        <!-- Criterion Title and Description -->
                        <h4 class="text-md font-semibold text-white">{{ $criterion->title }} <span class="text-red-500">*</span></h4>
                        <p class="text-sm text-gray-400 mt-1">{{ $criterion->description }}</p>

                        <!-- Validation Toggle -->
                        <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:space-x-4">
                            <span class="text-sm font-medium text-white mb-2 sm:mb-0">Is this criterion met?</span>
                            <div class="flex items-center space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="criteria[{{ $criterion->id }}][valid]" value="1" 
                                        class="form-radio h-5 w-5 text-blue-600 focus:ring-blue-500 focus:ring-offset-gray-800"
                                        {{ old('criteria.'.$criterion->id.'.valid', isset($existingAnswers[$criterion->id]) && $existingAnswers[$criterion->id]->response ? '1' : '') == '1' ? 'checked' : '' }}>
                                    <span class="ml-2 text-white">Yes</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="criteria[{{ $criterion->id }}][valid]" value="0" 
                                        class="form-radio h-5 w-5 text-red-600 focus:ring-red-500 focus:ring-offset-gray-800"
                                        {{ old('criteria.'.$criterion->id.'.valid', isset($existingAnswers[$criterion->id]) && $existingAnswers[$criterion->id]->response === false ? '0' : '') == '0' ? 'checked' : '' }}>
                                    <span class="ml-2 text-white">No</span>
                                </label>
                            </div>
                        </div>
                        @error('criteria.'.$criterion->id.'.valid')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror

                        <!-- Comment Field -->
                        <div class="mt-4">
                            <label for="criteria-{{ $criterion->id }}-comment" class="block text-sm font-medium text-white mb-1">Comment</label>
                            <textarea id="criteria-{{ $criterion->id }}-comment" 
                                name="criteria[{{ $criterion->id }}][comment]" 
                                rows="3" 
                                class="block w-full rounded-lg bg-gray-700 border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-white placeholder-gray-400"
                                placeholder="Provide feedback on this criterion...">{{ old('criteria.'.$criterion->id.'.comment', isset($existingAnswers[$criterion->id]) ? $existingAnswers[$criterion->id]->comment : '') }}</textarea>
                            @error('criteria.'.$criterion->id.'.comment')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endforeach

                <!-- Overall Feedback -->
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-white mb-2">Overall Feedback</h3>
                    <p class="text-sm text-gray-400 mb-4">Provide some general feedback for the student:</p>
                    <textarea name="overall_feedback" rows="5" class="block w-full rounded-lg bg-gray-700 border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-white placeholder-gray-400" placeholder="Provide feedback on the submission overall...">{{ old('overall_feedback', $evaluation->feedback ? $evaluation->feedback->content : '') }}</textarea>
                    @error('overall_feedback')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit buttons -->
                <div class="mt-8 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
                    <button type="button" id="saveButton" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="far fa-save mr-2"></i>Save Draft
                    </button>
                    <button type="button" id="completeButton" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-check-circle mr-2"></i>Complete Evaluation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Debug Test Form -->
<div class="bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-700 mt-8">
    <div class="border-b border-gray-700 px-6 py-4">
        <h2 class="text-xl font-bold text-white">Debug Test Form</h2>
    </div>
    <div class="p-6">
        <button id="testAjaxButton" class="px-4 py-2 bg-purple-600 text-white rounded-lg">
            Test Direct AJAX Call
        </button>
        <div id="ajaxResult" class="mt-4 text-white"></div>
    </div>
</div>

<!-- Status message display -->
<div id="statusMessage" class="mt-4 hidden"></div>
@endsection

@section('styles')
<style>
    .criterion-card {
        transition: border-color 0.2s ease-in-out;
    }
    
    .criterion-card:hover {
        border-color: #4f46e5;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Utility function to show status messages
        function showStatus(message, isError = false) {
            const statusEl = document.getElementById('statusMessage') || document.createElement('div');
            
            if (!document.getElementById('statusMessage')) {
                statusEl.id = 'statusMessage';
                document.querySelector('.container').appendChild(statusEl);
            }
            
            statusEl.className = `mt-4 p-4 rounded-lg ${isError ? 'bg-red-500/75 text-white' : 'bg-green-500/75 text-white'}`;
            statusEl.textContent = message;
            statusEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
        
        // Main form submission handlers
        const form = document.getElementById('evaluationForm');
        const saveButton = document.getElementById('saveButton');
        const completeButton = document.getElementById('completeButton');
        
        if (form && saveButton && completeButton) {
            // Function to validate form
            function validateForm() {
                // Check if all criteria have been evaluated
                let valid = true;
                const criteriaRadios = document.querySelectorAll('input[type="radio"][name^="criteria"][name$="[valid]"]');
                const criteriaGroups = {};
                
                // Group radio buttons by their criteria ID
                criteriaRadios.forEach(radio => {
                    const name = radio.getAttribute('name');
                    if (!criteriaGroups[name]) {
                        criteriaGroups[name] = [];
                    }
                    criteriaGroups[name].push(radio);
                });
                
                // Check if at least one radio button is selected in each group
                const missingCriteria = [];
                for (const groupName in criteriaGroups) {
                    const group = criteriaGroups[groupName];
                    if (!group.some(radio => radio.checked)) {
                        valid = false;
                        const criterionId = groupName.match(/\[(\d+)\]/)[1];
                        missingCriteria.push(criterionId);
                    }
                }
                
                if (!valid) {
                    showStatus(`Please evaluate criteria: ${missingCriteria.join(', ')}`, true);
                    return false;
                }
                
                return true;
            }
            
            // Function to submit the form with AJAX
            function submitForm(isComplete) {
                // For complete submissions, validate first
                if (isComplete && !validateForm()) {
                    return;
                }
                
                // Show loading message
                showStatus('Submitting evaluation...');
                
                // Collect form data
                const formData = new FormData(form);
                
                // Add method and completion status
                formData.append('_method', 'PUT');
                formData.append('is_complete', isComplete ? '1' : '0');
                formData.append('rating', '5'); // Add a rating between 1 and 5
                
                // Get CSRF token
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Submit with fetch API
                fetch(form.getAttribute('action'), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.error || 'An error occurred');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Success:', data);
                    if (data.success) {
                        if (isComplete && data.redirect) {
                            // Redirect to the completion page
                            window.location.href = data.redirect;
                        } else {
                            // Show success message
                            showStatus(data.message || 'Your progress has been saved.');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showStatus(error.message || 'An error occurred while submitting the evaluation. Please try again.', true);
                });
            }
            
            // Prevent normal form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                return false;
            });
            
            // Setup button handlers
            saveButton.addEventListener('click', function() {
                submitForm(false);
            });
            
            completeButton.addEventListener('click', function() {
                submitForm(true);
            });
            
            // Handle the test AJAX button
            const testAjaxButton = document.getElementById('testAjaxButton');
            const resultDiv = document.getElementById('ajaxResult');
            
            if (testAjaxButton && resultDiv) {
                testAjaxButton.addEventListener('click', function() {
                    resultDiv.innerHTML = "Sending test request...";
                    
                    // Get CSRF token
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    // Create minimal test data - just enough to pass validation
                    const testData = new FormData();
                    testData.append('_method', 'PUT');
                    testData.append('is_complete', '0'); // Save as draft
                    testData.append('rating', '5'); // Add a rating between 1 and 5
                    
                    // Add all criteria
                    @foreach($evaluation->submission->brief->criteria as $criterion)
                        testData.append('criteria[{{ $criterion->id }}][valid]', '1');
                        testData.append('criteria[{{ $criterion->id }}][comment]', 'Auto-generated comment for testing');
                    @endforeach
                    
                    testData.append('overall_feedback', 'Test feedback');
                    
                    // Make direct fetch request
                    fetch('{{ route("student.evaluations.update", $evaluation->id) }}', {
                        method: 'POST', 
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: testData
                    })
                    .then(response => {
                        resultDiv.innerHTML += "<br>Response status: " + response.status;
                        return response.json();
                    })
                    .then(data => {
                        resultDiv.innerHTML += "<br>Response data: " + JSON.stringify(data);
                        console.log(data);
                        
                        if (data.success) {
                            resultDiv.innerHTML += "<br><span class='text-green-500'>✓ Success! The evaluation has been saved successfully.</span>";
                        }
                    })
                    .catch(error => {
                        resultDiv.innerHTML += "<br>Error: " + error.message;
                        console.error(error);
                    });
                });
            }
        }
    });
</script>
@endsection 