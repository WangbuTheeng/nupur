<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

// Role-based dashboard routing
Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user->hasRole('admin')) {
        return app(DashboardController::class)->adminDashboard();
    } elseif ($user->hasRole('operator')) {
        return redirect()->route('operator.dashboard');
    } else {
        return app(DashboardController::class)->userDashboard();
    }
})->middleware(['auth', 'verified'])->name('dashboard');

// Operator Dashboard Route
Route::get('/operator/dashboard', function () {
    return view('operator.dashboard');
})->middleware(['auth', 'verified', 'operator'])->name('operator.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
