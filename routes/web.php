<?php

// ─────────────────────────────────────────────────────────────
// ADD THIS to routes/web.php inside the existing admin group:
//
//   Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
//       ...existing routes...
//
//       // ADD THESE LINES:
//       Route::get('/users',          [AdminUserController::class, 'index'])->name('users.index');
//       Route::post('/users',         [AdminUserController::class, 'store'])->name('users.store');
//       Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
//       Route::delete('/users/{user}',[AdminUserController::class, 'destroy'])->name('users.destroy');
//   });
// ─────────────────────────────────────────────────────────────

// Also add this use statement at the top of web.php:
// use App\Http\Controllers\Admin\AdminUserController;

// ─────────────────────────────────────────────────────────────
// FULL UPDATED web.php for reference:
// ─────────────────────────────────────────────────────────────

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\AdminUserController;   
use App\Http\Controllers\User\UserProjectController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('user.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::patch('/projects/{project}/entry',      [ProjectController::class, 'updateEntry'])->name('projects.updateEntry');
    Route::delete('/projects/{project}/entry',     [ProjectController::class, 'destroyEntry'])->name('projects.destroyEntry');
    Route::patch('/projects/{project}/billing',    [ProjectController::class, 'updateBilling'])->name('projects.updateBilling');
    Route::patch('/projects/{project}/reactivate', [ProjectController::class, 'reactivate'])->name('projects.reactivate');

    Route::resource('projects', ProjectController::class);

    Route::get('/projects/{project}/export-pdf', [ProjectController::class, 'exportPdf'])->name('projects.export-pdf');
    Route::get('/reports',          [ProjectController::class, 'reports'])->name('reports.index');
    Route::get('/reports/generate', [ProjectController::class, 'generateReport'])->name('reports.generate');

    // ── User management (super admin only — enforced in controller) ──
    Route::get('/users',           [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users',          [AdminUserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}',  [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});

// User Routes
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', function () {
        return view('user.dashboard');
    })->name('dashboard');

    Route::get('/projects', [UserProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [UserProjectController::class, 'show'])->name('projects.show');
});

require __DIR__.'/auth.php';