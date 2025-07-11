<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OperatorController;
use App\Http\Controllers\Admin\BusController;
use App\Http\Controllers\Admin\RouteController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SystemController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here are all the admin routes for the BookNGO system.
| All routes are protected by the 'admin' middleware.
|
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // User Management
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    
    // Operator Management
    Route::resource('operators', OperatorController::class);
    Route::post('operators/{operator}/toggle-status', [OperatorController::class, 'toggleStatus'])->name('operators.toggle-status');
    Route::post('operators/{operator}/reset-password', [OperatorController::class, 'resetPassword'])->name('operators.reset-password');
    Route::get('operators/{operator}/buses', [OperatorController::class, 'buses'])->name('operators.buses');
    Route::get('operators/{operator}/schedules', [OperatorController::class, 'schedules'])->name('operators.schedules');
    Route::get('operators/{operator}/bookings', [OperatorController::class, 'bookings'])->name('operators.bookings');
    
    // Bus Management (System-wide)
    Route::resource('buses', BusController::class);
    Route::post('buses/{bus}/toggle-status', [BusController::class, 'toggleStatus'])->name('buses.toggle-status');
    Route::get('buses/{bus}/schedules', [BusController::class, 'schedules'])->name('buses.schedules');
    
    // Route Management
    Route::resource('routes', RouteController::class);
    Route::post('routes/{route}/toggle-status', [RouteController::class, 'toggleStatus'])->name('routes.toggle-status');
    Route::get('routes/{route}/schedules', [RouteController::class, 'schedules'])->name('routes.schedules');
    
    // Schedule Management (System-wide oversight)
    Route::resource('schedules', ScheduleController::class);
    Route::post('schedules/{schedule}/toggle-status', [ScheduleController::class, 'toggleStatus'])->name('schedules.toggle-status');
    Route::get('schedules/calendar', [ScheduleController::class, 'calendar'])->name('schedules.calendar');
    Route::get('schedules/filter', [ScheduleController::class, 'filter'])->name('schedules.filter');
    
    // Booking Management (System-wide monitoring)
    Route::resource('bookings', BookingController::class)->only(['index', 'show', 'edit', 'update']);
    Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::get('bookings/today', [BookingController::class, 'today'])->name('bookings.today');
    Route::get('bookings/revenue', [BookingController::class, 'revenue'])->name('bookings.revenue');
    
    // Reports & Analytics
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('bookings', [ReportController::class, 'bookings'])->name('bookings');
        Route::get('revenue', [ReportController::class, 'revenue'])->name('revenue');
        Route::get('operators', [ReportController::class, 'operators'])->name('operators');
        Route::get('routes', [ReportController::class, 'routes'])->name('routes');
        Route::get('export/bookings', [ReportController::class, 'exportBookings'])->name('export.bookings');
        Route::get('export/revenue', [ReportController::class, 'exportRevenue'])->name('export.revenue');
        Route::get('export/operators', [ReportController::class, 'exportOperators'])->name('export.operators');
    });
    
    // System Settings & Configuration
    Route::prefix('system')->name('system.')->group(function () {
        Route::get('settings', [SystemController::class, 'settings'])->name('settings');
        Route::post('settings', [SystemController::class, 'updateSettings'])->name('settings.update');
        Route::get('bus-types', [SystemController::class, 'busTypes'])->name('bus-types');
        Route::post('bus-types', [SystemController::class, 'storeBusType'])->name('bus-types.store');
        Route::put('bus-types/{busType}', [SystemController::class, 'updateBusType'])->name('bus-types.update');
        Route::delete('bus-types/{busType}', [SystemController::class, 'destroyBusType'])->name('bus-types.destroy');
        Route::get('cities', [SystemController::class, 'cities'])->name('cities');
        Route::post('cities', [SystemController::class, 'storeCity'])->name('cities.store');
        Route::put('cities/{city}', [SystemController::class, 'updateCity'])->name('cities.update');
        Route::delete('cities/{city}', [SystemController::class, 'destroyCity'])->name('cities.destroy');
    });
    
    // Festival Mode Management
    Route::prefix('festival')->name('festival.')->group(function () {
        Route::get('/', [SystemController::class, 'festivalMode'])->name('index');
        Route::post('toggle', [SystemController::class, 'toggleFestivalMode'])->name('toggle');
        Route::get('schedules', [SystemController::class, 'festivalSchedules'])->name('schedules');
        Route::post('schedules', [SystemController::class, 'createFestivalSchedule'])->name('schedules.store');
    });
});
