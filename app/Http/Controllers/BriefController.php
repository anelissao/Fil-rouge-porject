<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brief;
use Illuminate\Support\Facades\Auth;

class BriefController extends Controller
{
    /**
     * Display a listing of the briefs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Brief::query();
        
        // Apply status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        } else {
            // By default, only show published briefs for students
            // For teachers, show all briefs that they created
            if (Auth::user()->isStudent()) {
                $query->where('status', 'published');
            } elseif (Auth::user()->isTeacher()) {
                $query->where(function($q) {
                    $q->where('teacher_id', Auth::id())
                      ->orWhere('status', 'published');
                });
            }
        }
        
        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        
        // Get the user's role
        $role = Auth::user()->role;
        
        // For teachers, include briefs they created
        if ($role === 'teacher') {
            $query->orderByRaw("CASE WHEN teacher_id = ? THEN 0 ELSE 1 END", [Auth::id()]);
        }
        
        // Order by created_at (newest first)
        $query->orderBy('created_at', 'desc');
        
        // Get briefs with teacher relationship
        $briefs = $query->with('teacher:id,username,first_name,last_name')
            ->withCount('submissions')
            ->paginate(10);
        
        return view('briefs.index', compact('briefs'));
    }
    
    /**
     * Display the specified brief.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $brief = Brief::with([
            'teacher:id,username,first_name,last_name',
            'criteria',
        ])->findOrFail($id);
        
        // Check if the brief is published or if the user is the teacher who created it
        if ($brief->status !== 'published' && 
            (!Auth::user()->isTeacher() || $brief->teacher_id !== Auth::id())) {
            return redirect()->route('briefs.index')
                ->with('error', 'You do not have permission to view this brief.');
        }
        
        // For students, check if they already submitted to this brief
        $hasSubmitted = false;
        if (Auth::user()->isStudent()) {
            $hasSubmitted = $brief->submissions()
                ->where('student_id', Auth::id())
                ->exists();
        }
        
        return view('briefs.show', compact('brief', 'hasSubmitted'));
    }
} 