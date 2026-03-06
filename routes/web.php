<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
<<<<<<< HEAD
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\ProjectController;
=======
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\User\UserController;
>>>>>>> 89caed72e1a46b970403232f253207870b3ea870

Route::get('/', function () {
    return view('welcome');
});

<<<<<<< HEAD
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('user.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
=======

// Profile Routes (Breeze default - keep only this one)
Route::middleware('auth')->group(function () {
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
>>>>>>> 89caed72e1a46b970403232f253207870b3ea870
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
<<<<<<< HEAD
    Route::resource('projects', ProjectController::class);

=======
    Route::resource('students', AdminStudentController::class)->names('students');
});

// Student Routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
>>>>>>> 89caed72e1a46b970403232f253207870b3ea870
});

// User Routes
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
});

<<<<<<< HEAD
require __DIR__.'/auth.php';
=======
// Protected routes (add auth middleware)
Route::middleware(['auth'])->group(function () {
    // Additional routes can be added here as needed
});

require __DIR__.'/auth.php';
>>>>>>> 89caed72e1a46b970403232f253207870b3ea870
