<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::resource('courses', CourseController::class);
Route::get('user/courses', [CourseController::class, 'courses'])->name('user.courses');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
