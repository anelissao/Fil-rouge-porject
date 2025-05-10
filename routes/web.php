<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\BriefController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EvaluationController;
use Illuminate\Support\Facades\Auth;

// Public routes
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('home');
})->name('home');

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ForgotPasswordController::class, 'reset'])->name('password.update');

// Google Authentication Routes
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard - redirect based on role
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user->role === 'student') {
            return redirect()->route('student.dashboard');
        } elseif ($user->role === 'teacher') {
            return redirect()->route('teacher.dashboard');
        } elseif ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } else {
            return view('dashboard');
        }
    })->name('dashboard');

    // User profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');

    // Brief routes (accessible by both teachers and students)
    Route::get('/briefs', [BriefController::class, 'index'])->name('briefs.index');
    Route::get('/briefs/{id}', [BriefController::class, 'show'])->name('briefs.show');
    
    // Evaluation routes (accessible by both teachers and students)
    Route::get('/evaluations', [App\Http\Controllers\Teacher\EvaluationController::class, 'index'])->name('evaluations.index');
    Route::get('/evaluations/assign', [App\Http\Controllers\Teacher\AssignmentController::class, 'manualForm'])->name('evaluations.assign');
    Route::post('/evaluations/assign', [App\Http\Controllers\Teacher\AssignmentController::class, 'storeManual'])->name('evaluations.store');
    Route::get('/evaluations/random', [App\Http\Controllers\Teacher\AssignmentController::class, 'randomForm'])->name('evaluations.random');
    Route::post('/evaluations/random', [App\Http\Controllers\Teacher\AssignmentController::class, 'storeRandom'])->name('evaluations.random.store');
    Route::get('/evaluations/{id}/student/{student_id}', [App\Http\Controllers\Teacher\EvaluationController::class, 'showStudentEvaluation'])->name('evaluations.student.show');
    Route::get('/evaluations/{evaluation}', [App\Http\Controllers\Teacher\EvaluationController::class, 'show'])->name('evaluations.show');
    Route::post('/evaluations/{id}/reassign', [App\Http\Controllers\Teacher\AssignmentController::class, 'reassign'])->name('evaluations.reassign');
    Route::delete('/evaluations/{id}', [App\Http\Controllers\Teacher\AssignmentController::class, 'cancel'])->name('evaluations.cancel');
    Route::post('/evaluations/{evaluation}/remind', [App\Http\Controllers\Teacher\EvaluationController::class, 'sendReminder'])->name('evaluations.remind');

    // Student-specific routes
    Route::middleware(['auth'])->prefix('student')->name('student.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');
        
        // Submissions
        Route::get('/submissions', [App\Http\Controllers\Student\SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('/submissions/create', [App\Http\Controllers\Student\SubmissionController::class, 'create'])->name('submissions.create');
        Route::post('/submissions', [App\Http\Controllers\Student\SubmissionController::class, 'store'])->name('submissions.store');
        Route::get('/submissions/{submission}', [App\Http\Controllers\Student\SubmissionController::class, 'show'])->name('submissions.show');
        Route::get('/submissions/{submission}/download', [App\Http\Controllers\Student\SubmissionController::class, 'download'])->name('submissions.download');

        // Evaluations
        Route::get('/evaluations', [App\Http\Controllers\Student\EvaluationController::class, 'index'])->name('evaluations.index');
        Route::get('/evaluations/{id}/edit', [App\Http\Controllers\Student\EvaluationController::class, 'edit'])->name('evaluations.edit');
        Route::put('/evaluations/{id}', [App\Http\Controllers\Student\EvaluationController::class, 'update'])->name('evaluations.update');
        Route::get('/evaluations/{id}', [App\Http\Controllers\Student\EvaluationController::class, 'show'])->name('evaluations.show');
    });

    // Teacher-specific routes
    Route::middleware(['auth'])->prefix('teacher')->name('teacher.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Teacher\DashboardController::class, 'index'])->name('dashboard');

        // Briefs management
        Route::resource('briefs', \App\Http\Controllers\Teacher\BriefController::class);
        
        // Additional brief routes
        Route::post('/briefs/{brief}/publish', [\App\Http\Controllers\Teacher\BriefController::class, 'publish'])->name('briefs.publish');
        Route::post('/briefs/{brief}/unpublish', [\App\Http\Controllers\Teacher\BriefController::class, 'unpublish'])->name('briefs.unpublish');
        Route::get('/briefs/{brief}/submissions', [\App\Http\Controllers\Teacher\BriefController::class, 'submissions'])->name('briefs.submissions');
        Route::get('/briefs/{brief}/results', [\App\Http\Controllers\Teacher\BriefController::class, 'results'])->name('briefs.results');
        
        // Submissions management
        Route::get('/submissions', [App\Http\Controllers\Teacher\SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('/submissions/{submission}', [App\Http\Controllers\Teacher\SubmissionController::class, 'show'])->name('submissions.show');
        
        // Evaluations
        Route::get('/evaluations', [App\Http\Controllers\Teacher\EvaluationController::class, 'index'])->name('evaluations.index');
        // Specific routes must come before wildcard routes
        Route::get('/evaluations/assign', [App\Http\Controllers\Teacher\AssignmentController::class, 'manualForm'])->name('evaluations.assign');
        Route::post('/evaluations/assign', [App\Http\Controllers\Teacher\AssignmentController::class, 'storeManual'])->name('evaluations.store');
        Route::get('/evaluations/random', [App\Http\Controllers\Teacher\AssignmentController::class, 'randomForm'])->name('evaluations.random');
        Route::post('/evaluations/random', [App\Http\Controllers\Teacher\AssignmentController::class, 'storeRandom'])->name('evaluations.random.store');
        // Wildcard routes come after specific routes
        Route::get('/evaluations/{evaluation}', [App\Http\Controllers\Teacher\EvaluationController::class, 'show'])->name('evaluations.show');
        Route::get('/evaluations/{id}/student/{student_id}', [App\Http\Controllers\Teacher\EvaluationController::class, 'showStudentEvaluation'])->name('evaluations.student.show');
        Route::post('/evaluations/{id}/reassign', [App\Http\Controllers\Teacher\AssignmentController::class, 'reassign'])->name('evaluations.reassign');
        Route::delete('/evaluations/{id}', [App\Http\Controllers\Teacher\AssignmentController::class, 'cancel'])->name('evaluations.cancel');
        Route::post('/evaluations/{evaluation}/remind', [App\Http\Controllers\Teacher\EvaluationController::class, 'sendReminder'])->name('evaluations.remind');
        
        // Results
        Route::get('/results', [App\Http\Controllers\Teacher\ResultsController::class, 'index'])->name('results.index');
        Route::get('/results/{id}', [App\Http\Controllers\Teacher\ResultsController::class, 'show'])->name('results.show');
        Route::get('/results/{id}/export', [App\Http\Controllers\Teacher\ResultsController::class, 'export'])->name('results.export');
    });

    // Admin-specific routes
    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function() {
            // Check if user is admin
            if (Auth::user()->role !== 'admin') {
                return redirect('/')->with('error', 'Only administrators can access this area.');
            }
            return view('admin.dashboard');
        })->name('dashboard');

        Route::get('/users', function() {
            // Check if user is admin
            if (Auth::user()->role !== 'admin') {
                return redirect('/')->with('error', 'Only administrators can access this area.');
            }
            return view('admin.users.index');
        })->name('users.index');

        Route::get('/briefs', function() {
            // Check if user is admin
            if (Auth::user()->role !== 'admin') {
                return redirect('/')->with('error', 'Only administrators can access this area.');
            }
            return view('admin.briefs.index');
        })->name('briefs.index');

        Route::get('/settings', function() {
            // Check if user is admin
            if (Auth::user()->role !== 'admin') {
                return redirect('/')->with('error', 'Only administrators can access this area.');
            }
            return view('admin.settings');
        })->name('settings');
    });
});
