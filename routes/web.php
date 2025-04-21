<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleController;

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
    Route::get('/profile', function() {
        return view('profile.show');
    })->name('profile.show');

    // Brief routes (accessible by both teachers and students)
    Route::get('/briefs', function() {
        return view('briefs.index');
    })->name('briefs.index');

    // Student-specific routes
    Route::middleware(['role:student'])->group(function () {
        // Submissions
        Route::get('/submissions', function() {
            return view('submissions.index');
        })->name('submissions.index');

        Route::get('/submissions/create', function() {
            return view('submissions.create');
        })->name('submissions.create');

        // Evaluations
        Route::get('/evaluations', function() {
            return view('evaluations.index');
        })->name('evaluations.index');
    });

    // Teacher-specific routes
    Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
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
        
        // Results
        Route::get('/results', [App\Http\Controllers\Teacher\ResultsController::class, 'index'])->name('results.index');
        Route::get('/results/{id}', [App\Http\Controllers\Teacher\ResultsController::class, 'show'])->name('results.show');
        Route::get('/results/{id}/export', [App\Http\Controllers\Teacher\ResultsController::class, 'export'])->name('results.export');
        
        // Additional evaluation routes
        Route::post('/evaluations/{evaluation}/remind', [App\Http\Controllers\Teacher\EvaluationController::class, 'sendReminder'])->name('evaluations.remind');
        Route::post('/evaluations/random', [App\Http\Controllers\Teacher\EvaluationController::class, 'assignRandom'])->name('evaluations.random');
    });

    // Admin-specific routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function() {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::get('/users', function() {
            return view('admin.users.index');
        })->name('users.index');

        Route::get('/briefs', function() {
            return view('admin.briefs.index');
        })->name('briefs.index');

        Route::get('/settings', function() {
            return view('admin.settings');
        })->name('settings');
    });
});
