<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Home page
Route::get('/', function () {
    return view('welcome');
});

// Public Ticket Verification (no auth required)
Route::get('/verify', function () {
    return view('tickets.verify');
})->name('tickets.verify.page');
Route::post('/verify', [\App\Http\Controllers\TicketController::class, 'verify'])->name('tickets.verify');
Route::post('/verify/manual', [\App\Http\Controllers\TicketController::class, 'verifyManual'])->name('tickets.verify.manual');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth', 'user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'userDashboard'])->name('dashboard');

    // Search Routes
    Route::get('/search', [\App\Http\Controllers\SearchController::class, 'index'])->name('search');
    Route::post('/search', [\App\Http\Controllers\SearchController::class, 'search'])->name('search.results');
    Route::get('/schedule/{schedule}', [\App\Http\Controllers\SearchController::class, 'showSchedule'])->name('schedule.show');

    // Booking Routes
    Route::get('/bookings', [\App\Http\Controllers\BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create/{schedule}', [\App\Http\Controllers\BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings/create/{schedule}', [\App\Http\Controllers\BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [\App\Http\Controllers\BookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{booking}/payment', [\App\Http\Controllers\BookingController::class, 'payment'])->name('bookings.payment');
    Route::post('/bookings/{booking}/payment/demo', [\App\Http\Controllers\BookingController::class, 'demoPayment'])->name('bookings.payment.demo');
    Route::post('/bookings/{booking}/cancel', [\App\Http\Controllers\BookingController::class, 'cancel'])->name('bookings.cancel');

    // Ticket Routes
    Route::get('/tickets/{booking}', [\App\Http\Controllers\TicketController::class, 'view'])->name('tickets.view');
    Route::get('/tickets/{booking}/download', [\App\Http\Controllers\TicketController::class, 'download'])->name('tickets.download');

    Route::get('/profile', function () {
        return view('profile.index');
    })->name('profile');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

    // Operator Management
    Route::resource('operators', \App\Http\Controllers\Admin\OperatorController::class);
    Route::patch('operators/{operator}/toggle-status', [\App\Http\Controllers\Admin\OperatorController::class, 'toggleStatus'])->name('operators.toggle-status');

    // User Management
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::patch('users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Bus Management
    Route::resource('buses', \App\Http\Controllers\Admin\BusController::class);

    // Route Management
    Route::resource('routes', \App\Http\Controllers\Admin\RouteController::class);

    // Schedule Management
    Route::resource('schedules', \App\Http\Controllers\Admin\ScheduleController::class);

    // Booking Management
    Route::get('bookings', function () {
        return view('admin.bookings.index');
    })->name('bookings.index');
});
