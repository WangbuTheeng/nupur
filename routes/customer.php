<?php

use App\Http\Controllers\Customer\SearchController;
use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Customer\TicketController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
|
| Here are all the customer routes for the BookNGO system.
| These routes handle bus search, booking, and payment processes.
|
*/

// Public routes (accessible without authentication)
Route::prefix('search')->name('search.')->group(function () {
    Route::get('/', [SearchController::class, 'index'])->name('index');
    Route::post('/', [SearchController::class, 'search'])->name('results');
    Route::get('/results', [SearchController::class, 'results'])->name('show');
    Route::get('/schedule/{schedule}', [SearchController::class, 'scheduleDetails'])->name('schedule');
    Route::get('/route/{route}', [SearchController::class, 'routeDetails'])->name('route');
});

// Authenticated customer routes
Route::middleware(['auth', 'user'])->group(function () {

    // Customer Dashboard
    Route::get('/customer/dashboard', [DashboardController::class, 'index'])->name('customer.dashboard');
    Route::get('/customer/dashboard/stats', [DashboardController::class, 'stats'])->name('customer.dashboard.stats');

    // Customer Bookings
    Route::prefix('customer/bookings')->name('customer.bookings.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/upcoming', [BookingController::class, 'upcoming'])->name('upcoming');
        Route::get('/history', [BookingController::class, 'history'])->name('history');
        Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
    });

    // Customer Tickets
    Route::prefix('customer/tickets')->name('customer.tickets.')->group(function () {
        Route::get('/{booking}', [TicketController::class, 'show'])->name('show');
        Route::get('/{booking}/download', [TicketController::class, 'download'])->name('download');
    });

    // Customer Payments
    Route::prefix('customer/payments')->name('customer.payments.')->group(function () {
        Route::get('/history', [PaymentController::class, 'history'])->name('history');
    });
    
    // Booking Process
    Route::prefix('booking')->name('booking.')->group(function () {
        Route::get('/seat-selection/{schedule}', [BookingController::class, 'seatSelection'])->name('seat-selection');
        Route::post('/reserve-seats', [BookingController::class, 'reserveSeats'])->name('reserve-seats');
        Route::get('/passenger-details/{schedule}', [BookingController::class, 'passengerDetails'])->name('passenger-details');
        Route::post('/store-details', [BookingController::class, 'storeDetails'])->name('store-details');
        Route::get('/review/{booking}', [BookingController::class, 'review'])->name('review');
        Route::post('/confirm/{booking}', [BookingController::class, 'confirm'])->name('confirm');
        Route::get('/success/{booking}', [BookingController::class, 'success'])->name('success');
        Route::get('/cancel/{booking}', [BookingController::class, 'cancel'])->name('cancel');
        Route::post('/cancel/{booking}', [BookingController::class, 'processCancel'])->name('process-cancel');
    });
    
    // Payment Processing
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/{booking}', [PaymentController::class, 'index'])->name('index');
        Route::post('/process/{booking}', [PaymentController::class, 'process'])->name('process');
        Route::get('/success/{booking}', [PaymentController::class, 'success'])->name('success');
        Route::get('/failed/{booking}', [PaymentController::class, 'failed'])->name('failed');
        Route::post('/verify/{booking}', [PaymentController::class, 'verify'])->name('verify');
        
        // Payment Gateway Callbacks
        Route::post('/esewa/callback', [PaymentController::class, 'esewaCallback'])->name('esewa.callback');
        Route::post('/khalti/callback', [PaymentController::class, 'khaltiCallback'])->name('khalti.callback');
        Route::post('/ime/callback', [PaymentController::class, 'imeCallback'])->name('ime.callback');
    });
    
    // My Bookings
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
        Route::get('/{booking}/edit', [BookingController::class, 'edit'])->name('edit');
        Route::put('/{booking}', [BookingController::class, 'update'])->name('update');
        Route::get('/upcoming', [BookingController::class, 'upcoming'])->name('upcoming');
        Route::get('/history', [BookingController::class, 'history'])->name('history');
        Route::get('/cancelled', [BookingController::class, 'cancelled'])->name('cancelled');
    });
    
    // E-Tickets
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/{booking}', [TicketController::class, 'show'])->name('show');
        Route::get('/{booking}/view', [TicketController::class, 'view'])->name('view');
        Route::get('/{booking}/download', [TicketController::class, 'download'])->name('download');
        Route::get('/{booking}/email', [TicketController::class, 'email'])->name('email');
        Route::post('/{booking}/email', [TicketController::class, 'sendEmail'])->name('send-email');
        Route::get('/{booking}/qr', [TicketController::class, 'qrCode'])->name('qr');
    });
    
    // Customer Profile
    Route::prefix('profile')->name('customer.profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::get('/preferences', [ProfileController::class, 'preferences'])->name('preferences');
        Route::put('/preferences', [ProfileController::class, 'updatePreferences'])->name('update-preferences');
        Route::get('/notifications', [ProfileController::class, 'notifications'])->name('notifications');
        Route::put('/notifications', [ProfileController::class, 'updateNotifications'])->name('update-notifications');
    });
    
    // Favorites & Saved Routes
    Route::prefix('favorites')->name('favorites.')->group(function () {
        Route::get('/', [SearchController::class, 'favorites'])->name('index');
        Route::post('/routes/{route}', [SearchController::class, 'addFavoriteRoute'])->name('routes.add');
        Route::delete('/routes/{route}', [SearchController::class, 'removeFavoriteRoute'])->name('routes.remove');
        Route::post('/operators/{operator}', [SearchController::class, 'addFavoriteOperator'])->name('operators.add');
        Route::delete('/operators/{operator}', [SearchController::class, 'removeFavoriteOperator'])->name('operators.remove');
    });
    
    // Reviews & Ratings
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [BookingController::class, 'reviews'])->name('index');
        Route::get('/create/{booking}', [BookingController::class, 'createReview'])->name('create');
        Route::post('/store/{booking}', [BookingController::class, 'storeReview'])->name('store');
        Route::get('/{review}/edit', [BookingController::class, 'editReview'])->name('edit');
        Route::put('/{review}', [BookingController::class, 'updateReview'])->name('update');
    });
    
    // Help & Support
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [ProfileController::class, 'support'])->name('index');
        Route::get('/faq', [ProfileController::class, 'faq'])->name('faq');
        Route::get('/contact', [ProfileController::class, 'contact'])->name('contact');
        Route::post('/contact', [ProfileController::class, 'submitContact'])->name('submit-contact');
        Route::get('/tickets', [ProfileController::class, 'supportTickets'])->name('tickets');
        Route::post('/tickets', [ProfileController::class, 'createSupportTicket'])->name('create-ticket');
    });
});
