@extends('layouts.app')

@section('title', 'Create Brief')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Create New Brief</h1>
            <p class="page-subtitle">Create a comprehensive assignment for your students</p>
        </div>
        <a href="{{ route('teacher.briefs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Briefs
        </a>
    </div>

    <div class="content-card">
        <form action="{{ route('teacher.briefs.store') }}" method="POST" enctype="multipart/form-data" id="briefForm">
            @csrf
            
            <div class="form-grid">
                <!-- Title and Basic Info -->
                <div class="section-card">
                    <h2 class="section-title">Basic Information</h2>
                    
                    <div class="form-group">
                        <label for="title">Brief Title</label>
                        <input type="text" name="title" id="title" class="form-control" 
                               placeholder="Enter a descriptive title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="deadline">Deadline</label>
                            <input type="datetime-local" name="deadline" id="deadline" class="form-control" 
                                   value="{{ old('deadline') }}" required>
                            @error('deadline')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                            </select>
                            @error('status')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Description with WYSIWYG Editor -->
                <div class="section-card">
                    <h2 class="section-title">Description</h2>
                    
                    <div class="form-group">
                        <textarea name="description" id="description" class="form-control wysiwyg-editor">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Evaluation Criteria Section -->
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">Evaluation Criteria</h2>
                        <button type="button" id="addCriterion" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Add Criterion
                        </button>
                    </div>
                    
                    <div id="criteriaContainer">
                        <!-- Existing criteria will be populated here when editing -->
                        <div class="empty-state" id="noCriteriaMessage">
                            <i class="fas fa-clipboard-list"></i>
                            <p>No evaluation criteria have been added yet.</p>
                            <p class="hint">Click "Add Criterion" to create your first evaluation criterion.</p>
                        </div>
                    </div>
                </div>

                <!-- Attachment and Skills -->
                <div class="section-card">
                    <h2 class="section-title">Additional Information</h2>
                    
                    <div class="form-group">
                        <label for="attachment">Attachment (optional)</label>
                        <div class="file-upload-wrapper">
                            <input type="file" name="attachment" id="attachment" class="file-upload">
                        </div>
                        <small class="form-text">Upload a PDF, DOCX, or other document with detailed instructions (max 10MB)</small>
                        @error('attachment')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="skills">Skills/Competencies (optional)</label>
                        <input type="text" name="skills" id="skills" class="form-control" 
                               placeholder="e.g. HTML, CSS, JavaScript, Design" value="{{ old('skills') }}">
                        <small class="form-text">Comma-separated list of skills this brief will help develop</small>
                        @error('skills')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Student Assignment -->
                <div class="section-card">
                    <h2 class="section-title">Assign to Students</h2>
                    
                    <div class="form-group">
                        <div class="student-selection">
                            <div class="selection-toggle">
                                <input type="radio" id="assignAll" name="assignment_type" value="all" checked>
                                <label for="assignAll">Assign to all students</label>
                            </div>
                            <div class="selection-toggle">
                                <input type="radio" id="assignSelect" name="assignment_type" value="select">
                                <label for="assignSelect">Select specific students</label>
                            </div>
                        </div>
                        
                        <div id="studentSelectContainer" class="student-select-container" style="display: none;">
                            <div class="search-container">
                                <input type="text" id="studentSearch" placeholder="Search students..." class="search-input">
                                <button type="button" class="btn btn-sm btn-outline select-all">Select All</button>
                                <button type="button" class="btn btn-sm btn-outline deselect-all">Deselect All</button>
                            </div>
                            
                            <div class="student-grid">
                                @foreach($classes as $student)
                                    <div class="student-item">
                                        <label class="student-checkbox">
                                            <input type="checkbox" name="students[]" value="{{ $student->id }}" 
                                                {{ in_array($student->id, old('students', [])) ? 'checked' : '' }}>
                                            <span class="checkbox-label">
                                                <span class="student-name">{{ $student->first_name }} {{ $student->last_name }}</span>
                                                <span class="student-email">{{ $student->email }}</span>
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="window.location='{{ route('teacher.briefs.index') }}'">
                    Cancel
                </button>
                <button type="submit" name="save_draft" value="1" class="btn btn-outline-primary">
                    Save as Draft
                </button>
                <button type="submit" class="btn btn-primary">
                    Create Brief
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Criterion Template for JavaScript -->
<template id="criterionTemplate">
    <div class="criterion-card" data-index="{index}">
        <div class="criterion-header">
            <h3 class="criterion-number">Criterion {number}</h3>
            <div class="criterion-actions">
                <button type="button" class="btn-icon move-up" title="Move Up">
                    <i class="fas fa-arrow-up"></i>
                </button>
                <button type="button" class="btn-icon move-down" title="Move Down">
                    <i class="fas fa-arrow-down"></i>
                </button>
                <button type="button" class="btn-icon remove-criterion" title="Remove">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        
        <div class="criterion-body">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="criteria[{index}][title]" class="form-control" placeholder="e.g. Code Quality" required>
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="criteria[{index}][description]" class="form-control" rows="2" placeholder="Describe what this criterion evaluates"></textarea>
            </div>
            
            <div class="form-group">
                <div class="task-header">
                    <label>Tasks/Requirements</label>
                    <button type="button" class="btn-sm btn-link add-task">
                        <i class="fas fa-plus"></i> Add Task
                    </button>
                </div>
                
                <div class="tasks-container">
                    <!-- Tasks will be added here -->
                    <div class="empty-state task-empty-state">
                        <p>No tasks added yet</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Task Template for JavaScript -->
<template id="taskTemplate">
    <div class="task-item">
        <div class="task-input-group">
            <input type="text" name="criteria[{criterionIndex}][tasks][{taskIndex}]" class="form-control" placeholder="Describe a specific task or requirement">
            <button type="button" class="btn-icon remove-task">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</template>

@endsection

@section('styles')
<style>
    /* Page Layout */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .page-title {
        font-size: 1.75rem;
        margin-bottom: 0.25rem;
        color: var(--secondary-color);
    }

    .page-subtitle {
        color: var(--accent-color);
    }

    .content-card {
        background-color: var(--highlight-color);
        border-radius: 0.5rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Form Layout */
    .form-grid {
        display: grid;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .section-card {
        background-color: rgba(255, 255, 255, 0.05);
        border-radius: 0.5rem;
        padding: 1.5rem;
        border: 1px solid rgba(229, 231, 235, 0.1);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .section-title {
        font-size: 1.25rem;
        margin-bottom: 1rem;
        color: var(--secondary-color);
    }

    /* Form Controls */
    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-row {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .form-control {
        display: block;
        width: 100%;
        padding: 0.5rem 0.75rem;
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(229, 231, 235, 0.2);
        border-radius: 0.375rem;
        color: var(--secondary-color);
    }

    .form-control:focus {
        border-color: var(--primary-color);
        outline: none;
        box-shadow: 0 0 0 2px rgba(30, 144, 255, 0.2);
    }

    label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--secondary-color);
    }

    .form-text {
        display: block;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: var(--accent-color);
    }

    .form-error {
        color: #f56565;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    /* File Upload Styling */
    .file-upload-wrapper {
        position: relative;
        margin-bottom: 0.5rem;
    }

    .file-upload {
        display: block;
        width: 100%;
        padding: 0.5rem;
        border: 1px dashed rgba(229, 231, 235, 0.3);
        border-radius: 0.375rem;
        background-color: rgba(255, 255, 255, 0.02);
        cursor: pointer;
    }

    /* Buttons and Actions */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(229, 231, 235, 0.1);
    }

    /* Criterion Styling */
    .criterion-card {
        background-color: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(229, 231, 235, 0.15);
        border-radius: 0.375rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .criterion-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid rgba(229, 231, 235, 0.1);
    }

    .criterion-number {
        font-size: 1.1rem;
        color: var(--secondary-color);
        margin: 0;
    }

    .criterion-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-icon {
        background: none;
        border: none;
        padding: 0.25rem;
        font-size: 0.875rem;
        color: var(--accent-color);
        cursor: pointer;
        border-radius: 0.25rem;
    }

    .btn-icon:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: var(--primary-color);
    }

    /* Task Styling */
    .task-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .tasks-container {
        padding-left: 0.25rem;
    }

    .task-item {
        margin-bottom: 0.5rem;
    }

    .task-input-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Student Selection */
    .student-selection {
        display: flex;
        gap: 2rem;
        margin-bottom: 1rem;
    }

    .selection-toggle {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .student-select-container {
        border: 1px solid rgba(229, 231, 235, 0.2);
        border-radius: 0.375rem;
        padding: 1rem;
        max-height: 400px;
        overflow-y: auto;
    }

    .search-container {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .search-input {
        flex: 1;
        padding: 0.5rem 0.75rem;
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(229, 231, 235, 0.2);
        border-radius: 0.375rem;
        color: var(--secondary-color);
    }

    .student-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 0.5rem;
    }

    .student-item {
        background-color: rgba(255, 255, 255, 0.03);
        border-radius: 0.25rem;
        padding: 0.5rem;
    }

    .student-checkbox {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
    }

    .checkbox-label {
        display: flex;
        flex-direction: column;
    }

    .student-name {
        font-size: 0.9rem;
        color: var(--secondary-color);
    }

    .student-email {
        font-size: 0.8rem;
        color: var(--accent-color);
    }

    /* Empty States */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        text-align: center;
        color: var(--accent-color);
    }

    .empty-state i {
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    .empty-state .hint {
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    .task-empty-state {
        padding: 1rem;
        font-size: 0.875rem;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize TinyMCE
        tinymce.init({
            selector: '.wysiwyg-editor',
            height: 300,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview', 'anchor',
                'searchreplace', 'visualblocks', 'code', 'fullscreen', 'insertdatetime',
                'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px }'
        });

        // Handle student assignment type toggle
        const assignAll = document.getElementById('assignAll');
        const assignSelect = document.getElementById('assignSelect');
        const studentSelectContainer = document.getElementById('studentSelectContainer');

        assignAll.addEventListener('change', function() {
            if (this.checked) {
                studentSelectContainer.style.display = 'none';
            }
        });

        assignSelect.addEventListener('change', function() {
            if (this.checked) {
                studentSelectContainer.style.display = 'block';
            }
        });

        // Student search functionality
        const studentSearch = document.getElementById('studentSearch');
        const studentItems = document.querySelectorAll('.student-item');

        studentSearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            
            studentItems.forEach(item => {
                const name = item.querySelector('.student-name').textContent.toLowerCase();
                const email = item.querySelector('.student-email').textContent.toLowerCase();
                
                if (name.includes(searchTerm) || email.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Select/Deselect all students
        document.querySelector('.select-all').addEventListener('click', function() {
            document.querySelectorAll('.student-item input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = true;
            });
        });

        document.querySelector('.deselect-all').addEventListener('click', function() {
            document.querySelectorAll('.student-item input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });
        });

        // Criteria Management
        const criteriaContainer = document.getElementById('criteriaContainer');
        const addCriterionButton = document.getElementById('addCriterion');
        const noCriteriaMessage = document.getElementById('noCriteriaMessage');
        const criterionTemplate = document.getElementById('criterionTemplate').innerHTML;
        const taskTemplate = document.getElementById('taskTemplate').innerHTML;
        let criterionCount = 0;

        addCriterionButton.addEventListener('click', function() {
            addCriterion();
        });

        function addCriterion() {
            if (noCriteriaMessage) {
                noCriteriaMessage.style.display = 'none';
            }

            const newIndex = criterionCount;
            const newHtml = criterionTemplate
                .replace(/{index}/g, newIndex)
                .replace(/{number}/g, criterionCount + 1);
            
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = newHtml;
            const newCriterion = tempDiv.firstElementChild;
            
            criteriaContainer.appendChild(newCriterion);
            criterionCount++;
            
            // Add event listeners for the new criterion
            setupCriterionEvents(newCriterion, newIndex);
        }

        function setupCriterionEvents(criterion, index) {
            // Remove criterion
            criterion.querySelector('.remove-criterion').addEventListener('click', function() {
                criterion.remove();
                if (criteriaContainer.querySelectorAll('.criterion-card').length === 0) {
                    noCriteriaMessage.style.display = 'flex';
                }
                reindexCriteria();
            });

            // Move criterion up
            criterion.querySelector('.move-up').addEventListener('click', function() {
                const prev = criterion.previousElementSibling;
                if (prev && prev.classList.contains('criterion-card')) {
                    criteriaContainer.insertBefore(criterion, prev);
                    reindexCriteria();
                }
            });

            // Move criterion down
            criterion.querySelector('.move-down').addEventListener('click', function() {
                const next = criterion.nextElementSibling;
                if (next) {
                    criteriaContainer.insertBefore(next, criterion);
                    reindexCriteria();
                }
            });

            // Add task
            let taskCount = 0;
            const tasksContainer = criterion.querySelector('.tasks-container');
            const taskEmptyState = tasksContainer.querySelector('.task-empty-state');
            
            criterion.querySelector('.add-task').addEventListener('click', function() {
                if (taskEmptyState) {
                    taskEmptyState.style.display = 'none';
                }
                
                const newTaskHtml = taskTemplate
                    .replace(/{criterionIndex}/g, criterion.dataset.index)
                    .replace(/{taskIndex}/g, taskCount);
                
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = newTaskHtml;
                const newTask = tempDiv.firstElementChild;
                
                tasksContainer.appendChild(newTask);
                taskCount++;
                
                // Remove task event
                newTask.querySelector('.remove-task').addEventListener('click', function() {
                    newTask.remove();
                    if (tasksContainer.querySelectorAll('.task-item').length === 0) {
                        taskEmptyState.style.display = 'block';
                    }
                });
            });
        }

        function reindexCriteria() {
            const criteria = criteriaContainer.querySelectorAll('.criterion-card');
            criteria.forEach((criterion, idx) => {
                criterion.dataset.index = idx;
                criterion.querySelector('.criterion-number').textContent = `Criterion ${idx + 1}`;
                
                // Update input names
                criterion.querySelectorAll('input, textarea').forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        input.setAttribute('name', name.replace(/criteria\[\d+\]/, `criteria[${idx}]`));
                    }
                });
                
                // Update task indices
                const tasks = criterion.querySelectorAll('.task-item input');
                tasks.forEach((task, taskIdx) => {
                    const name = task.getAttribute('name');
                    if (name) {
                        task.setAttribute('name', name.replace(/criteria\[\d+\]\[tasks\]\[\d+\]/, `criteria[${idx}][tasks][${taskIdx}]`));
                    }
                });
            });
        }

        // Add one criterion by default
        addCriterion();
    });
</script>
@endsection 