<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Brief;
use App\Models\BriefCriteria;
use App\Models\BriefTask;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BriefController extends Controller
{
    /**
     * Display a listing of the briefs.
     */
    public function index(Request $request)
    {
        $query = Brief::where('teacher_id', Auth::id());

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Apply sorting
        switch ($request->sort ?? 'newest') {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'deadline_asc':
                $query->orderBy('deadline', 'asc');
                break;
            case 'deadline_desc':
                $query->orderBy('deadline', 'desc');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Get briefs with count of submissions
        $briefs = $query->withCount(['submissions'])
                        ->paginate(12)
                        ->appends($request->query());

        // Retrieve all classes that this teacher teaches
        $classes = User::where('role', 'student')->get();

        return view('teacher.briefs.index', compact('briefs', 'classes'));
    }

    /**
     * Show the form for creating a new brief.
     */
    public function create()
    {
        // Get classes taught by this teacher
        $classes = User::where('role', 'student')->get();
        
        return view('teacher.briefs.create', compact('classes'));
    }

    /**
     * Store a newly created brief in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date',
            'status' => 'required|in:draft,active',
            'classes' => 'nullable|array',
            'classes.*' => 'exists:users,id',
            'skills' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create the brief
        $brief = new Brief([
            'title' => $request->title,
            'description' => $request->description,
            'teacher_id' => Auth::id(),
            'deadline' => $request->deadline,
            'status' => $request->input('save_draft') ? 'draft' : $request->status,
        ]);

        $brief->save();

        // Handle file attachment if provided
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . Str::slug($brief->title) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('brief_attachments', $filename, 'public');
            
            // Store the file path in a brief_attachments table or add to the brief model
            // For now, we'll assume we'll add a file_path field to the briefs table later
        }

        // Add skills/competencies to the brief (assuming we'll have a brief_skills table)
        if ($request->has('skills') && !empty($request->skills)) {
            $skills = explode(',', $request->skills);
            // Here we would store skills in a related table
        }

        // Assign to classes (student users)
        if ($request->has('classes') && is_array($request->classes)) {
            // Here we would create student assignments in a related table
        }

        // Redirect based on action
        if ($request->input('save_draft')) {
            return redirect()->route('teacher.briefs.edit', $brief->id)
                ->with('success', 'Brief has been saved as a draft.');
        }

        return redirect()->route('teacher.briefs.index')
            ->with('success', 'Brief has been created successfully.');
    }

    /**
     * Display the specified brief.
     */
    public function show(Brief $brief)
    {
        // Check if the brief belongs to the authenticated teacher
        if ($brief->teacher_id !== Auth::id()) {
            return abort(403, 'Unauthorized action.');
        }

        // Get submissions count and other stats
        $submissionsCount = $brief->submissions()->count();
        $criteria = $brief->criteria()->with('tasks')->orderBy('order')->get();

        return view('teacher.briefs.show', compact('brief', 'submissionsCount', 'criteria'));
    }

    /**
     * Show the form for editing the specified brief.
     */
    public function edit(Brief $brief)
    {
        // Check if the brief belongs to the authenticated teacher
        if ($brief->teacher_id !== Auth::id()) {
            return abort(403, 'Unauthorized action.');
        }

        // Get classes taught by this teacher
        $classes = User::where('role', 'student')->get();
        
        // Get currently assigned classes
        $assignedClasses = []; // This would come from your assignment table

        // Get criteria and tasks
        $criteria = $brief->criteria()->with('tasks')->orderBy('order')->get();

        return view('teacher.briefs.edit', compact('brief', 'classes', 'assignedClasses', 'criteria'));
    }

    /**
     * Update the specified brief in storage.
     */
    public function update(Request $request, Brief $brief)
    {
        // Check if the brief belongs to the authenticated teacher
        if ($brief->teacher_id !== Auth::id()) {
            return abort(403, 'Unauthorized action.');
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date',
            'status' => 'required|in:draft,active,expired',
            'classes' => 'nullable|array',
            'classes.*' => 'exists:users,id',
            'skills' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update the brief
        $brief->title = $request->title;
        $brief->description = $request->description;
        $brief->deadline = $request->deadline;
        $brief->status = $request->input('save_draft') ? 'draft' : $request->status;
        
        $brief->save();

        // Handle file attachment if provided
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . Str::slug($brief->title) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('brief_attachments', $filename, 'public');
            
            // Store the file path or update if exists
            // For now, we'll assume we'll add a file_path field to the briefs table later
        }

        // Update skills/competencies
        if ($request->has('skills')) {
            $skills = !empty($request->skills) ? explode(',', $request->skills) : [];
            // Here we would update skills in a related table
        }

        // Update class assignments
        if ($request->has('classes')) {
            $classes = $request->classes ?? [];
            // Here we would update student assignments in a related table
        }

        // Redirect based on action
        if ($request->input('save_draft')) {
            return redirect()->route('teacher.briefs.edit', $brief->id)
                ->with('success', 'Brief has been saved as a draft.');
        }

        return redirect()->route('teacher.briefs.show', $brief->id)
            ->with('success', 'Brief has been updated successfully.');
    }

    /**
     * Remove the specified brief from storage.
     */
    public function destroy(Brief $brief)
    {
        // Check if the brief belongs to the authenticated teacher
        if ($brief->teacher_id !== Auth::id()) {
            return abort(403, 'Unauthorized action.');
        }

        // Check if there are any submissions
        if ($brief->submissions()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete a brief that has submissions.');
        }

        // Delete the brief and related data
        $brief->criteria()->delete(); // This will cascade delete tasks due to foreign key
        $brief->delete();

        return redirect()->route('teacher.briefs.index')
            ->with('success', 'Brief has been deleted successfully.');
    }

    /**
     * Publish a draft brief.
     */
    public function publish(Brief $brief)
    {
        // Check if the brief belongs to the authenticated teacher
        if ($brief->teacher_id !== Auth::id()) {
            return abort(403, 'Unauthorized action.');
        }

        // Check if the brief is in draft status
        if (!$brief->isDraft()) {
            return redirect()->back()
                ->with('error', 'Only draft briefs can be published.');
        }

        $brief->status = 'active';
        $brief->save();

        return redirect()->back()
            ->with('success', 'Brief has been published successfully.');
    }

    /**
     * Unpublish an active brief.
     */
    public function unpublish(Brief $brief)
    {
        // Check if the brief belongs to the authenticated teacher
        if ($brief->teacher_id !== Auth::id()) {
            return abort(403, 'Unauthorized action.');
        }

        // Check if the brief is in active status
        if (!$brief->isActive()) {
            return redirect()->back()
                ->with('error', 'Only active briefs can be unpublished.');
        }

        $brief->status = 'draft';
        $brief->save();

        return redirect()->back()
            ->with('success', 'Brief has been unpublished and is now a draft.');
    }

    /**
     * Show submissions for a specific brief.
     */
    public function submissions(Brief $brief)
    {
        // Check if the brief belongs to the authenticated teacher
        if ($brief->teacher_id !== Auth::id()) {
            return abort(403, 'Unauthorized action.');
        }

        $submissions = $brief->submissions()
            ->with('student')
            ->latest()
            ->paginate(20);

        return view('teacher.briefs.submissions', compact('brief', 'submissions'));
    }

    /**
     * Display the results/analytics for a specific brief.
     *
     * @param Brief $brief
     * @return \Illuminate\View\View
     */
    public function results(Brief $brief)
    {
        // Get total submissions
        $totalSubmissions = $brief->submissions()->count();
        
        // Get completed evaluations
        $completedEvaluations = $brief->submissions()
            ->whereHas('evaluations', function ($query) {
                $query->where('status', 'completed');
            })
            ->count();
        
        // Example skills performance data - replace with actual logic as needed
        $skillsPerformance = [];
        $skills = $brief->skills;
        
        foreach ($skills as $skill) {
            $skillsPerformance[] = [
                'name' => $skill->name,
                'score' => rand(40, 95), // Replace with actual calculation
            ];
        }
        
        // Example criteria performance data - replace with actual logic as needed
        $criteriaPerformance = [];
        $criteria = $brief->evaluationCriteria;
        
        foreach ($criteria as $criterion) {
            $criteriaPerformance[] = [
                'title' => $criterion->title,
                'pass_rate' => rand(30, 90), // Replace with actual calculation
            ];
        }
        
        // Example student performance data - replace with actual logic as needed
        $studentPerformance = [];
        $assigned_students = $brief->students;
        
        foreach ($assigned_students as $student) {
            $hasSubmission = $student->submissions()->where('brief_id', $brief->id)->exists();
            $submission = $student->submissions()->where('brief_id', $brief->id)->first();
            
            $studentPerformance[] = [
                'name' => $student->name,
                'email' => $student->email,
                'has_submission' => $hasSubmission,
                'submission_date' => $hasSubmission ? $submission->created_at->format('M d, Y H:i') : null,
                'is_late' => $hasSubmission ? $submission->created_at > $brief->deadline : false,
                'evaluations_count' => $hasSubmission ? $submission->evaluations()->count() : 0,
                'score' => $hasSubmission && $submission->evaluations()->exists() ? 
                            $submission->evaluations()->avg('score') : null,
            ];
        }
        
        return view('teacher.briefs.results', compact(
            'brief',
            'totalSubmissions',
            'completedEvaluations',
            'skillsPerformance',
            'criteriaPerformance',
            'studentPerformance'
        ));
    }
} 