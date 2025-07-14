<?php

use App\Http\Controllers\Operator\BusController;
use App\Http\Controllers\Operator\ScheduleController;
use App\Http\Controllers\Operator\BookingController;
use App\Http\Controllers\Operator\CounterController;
use App\Http\Controllers\Operator\ReportController;
use App\Http\Controllers\Operator\DashboardController;
use App\Http\Controllers\Operator\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Operator Routes
|--------------------------------------------------------------------------
|
| Here are all the operator routes for the BookNGO system.
| All routes are protected by the 'operator' middleware.
|
*/

Route::middleware(['auth', 'operator'])->prefix('operator')->name('operator.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
    
    // Bus Management
    Route::resource('buses', BusController::class);
    Route::post('buses/{bus}/toggle-status', [BusController::class, 'toggleStatus'])->name('buses.toggle-status');
    Route::get('buses/{bus}/schedules', [BusController::class, 'schedules'])->name('buses.schedules');
    Route::get('buses/{bus}/maintenance', [BusController::class, 'maintenance'])->name('buses.maintenance');
    Route::post('buses/{bus}/maintenance', [BusController::class, 'storeMaintenance'])->name('buses.maintenance.store');
    Route::get('buses/{bus}/duplicate', [BusController::class, 'duplicate'])->name('buses.duplicate');
    Route::post('buses/{bus}/seat-layout', [BusController::class, 'updateSeatLayout'])->name('buses.seat-layout.update');
    Route::post('buses/preview-seat-layout', [BusController::class, 'previewSeatLayout'])->name('buses.seat-layout.preview');
    Route::get('buses/preview-seat-layout', [BusController::class, 'showSeatLayoutPreview'])->name('buses.seat-layout.preview.show');
    Route::get('buses/valid-seat-counts', [BusController::class, 'getValidSeatCounts'])->name('buses.valid-seat-counts');
    
    // Schedule Management
    Route::resource('schedules', ScheduleController::class);
    Route::post('schedules/{schedule}/toggle-status', [ScheduleController::class, 'toggleStatus'])->name('schedules.toggle-status');
    Route::patch('schedules/{schedule}/update-status', [ScheduleController::class, 'updateStatus'])->name('schedules.update-status');
    Route::get('schedules/calendar', [ScheduleController::class, 'calendar'])->name('schedules.calendar');
    Route::post('schedules/bulk-create', [ScheduleController::class, 'bulkCreate'])->name('schedules.bulk-create');
    Route::get('schedules/{schedule}/passengers', [ScheduleController::class, 'passengers'])->name('schedules.passengers');
    Route::get('schedules/{schedule}/seat-map', [ScheduleController::class, 'seatMap'])->name('schedules.seat-map');
    Route::post('schedules/{schedule}/update-seats', [ScheduleController::class, 'updateSeats'])->name('schedules.update-seats');
    
    // Booking Management
    Route::resource('bookings', BookingController::class)->only(['index', 'show', 'edit', 'update']);
    Route::post('bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('bookings/{booking}/refund', [BookingController::class, 'refund'])->name('bookings.refund');
    Route::get('bookings/today', [BookingController::class, 'today'])->name('bookings.today');
    Route::get('bookings/upcoming', [BookingController::class, 'upcoming'])->name('bookings.upcoming');
    Route::get('bookings/{booking}/ticket', [BookingController::class, 'ticket'])->name('bookings.ticket');
    Route::get('bookings/export', [BookingController::class, 'export'])->name('bookings.export');
    Route::get('bookings/{booking}/receipt', [BookingController::class, 'receipt'])->name('bookings.receipt');
    
    // Counter Booking (Manual Booking)
    Route::prefix('counter')->name('counter.')->group(function () {
        Route::get('/', [CounterController::class, 'index'])->name('index');
        Route::get('/search', [CounterController::class, 'search'])->name('search');
        Route::post('/search', [CounterController::class, 'searchResults'])->name('search.results');
        Route::get('/book/{schedule}', [CounterController::class, 'book'])->name('book');
        Route::post('/book/{schedule}', [CounterController::class, 'storeBooking'])->name('book.store');
        Route::get('/seat-selection/{schedule}', [CounterController::class, 'seatSelection'])->name('seat-selection');
        Route::post('/reserve-seats', [CounterController::class, 'reserveSeats'])->name('reserve-seats');
        Route::get('/payment/{booking}', [CounterController::class, 'payment'])->name('payment');
        Route::post('/payment/{booking}', [CounterController::class, 'processPayment'])->name('payment.process');
        Route::get('/receipt/{booking}', [CounterController::class, 'receipt'])->name('receipt');
    });
    
    // Reports & Analytics
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
        Route::get('/bookings', [ReportController::class, 'bookings'])->name('bookings');
        Route::get('/buses', [ReportController::class, 'buses'])->name('buses');
        Route::get('/routes', [ReportController::class, 'routes'])->name('routes');
        Route::get('/passengers', [ReportController::class, 'passengers'])->name('passengers');
        Route::get('/export/revenue', [ReportController::class, 'exportRevenue'])->name('export.revenue');
        Route::get('/export/bookings', [ReportController::class, 'exportBookings'])->name('export.bookings');
        Route::get('/export/passengers', [ReportController::class, 'exportPassengers'])->name('export.passengers');
    });
    
    // Route Management (Operator can suggest new routes)
    Route::prefix('routes')->name('routes.')->group(function () {
        Route::get('/', [BusController::class, 'routes'])->name('index');
        Route::get('/suggest', [BusController::class, 'suggestRoute'])->name('suggest');
        Route::post('/suggest', [BusController::class, 'storeSuggestion'])->name('suggest.store');
    });
    
    // Profile & Settings
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    Route::put('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread', [NotificationController::class, 'unread'])->name('unread');
        Route::get('/count', [NotificationController::class, 'getCount'])->name('count');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
    });
});
