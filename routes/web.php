<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

// Demo route for seat layouts
Route::get('/demo/seat-layouts', function () {
    return view('demo.seat-layouts');
})->name('demo.seat-layouts');

// Debug route to check bus data
Route::get('/debug/bus-data', function () {
    $bus = App\Models\Bus::first();
    if (!$bus) {
        return response()->json(['error' => 'No buses found']);
    }

    return response()->json([
        'bus_number' => $bus->bus_number,
        'total_seats' => $bus->total_seats,
        'seat_layout' => $bus->seat_layout,
        'has_new_format' => isset($bus->seat_layout['layout_type']),
        'seats_count' => count($bus->seat_layout['seats'] ?? [])
    ]);
});

// Test route for seat layout rendering
Route::get('/test/seat-layout', function () {
    return view('test-seat-layout');
});

// Role-based dashboard routing
Route::get('/dashboard', function () {
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login');
    }

    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->hasRole('operator')) {
        return redirect()->route('operator.dashboard');
    } else {
        // For regular users, redirect to customer dashboard
        return redirect()->route('customer.dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Payment Routes
    Route::get('/payment/{booking}/options', [App\Http\Controllers\PaymentController::class, 'showPaymentOptions'])->name('payment.options');
    Route::post('/payment/{booking}/esewa', [App\Http\Controllers\PaymentController::class, 'initiateEsewaPayment'])->name('payment.esewa.initiate');
    Route::get('/payment/esewa/success', [App\Http\Controllers\PaymentController::class, 'esewaSuccess'])->name('payment.esewa.success');
    Route::get('/payment/esewa/failure', [App\Http\Controllers\PaymentController::class, 'esewaFailure'])->name('payment.esewa.failure');
    Route::get('/payment/{payment}/status', [App\Http\Controllers\PaymentController::class, 'getPaymentStatus'])->name('payment.status');
    Route::get('/payments/history', [App\Http\Controllers\PaymentController::class, 'paymentHistory'])->name('payments.history');
});

require __DIR__.'/auth.php';
