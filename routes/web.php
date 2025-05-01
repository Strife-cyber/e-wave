<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\LessonController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public route
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Authenticated and verified user routes
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('/contributions', ContributionController::class);
    Route::resource('/groups', GroupController::class);
    Route::resource('/attachments', AttachmentController::class);
    Route::get('/attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');
    // Courses Routes
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('enroll/{course}', [CourseController::class, 'enroll'])->name('enroll');
        Route::resource('/', CourseController::class)->parameters(['' => 'course'])->except(['create', 'edit'])->names([
            'index' => 'index',
            'store' => 'store',
            'show' => 'show',
            'update' => 'update',
            'destroy' => 'destroy',
        ]);
    });

    // Lessons Routes
    Route::resource('lessons', LessonController::class)->except(['create', 'edit']);
    Route::resource('echo', \App\Http\Controllers\EchoCardController::class);

    // User-specific routes
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('courses', [CourseController::class, 'courses'])->name('courses');
        Route::get('courses/{course}', [CourseController::class, 'showCourse'])->name('courses.show');
        Route::get('courses/{course}/lessons', [LessonController::class, 'lessons'])->name('courses.lessons');
    });

});

// Additional route files
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
