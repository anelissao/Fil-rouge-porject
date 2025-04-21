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
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
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
    Route::get('/evaluations/{evaluation}', [EvaluationController::class, 'show'])->name('evaluations.show');

    // Student-specific routes
    Route::middleware(['auth'])->prefix('student')->name('student.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');
        
        // Submissions
        Route::get('/submissions', function() {
            // Check if user is student
            if (Auth::user()->role !== 'student') {
                return redirect('/')->with('error', 'Only students can access this area.');
            }
            return view('student.submissions.index');
        })->name('submissions.index');

        Route::get('/submissions/create', function() {
            // Check if user is student
            if (Auth::user()->role !== 'student') {
                return redirect('/')->with('error', 'Only students can access this area.');
            }
            return view('student.submissions.create');
        })->name('submissions.create');

        // Evaluations
        Route::get('/evaluations', function() {
            // Check if user is student
            if (Auth::user()->role !== 'student') {
                return redirect('/')->with('error', 'Only students can access this area.');
            }
            return view('student.evaluations.index');
        })->name('evaluations.index');
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
        Route::get('/evaluations/assign', [App\Http\Controllers\Teacher\EvaluationController::class, 'assignForm'])->name('evaluations.assign');
        Route::post('/evaluations', [App\Http\Controllers\Teacher\EvaluationController::class, 'store'])->name('evaluations.store');
        
        // Random evaluation assignments
        Route::get('/evaluations/random', [App\Http\Controllers\Teacher\EvaluationController::class, 'randomForm'])->name('evaluations.random');
        Route::post('/evaluations/random', [App\Http\Controllers\Teacher\EvaluationController::class, 'assignRandom'])->name('evaluations.random.store');
        
        // Results
        Route::get('/results', [App\Http\Controllers\Teacher\ResultsController::class, 'index'])->name('results.index');
        Route::get('/results/{id}', [App\Http\Controllers\Teacher\ResultsController::class, 'show'])->name('results.show');
        Route::get('/results/{id}/export', [App\Http\Controllers\Teacher\ResultsController::class, 'export'])->name('results.export');
        
        // Additional evaluation routes
        Route::post('/evaluations/{evaluation}/remind', [App\Http\Controllers\Teacher\EvaluationController::class, 'sendReminder'])->name('evaluations.remind');
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
