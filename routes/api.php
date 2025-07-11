<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SeatMapController;
use App\Http\Controllers\Api\RealtimeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Real-time seat map endpoints
Route::get('/schedules/{schedule}/seat-map', [SeatMapController::class, 'getSeatMap']);
Route::post('/schedules/{schedule}/reserve-seats', [SeatMapController::class, 'reserveSeats'])->middleware('auth');
Route::delete('/schedules/{schedule}/release-seats', [SeatMapController::class, 'releaseSeats'])->middleware('auth');

// Real-time data endpoints
Route::get('/realtime/dashboard-stats', [RealtimeController::class, 'getDashboardStats'])->middleware('auth');
Route::get('/realtime/booking-stats', [RealtimeController::class, 'getBookingStats'])->middleware('auth');
Route::get('/realtime/notifications', [RealtimeController::class, 'getNotifications'])->middleware('auth');
