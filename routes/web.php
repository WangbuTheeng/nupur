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

// Demo route for customer dashboard
Route::get('/demo/customer-dashboard', function () {
    return view('demo.customer-dashboard');
})->name('demo.customer-dashboard');

// Demo route for modern navbar
Route::get('/demo/modern-navbar', function () {
    return view('demo.modern-navbar');
})->name('demo.modern-navbar');

// Test route for navbar functionality
Route::get('/test/navbar', function () {
    return view('test-navbar');
})->name('test.navbar');



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

// Debug route to check specific bus
Route::get('/debug/bus/{id}', function ($id) {
    $bus = App\Models\Bus::find($id);
    if (!$bus) {
        return response()->json(['error' => 'Bus not found']);
    }

    return response()->json([
        'id' => $bus->id,
        'bus_number' => $bus->bus_number,
        'total_seats' => $bus->total_seats,
        'seat_layout' => $bus->seat_layout,
        'has_new_format' => isset($bus->seat_layout['layout_type']),
        'seats_count' => count($bus->seat_layout['seats'] ?? []),
        'layout_type' => $bus->seat_layout['layout_type'] ?? 'N/A',
        'has_back_row' => $bus->seat_layout['has_back_row'] ?? false,
        'aisle_position' => $bus->seat_layout['aisle_position'] ?? 'N/A'
    ]);
});

// Test route for seat layout rendering
Route::get('/test/seat-layout', function () {
    return view('test-seat-layout');
});

// Test route for seat color verification
Route::get('/test/seat-colors', function () {
    return view('test-seat-colors');
});

// Test route for responsive seat layout
Route::get('/test/responsive-seat-layout', function () {
    return view('test-responsive-seat-layout');
});

// Test route for seat restrictions
Route::get('/test/seat-restrictions', function () {
    return view('test-seat-restrictions');
});

// Test route for counter booking
Route::get('/test/counter-booking', function () {
    $schedule = App\Models\Schedule::with(['bus', 'route.sourceCity', 'route.destinationCity'])
        ->where('available_seats', '>', 0)
        ->first();

    if (!$schedule) {
        return response()->json(['error' => 'No available schedules found']);
    }

    return response()->json([
        'schedule_id' => $schedule->id,
        'route' => $schedule->route->name,
        'travel_date' => $schedule->travel_date,
        'available_seats' => $schedule->available_seats,
        'bus_number' => $schedule->bus->bus_number,
        'total_seats' => $schedule->bus->total_seats,
        'seat_layout_exists' => isset($schedule->bus->seat_layout['seats']),
        'seat_count' => count($schedule->bus->seat_layout['seats'] ?? []),
        'counter_booking_url' => route('operator.counter.book', $schedule),
        'booking_store_url' => route('operator.counter.book.store', $schedule),
    ]);
});

// Test route for booking submission
Route::post('/test/booking-submit', function (Illuminate\Http\Request $request) {
    \Log::info('Test booking submission received', [
        'all_data' => $request->all(),
        'seat_numbers' => $request->seat_numbers,
        'method' => $request->method(),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Test booking submission received',
        'data' => $request->all(),
    ]);
})->name('test.booking.submit');

// Debug route for counter booking
Route::post('/debug/counter-booking/{schedule}', function (Illuminate\Http\Request $request, App\Models\Schedule $schedule) {
    \Log::info('Debug counter booking submission', [
        'request_data' => $request->all(),
        'schedule_id' => $schedule->id,
        'has_seat_numbers' => $request->has('seat_numbers'),
        'seat_numbers' => $request->input('seat_numbers'),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Debug submission received',
        'data' => $request->all(),
        'schedule_id' => $schedule->id,
        'seat_numbers' => $request->input('seat_numbers'),
        'validation_errors' => [],
    ]);
})->middleware(['auth', 'operator'])->name('debug.counter.booking');

// Test page for counter booking
Route::get('/test/counter-booking-debug', function () {
    return view('test-counter-booking');
})->name('test.counter.booking.debug');

// Test customer search and booking flow
Route::get('/test/customer-search', function () {
    $cities = \App\Models\City::active()->withActiveRoutes()->orderBy('name')->get()->unique('name');
    $schedules = \App\Models\Schedule::with(['route.sourceCity', 'route.destinationCity', 'bus.busType', 'operator'])
        ->bookableOnline()
        ->where('travel_date', '>=', now()->toDateString())
        ->limit(10)
        ->get();

    return view('test-customer-search', compact('cities', 'schedules'));
})->name('test.customer.search');

// Debug route to check seat data for a specific schedule
Route::get('/test/seat-data/{schedule}', function (App\Models\Schedule $schedule) {
    $controller = new App\Http\Controllers\Operator\CounterController();
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('generateSeatMapWithBookings');
    $method->setAccessible(true);

    $seatMap = $method->invoke($controller, $schedule);

    return response()->json([
        'schedule_id' => $schedule->id,
        'bus_id' => $schedule->bus->id,
        'bus_total_seats' => $schedule->bus->total_seats,
        'bus_seat_layout' => $schedule->bus->seat_layout,
        'generated_seat_map' => $seatMap,
        'seat_count' => count($seatMap['seats'] ?? []),
        'first_seat' => $seatMap['seats'][0] ?? null,
        'sample_seats' => array_slice($seatMap['seats'] ?? [], 0, 10),
    ], JSON_PRETTY_PRINT);
})->name('test.seat.data');

// Route to regenerate seat layout for a specific bus
Route::get('/test/regenerate-bus-layout/{bus}', function (App\Models\Bus $bus) {
    $seatLayoutService = new App\Services\SeatLayoutService();
    $totalSeats = $bus->total_seats;
    $layoutType = '2x2'; // Default layout
    $hasBackRow = $totalSeats > 25; // Add back row for larger buses

    // Generate new layout
    $newLayout = $seatLayoutService->generateSeatLayout($totalSeats, $layoutType, $hasBackRow);

    // Update the bus
    $bus->seat_layout = $newLayout;
    $bus->save();

    return response()->json([
        'success' => true,
        'message' => 'Bus seat layout regenerated successfully',
        'bus_id' => $bus->id,
        'total_seats' => $totalSeats,
        'layout_type' => $layoutType,
        'has_back_row' => $hasBackRow,
        'new_layout' => $newLayout,
        'first_seat' => $newLayout['seats'][0] ?? null,
        'sample_seats' => array_slice($newLayout['seats'] ?? [], 0, 10),
    ], JSON_PRETTY_PRINT);
})->name('test.regenerate.bus.layout');

// Route to regenerate all bus layouts
Route::get('/test/regenerate-all-layouts', function () {
    $seatLayoutService = new App\Services\SeatLayoutService();
    $buses = App\Models\Bus::all();
    $updated = 0;
    $results = [];

    foreach ($buses as $bus) {
        $totalSeats = $bus->total_seats;
        $layoutType = '2x2'; // Default layout
        $hasBackRow = $totalSeats > 25; // Add back row for larger buses

        // Generate new layout
        $newLayout = $seatLayoutService->generateSeatLayout($totalSeats, $layoutType, $hasBackRow);

        // Update the bus
        $bus->seat_layout = $newLayout;
        $bus->save();

        $updated++;
        $results[] = [
            'bus_id' => $bus->id,
            'bus_number' => $bus->bus_number,
            'total_seats' => $totalSeats,
            'first_seat' => $newLayout['seats'][0] ?? null,
            'last_seat' => isset($newLayout['seats']) && count($newLayout['seats']) > 0 ?
                          $newLayout['seats'][count($newLayout['seats']) - 1] : null,
        ];
    }

    return response()->json([
        'success' => true,
        'message' => "Successfully regenerated layouts for {$updated} buses",
        'updated_count' => $updated,
        'results' => $results,
    ], JSON_PRETTY_PRINT);
})->name('test.regenerate.all.layouts');

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

// eSewa Payment Callback Routes (must be outside auth middleware)
Route::get('/payment/esewa/success/{payment?}', [App\Http\Controllers\PaymentController::class, 'esewaSuccess'])->name('payment.esewa.success');
Route::get('/payment/esewa/failure/{payment?}', [App\Http\Controllers\PaymentController::class, 'esewaFailure'])->name('payment.esewa.failure');

// Test Payment Completion Route (for bypassing eSewa issues) - TEMPORARILY ENABLED
Route::get('/payment/test-complete/{booking}', [App\Http\Controllers\PaymentController::class, 'testComplete'])->name('payment.test.complete');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Payment Routes (require authentication)
    Route::get('/payment/{booking}/options', [App\Http\Controllers\PaymentController::class, 'showPaymentOptions'])->name('payment.options');
    Route::post('/payment/{booking}/esewa', [App\Http\Controllers\PaymentController::class, 'initiateEsewaPayment'])->name('payment.esewa.initiate');
    Route::get('/payment/{payment}/status', [App\Http\Controllers\PaymentController::class, 'getPaymentStatus'])->name('payment.status');
    Route::get('/payment/{payment}/esewa/check-status', [App\Http\Controllers\PaymentController::class, 'checkEsewaStatus'])->name('payment.esewa.check-status');
    Route::get('/payments/history', [App\Http\Controllers\PaymentController::class, 'paymentHistory'])->name('payments.history');

    // Public Ticket Verification Routes (accessible without authentication)
    Route::get('/verify-ticket', [App\Http\Controllers\TicketController::class, 'showVerifyForm'])->name('tickets.verify.form');
    Route::post('/verify-ticket', [App\Http\Controllers\TicketController::class, 'verify'])->name('tickets.verify');
    Route::post('/verify-ticket/manual', [App\Http\Controllers\TicketController::class, 'verifyManual'])->name('tickets.verify.manual');
});

require __DIR__.'/auth.php';

Route::get('/debug/seat-layout/{bus}', function (App\Models\Bus $bus) {
    $seatLayoutService = new \App\Services\SeatLayoutService();
    return response()->json($seatLayoutService->generateSeatLayout($bus->total_seats, $bus->seat_layout['layout_type'] ?? '2x2', $bus->seat_layout['has_back_row'] ?? true));
});

// Fix seat layout for a specific bus with detailed debugging
Route::get('/fix/bus-seat-layout/{id}', function ($id) {
    $bus = App\Models\Bus::find($id);
    if (!$bus) {
        return response()->json(['error' => 'Bus not found']);
    }

    // Get old layout
    $oldLayout = $bus->seat_layout;
    $oldSeats = collect($oldLayout['seats'] ?? [])->pluck('number')->toArray();
    $oldDuplicates = collect($oldSeats)->duplicates()->toArray();

    // Generate new seat layout
    $seatLayoutService = new \App\Services\SeatLayoutService();
    $newLayout = $seatLayoutService->generateSeatLayout($bus->total_seats, '2x2', true);
    $newSeats = collect($newLayout['seats'] ?? [])->pluck('number')->toArray();
    $newDuplicates = collect($newSeats)->duplicates()->toArray();

    // Update the bus
    $bus->seat_layout = $newLayout;
    $saved = $bus->save();

    // Verify the update
    $bus->refresh();
    $verifySeats = collect($bus->seat_layout['seats'] ?? [])->pluck('number')->toArray();
    $verifyDuplicates = collect($verifySeats)->duplicates()->toArray();

    return response()->json([
        'success' => $saved,
        'message' => 'Seat layout updated for bus ' . $bus->bus_number,
        'debug' => [
            'old_seats' => $oldSeats,
            'old_duplicates' => $oldDuplicates,
            'new_seats' => $newSeats,
            'new_duplicates' => $newDuplicates,
            'verify_seats' => $verifySeats,
            'verify_duplicates' => $verifyDuplicates,
            'save_result' => $saved,
        ],
        'layout' => $newLayout
    ], JSON_PRETTY_PRINT);
});

// Test seat layout generation
Route::get('/test/seat-layout/{totalSeats}/{layoutType?}', function ($totalSeats, $layoutType = '2x2') {
    $seatLayoutService = new \App\Services\SeatLayoutService();
    $layout = $seatLayoutService->generateSeatLayout($totalSeats, $layoutType, true);

    // Group seats by row for analysis
    $seatsByRow = [];
    foreach ($layout['seats'] ?? [] as $seat) {
        $row = $seat['row'];
        if (!isset($seatsByRow[$row])) {
            $seatsByRow[$row] = [];
        }
        $seatsByRow[$row][] = $seat['number'];
    }

    return response()->json([
        'total_seats' => $totalSeats,
        'layout_type' => $layoutType,
        'layout' => $layout,
        'seat_count' => count($layout['seats'] ?? []),
        'seats_by_row' => $seatsByRow,
        'first_5_seats' => array_slice($layout['seats'] ?? [], 0, 5),
        'last_5_seats' => array_slice($layout['seats'] ?? [], -5),
        'all_seat_numbers' => array_column($layout['seats'] ?? [], 'number'),
    ], JSON_PRETTY_PRINT);
});

// Test counter booking seat layout
Route::get('/test/counter-seat-layout/{busId}', function ($busId) {
    $bus = App\Models\Bus::find($busId);
    if (!$bus) {
        return response()->json(['error' => 'Bus not found']);
    }

    $seatMap = $bus->seat_layout;
    $seats = $seatMap['seats'] ?? [];
    $rows = $seatMap['rows'] ?? 8;

    // Group seats by row (same logic as counter booking)
    $seatsByRow = [];
    foreach ($seats as $seat) {
        $row = $seat['row'] ?? 1;
        if (!isset($seatsByRow[$row])) {
            $seatsByRow[$row] = [];
        }
        $seatsByRow[$row][] = $seat;
    }

    // Sort seats within each row by column
    foreach ($seatsByRow as &$rowSeats) {
        usort($rowSeats, function($a, $b) {
            return ($a['column'] ?? 1) - ($b['column'] ?? 1);
        });
    }

    // Analyze each row
    $rowAnalysis = [];
    for ($row = 1; $row <= $rows; $row++) {
        if (isset($seatsByRow[$row]) && count($seatsByRow[$row]) > 0) {
            $rowSeats = $seatsByRow[$row];
            $isBackRow = ($seatMap['has_back_row'] ?? false) && $row == $rows;

            $rowAnalysis[$row] = [
                'is_back_row' => $isBackRow,
                'seat_count' => count($rowSeats),
                'seat_numbers' => array_column($rowSeats, 'number'),
                'seat_columns' => array_column($rowSeats, 'column'),
                'seats' => $rowSeats
            ];
        }
    }

    return response()->json([
        'bus_id' => $busId,
        'bus_number' => $bus->bus_number,
        'total_seats' => $bus->total_seats,
        'layout_type' => $seatMap['layout_type'] ?? 'N/A',
        'rows' => $rows,
        'has_back_row' => $seatMap['has_back_row'] ?? false,
        'seats_by_row' => $seatsByRow,
        'row_analysis' => $rowAnalysis,
    ], JSON_PRETTY_PRINT);
});

// Debug seat generation step by step
Route::get('/debug/seat-generation/{totalSeats}/{layoutType?}', function ($totalSeats, $layoutType = '2x2') {
    $seatLayoutService = new \App\Services\SeatLayoutService();

    // Get the config
    $configs = [
        '2x2' => [
            'left_seats' => 2,
            'right_seats' => 2,
            'total_per_row' => 4,
            'aisle_position' => 2,
            'back_row_seats' => 5,
            'back_row_config' => ['left' => 2, 'right' => 3],
        ]
    ];

    $config = $configs[$layoutType] ?? $configs['2x2'];
    $hasBackRow = true;

    // Calculate seats distribution
    $backRowSeats = $hasBackRow ? $config['back_row_seats'] : 0;
    $regularSeats = $totalSeats - $backRowSeats;
    $seatsPerRow = $config['total_per_row'];
    $regularRows = ceil($regularSeats / $seatsPerRow);

    $debug = [
        'total_seats' => $totalSeats,
        'back_row_seats' => $backRowSeats,
        'regular_seats' => $regularSeats,
        'seats_per_row' => $seatsPerRow,
        'regular_rows' => $regularRows,
        'config' => $config,
    ];

    // Generate seats step by step
    $allSeats = [];
    $seatNumber = 1;

    for ($row = 1; $row <= $regularRows; $row++) {
        $seatsGenerated = ($row - 1) * $seatsPerRow;
        $remainingSeats = $regularSeats - $seatsGenerated;
        $seatsInThisRow = min($seatsPerRow, $remainingSeats);

        $debug['row_' . $row] = [
            'seats_generated_before' => $seatsGenerated,
            'remaining_seats' => $remainingSeats,
            'seats_in_this_row' => $seatsInThisRow,
            'starting_seat_number' => $seatNumber,
        ];

        // Generate seats for this row
        for ($i = 1; $i <= $seatsInThisRow; $i++) {
            $allSeats[] = [
                'number' => $seatNumber,
                'row' => $row,
                'column' => $i <= 2 ? $i : $i + 1, // Skip aisle position
                'type' => 'regular'
            ];
            $seatNumber++;
        }

        $debug['row_' . $row]['ending_seat_number'] = $seatNumber - 1;
        $debug['row_' . $row]['next_seat_number'] = $seatNumber;
    }

    // Generate back row
    if ($hasBackRow && $backRowSeats > 0) {
        $debug['back_row'] = [
            'starting_seat_number' => $seatNumber,
            'seats_count' => $backRowSeats,
        ];

        for ($i = 1; $i <= $backRowSeats; $i++) {
            $allSeats[] = [
                'number' => $seatNumber,
                'row' => $regularRows + 1,
                'column' => $i,
                'type' => 'back_row'
            ];
            $seatNumber++;
        }

        $debug['back_row']['ending_seat_number'] = $seatNumber - 1;
    }

    return response()->json([
        'debug' => $debug,
        'all_seats' => $allSeats,
        'seat_count' => count($allSeats),
    ], JSON_PRETTY_PRINT);
});

// Check actual database content for bus seat layout
Route::get('/debug/bus-database/{busId}', function ($busId) {
    $bus = \App\Models\Bus::find($busId);

    if (!$bus) {
        return response()->json(['error' => 'Bus not found'], 404);
    }

    return response()->json([
        'bus_id' => $bus->id,
        'bus_number' => $bus->bus_number,
        'total_seats' => $bus->total_seats,
        'seat_layout_raw' => $bus->seat_layout,
        'seat_layout_formatted' => $bus->seat_layout,
        'seats_count' => count($bus->seat_layout['seats'] ?? []),
        'seats_numbers' => collect($bus->seat_layout['seats'] ?? [])->pluck('number')->toArray(),
        'duplicate_check' => collect($bus->seat_layout['seats'] ?? [])->pluck('number')->duplicates()->toArray(),
        'updated_at' => $bus->updated_at,
    ], JSON_PRETTY_PRINT);
});

// Check all buses for duplicate seat numbers
Route::get('/debug/check-all-buses-duplicates', function () {
    $buses = \App\Models\Bus::all();
    $results = [];

    foreach ($buses as $bus) {
        $seats = collect($bus->seat_layout['seats'] ?? [])->pluck('number')->toArray();
        $duplicates = collect($seats)->duplicates()->toArray();

        $results[] = [
            'bus_id' => $bus->id,
            'bus_number' => $bus->bus_number,
            'total_seats' => $bus->total_seats,
            'seats_count' => count($seats),
            'has_duplicates' => !empty($duplicates),
            'duplicates' => $duplicates,
            'all_seats' => $seats,
        ];
    }

    return response()->json([
        'total_buses' => count($buses),
        'buses_with_duplicates' => collect($results)->where('has_duplicates', true)->count(),
        'results' => $results,
    ], JSON_PRETTY_PRINT);
});

// Fix all buses with duplicate seat numbers
Route::get('/fix/all-buses-duplicates', function () {
    $buses = \App\Models\Bus::all();
    $fixed = [];
    $seatLayoutService = new \App\Services\SeatLayoutService();

    foreach ($buses as $bus) {
        $oldSeats = collect($bus->seat_layout['seats'] ?? [])->pluck('number')->toArray();
        $oldDuplicates = collect($oldSeats)->duplicates()->toArray();

        if (!empty($oldDuplicates)) {
            // Generate new seat layout
            $newLayout = $seatLayoutService->generateSeatLayout($bus->total_seats, '2x2', true);

            // Update the bus
            $bus->seat_layout = $newLayout;
            $bus->save();

            $fixed[] = [
                'bus_id' => $bus->id,
                'bus_number' => $bus->bus_number,
                'old_duplicates' => $oldDuplicates,
                'fixed' => true,
            ];
        }
    }

    return response()->json([
        'message' => 'Fixed all buses with duplicate seat numbers',
        'total_fixed' => count($fixed),
        'fixed_buses' => $fixed,
    ], JSON_PRETTY_PRINT);
});

// Test email configuration
Route::get('/test/email', function () {
    try {
        Mail::raw('Test email from BookNGO - Email configuration is working!', function($message) {
            $message->to('wangbutamang22@gmail.com')
                   ->subject('BookNGO Email Test - ' . now());
        });

        return response()->json([
            'success' => true,
            'message' => 'Test email sent successfully! Check your email inbox.',
            'config' => [
                'mailer' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'from' => config('mail.from.address')
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Email sending failed: ' . $e->getMessage(),
            'error' => $e->getMessage()
        ], 500);
    }
})->name('test.email');

// Simple test email route
Route::get('/test/simple-email', function () {
    try {
        $result = Mail::raw('Simple test email', function($message) {
            $message->to('wangbutamang22@gmail.com')->subject('Simple Test');
        });

        return 'Email sent successfully! Result: ' . ($result ? 'true' : 'false');
    } catch (\Exception $e) {
        return 'Email failed: ' . $e->getMessage();
    }
});

// Debug eSewa payment parameters
Route::get('/debug/esewa-params/{booking}', function (App\Models\Booking $booking) {
    $esewaService = new App\Services\EsewaPaymentService();
    $result = $esewaService->initiatePayment($booking);

    if ($result['success']) {
        return response()->json([
            'payment_data' => $result['payment_data'],
            'form_html' => $result['form_html']
        ], 200, [], JSON_PRETTY_PRINT);
    } else {
        return response()->json(['error' => $result['message']], 400);
    }
})->name('debug.esewa.params');

// Debug eSewa configuration
Route::get('/debug/esewa-config', function () {
    return response()->json([
        'merchant_id' => config('services.esewa.merchant_id'),
        'base_url' => config('services.esewa.base_url'),
        'payment_url' => config('services.esewa.payment_url'),
        'status_check_url' => config('services.esewa.status_check_url'),
        'success_url' => config('services.esewa.success_url'),
        'failure_url' => config('services.esewa.failure_url'),
        'secret_key_set' => !empty(config('services.esewa.secret_key')),
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.esewa.config');

// Test eSewa signature generation
Route::get('/debug/esewa-signature', function () {
    // Test data from documentation
    $testData = [
        'total_amount' => 100,
        'transaction_uuid' => '11-201-13',
        'product_code' => 'EPAYTEST',
        'signed_field_names' => 'total_amount,transaction_uuid,product_code'
    ];

    $message = sprintf('total_amount=%s,transaction_uuid=%s,product_code=%s',
        $testData['total_amount'],
        $testData['transaction_uuid'],
        $testData['product_code']
    );

    $secretKey = config('services.esewa.secret_key');
    $signature = hash_hmac('sha256', $message, $secretKey, true);
    $signatureBase64 = base64_encode($signature);

    return response()->json([
        'message' => $message,
        'signature' => $signatureBase64,
        'expected_signature' => '4Ov7pCI1zIOdwtV2BRMUNjz1upIlT/COTxfLhWvVurE=',
        'matches_expected' => $signatureBase64 === '4Ov7pCI1zIOdwtV2BRMUNjz1upIlT/COTxfLhWvVurE=',
        'test_data' => $testData
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.esewa.signature');

// Test eSewa URL accessibility
Route::get('/debug/esewa-url-test', function () {
    $paymentUrl = config('services.esewa.payment_url');

    try {
        $response = \Illuminate\Support\Facades\Http::timeout(10)->get($paymentUrl);

        return response()->json([
            'url' => $paymentUrl,
            'status_code' => $response->status(),
            'accessible' => $response->successful() || $response->status() === 405, // 405 Method Not Allowed is expected for GET on POST endpoint
            'headers' => $response->headers(),
            'body_preview' => substr($response->body(), 0, 500)
        ], 200, [], JSON_PRETTY_PRINT);

    } catch (\Exception $e) {
        return response()->json([
            'url' => $paymentUrl,
            'error' => $e->getMessage(),
            'accessible' => false
        ], 200, [], JSON_PRETTY_PRINT);
    }
})->name('debug.esewa.url.test');

// Test eSewa service accessibility
Route::get('/debug/esewa-service-test', function () {
    $esewaService = new \App\Services\EsewaPaymentService();

    return response()->json([
        'url_accessible' => $esewaService->testUrlAccessibility(),
        'config' => [
            'payment_url' => config('services.esewa.payment_url'),
            'merchant_id' => config('services.esewa.merchant_id'),
            'base_url' => config('services.esewa.base_url'),
        ]
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.esewa.service.test');

// Test eSewa payment data format (matches documentation example)
Route::get('/debug/esewa-payment-data', function () {
    // Create test data matching the documentation example
    $testPaymentData = [
        'amount' => '100',
        'tax_amount' => '10',
        'total_amount' => '110',
        'transaction_uuid' => '241028',
        'product_code' => 'EPAYTEST',
        'product_service_charge' => '0',
        'product_delivery_charge' => '0',
        'success_url' => 'https://developer.esewa.com.np/success',
        'failure_url' => 'https://developer.esewa.com.np/failure',
        'signed_field_names' => 'total_amount,transaction_uuid,product_code',
    ];

    // Generate signature
    $message = sprintf(
        'total_amount=%s,transaction_uuid=%s,product_code=%s',
        $testPaymentData['total_amount'],
        $testPaymentData['transaction_uuid'],
        $testPaymentData['product_code']
    );

    $secretKey = config('services.esewa.secret_key');
    $signature = hash_hmac('sha256', $message, $secretKey, true);
    $signatureBase64 = base64_encode($signature);

    $testPaymentData['signature'] = $signatureBase64;

    // Generate form HTML
    $formFields = '';
    foreach ($testPaymentData as $key => $value) {
        $formFields .= '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($value) . '">' . "\n";
    }

    $formHtml = '
    <form id="esewa-payment-form" action="' . config('services.esewa.payment_url') . '" method="POST">
        ' . $formFields . '
        <button type="submit" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px;">
            Pay with eSewa (Test)
        </button>
    </form>';

    return response()->json([
        'message' => $message,
        'signature' => $signatureBase64,
        'expected_signature' => 'i94zsd3oXF6ZsSr/kGqT4sSzYQzjj1W/waxjWyRwaME=',
        'signatures_match' => $signatureBase64 === 'i94zsd3oXF6ZsSr/kGqT4sSzYQzjj1W/waxjWyRwaME=',
        'payment_data' => $testPaymentData,
        'form_html' => $formHtml,
        'payment_url' => config('services.esewa.payment_url')
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.esewa.payment.data');

// Test eSewa form page (HTML)
Route::get('/debug/esewa-form-test', function () {
    // Create test data matching the documentation example
    $testPaymentData = [
        'amount' => '100',
        'tax_amount' => '10',
        'total_amount' => '110',
        'transaction_uuid' => '241028-' . time(), // Make it unique
        'product_code' => 'EPAYTEST',
        'product_service_charge' => '0',
        'product_delivery_charge' => '0',
        'success_url' => url('/payment/esewa/success'),
        'failure_url' => url('/payment/esewa/failure'),
        'signed_field_names' => 'total_amount,transaction_uuid,product_code',
    ];

    // Generate signature
    $message = sprintf(
        'total_amount=%s,transaction_uuid=%s,product_code=%s',
        $testPaymentData['total_amount'],
        $testPaymentData['transaction_uuid'],
        $testPaymentData['product_code']
    );

    $secretKey = config('services.esewa.secret_key');
    $signature = hash_hmac('sha256', $message, $secretKey, true);
    $signatureBase64 = base64_encode($signature);

    $testPaymentData['signature'] = $signatureBase64;

    // Generate form fields
    $formFields = '';
    foreach ($testPaymentData as $key => $value) {
        $formFields .= '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($value) . '">' . "\n";
    }

    $html = '<!DOCTYPE html>
<html>
<head>
    <title>eSewa Payment Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        .info { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .form-container { background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #28a745; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; }
        button:hover { background: #218838; }
        .debug { background: #e9ecef; padding: 10px; border-radius: 3px; font-family: monospace; font-size: 12px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>eSewa Payment Test (v2 API)</h1>

        <div class="info">
            <h3>Test Information:</h3>
            <p><strong>Payment URL:</strong> ' . config('services.esewa.payment_url') . '</p>
            <p><strong>Amount:</strong> NPR 100 + NPR 10 (tax) = NPR 110</p>
            <p><strong>Transaction UUID:</strong> ' . $testPaymentData['transaction_uuid'] . '</p>
            <p><strong>Signature:</strong> ' . $signatureBase64 . '</p>
        </div>

        <div class="form-container">
            <form id="esewa-payment-form" action="' . config('services.esewa.payment_url') . '" method="POST">
                ' . $formFields . '
                <button type="submit">Pay NPR 110 with eSewa</button>
            </form>
        </div>

        <div class="debug">
            <strong>Debug Info:</strong><br>
            Message: ' . $message . '<br>
            Secret Key: ' . substr($secretKey, 0, 5) . '***<br>
            Generated Signature: ' . $signatureBase64 . '<br>
            Expected Signature: i94zsd3oXF6ZsSr/kGqT4sSzYQzjj1W/waxjWyRwaME=<br>
            Signatures Match: ' . ($signatureBase64 === 'i94zsd3oXF6ZsSr/kGqT4sSzYQzjj1W/waxjWyRwaME=' ? 'No (different transaction_uuid)' : 'No') . '
        </div>

        <script>
            // Auto-submit after 3 seconds
            setTimeout(function() {
                if (confirm("Auto-submit the form to eSewa?")) {
                    document.getElementById("esewa-payment-form").submit();
                }
            }, 3000);
        </script>
    </div>
</body>
</html>';

    return response($html)->header('Content-Type', 'text/html');
})->name('debug.esewa.form.test');

// Test eSewa with actual booking data
Route::get('/debug/esewa-booking-test/{booking}', function (\App\Models\Booking $booking) {
    $esewaService = new \App\Services\EsewaPaymentService();

    try {
        $result = $esewaService->initiatePayment($booking);

        if ($result['success']) {
            return response($result['form_html'])->header('Content-Type', 'text/html');
        } else {
            return response()->json([
                'error' => $result['message'],
                'booking_id' => $booking->id,
                'booking_amount' => $booking->total_amount
            ], 400, [], JSON_PRETTY_PRINT);
        }
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'booking_id' => $booking->id
        ], 500, [], JSON_PRETTY_PRINT);
    }
})->name('debug.esewa.booking.test');

// Test eSewa V2 Service (Robust Implementation)
Route::get('/debug/esewa-v2-test', function () {
    // Create test booking data
    $testBooking = new \App\Models\Booking([
        'id' => 999,
        'user_id' => 1,
        'total_amount' => 1500,
    ]);

    $esewaServiceV2 = new \App\Services\EsewaPaymentServiceV2();

    try {
        $result = $esewaServiceV2->initiatePayment($testBooking);

        if ($result['success']) {
            $html = '<!DOCTYPE html>
<html>
<head>
    <title>eSewa V2 Payment Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto">
        <h1 class="text-2xl font-bold mb-4 text-center">eSewa V2 Payment Test</h1>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <h3 class="font-semibold text-blue-800">Test Information:</h3>
            <p class="text-blue-700 text-sm">Amount: NPR 1,500</p>
            <p class="text-blue-700 text-sm">Transaction: ' . $result['payment_data']['transaction_uuid'] . '</p>
            <p class="text-blue-700 text-sm">Signature: ' . substr($result['payment_data']['signature'], 0, 20) . '...</p>
        </div>
        ' . $result['form_html'] . '
    </div>
</body>
</html>';

            return response($html)->header('Content-Type', 'text/html');
        } else {
            return response()->json([
                'error' => $result['message'],
                'test_booking' => $testBooking->toArray()
            ], 400, [], JSON_PRETTY_PRINT);
        }
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500, [], JSON_PRETTY_PRINT);
    }
})->name('debug.esewa.v2.test');

// Test eSewa URL accessibility (both production and test)
Route::get('/debug/esewa-url-status', function () {
    $urls = [
        'production' => 'https://epay.esewa.com.np/api/epay/main/v2/form',
        'test' => 'https://rc-epay.esewa.com.np/api/epay/main/v2/form',
    ];

    $results = [];

    foreach ($urls as $type => $url) {
        try {
            $start = microtime(true);
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get($url);
            $end = microtime(true);

            $results[$type] = [
                'url' => $url,
                'status_code' => $response->status(),
                'accessible' => $response->successful() || $response->status() === 405,
                'response_time' => round(($end - $start) * 1000, 2) . 'ms',
                'headers' => $response->headers(),
                'body_preview' => substr($response->body(), 0, 200)
            ];
        } catch (\Exception $e) {
            $results[$type] = [
                'url' => $url,
                'accessible' => false,
                'error' => $e->getMessage(),
                'response_time' => 'timeout'
            ];
        }
    }

    return response()->json([
        'timestamp' => now()->toDateTimeString(),
        'results' => $results,
        'recommendation' => $results['production']['accessible'] ? 'Use production URL' :
                          ($results['test']['accessible'] ? 'Use test URL' : 'Both URLs unavailable - use test payment'),
        'current_config' => config('services.esewa.payment_url')
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.esewa.url.status');

// Test eSewa V3 Service (Ultimate Robust Implementation)
Route::get('/debug/esewa-v3-test', function () {
    // Create test booking data
    $testBooking = new \App\Models\Booking([
        'id' => 999,
        'user_id' => 1,
        'total_amount' => 1200,
    ]);

    $esewaServiceV3 = new \App\Services\EsewaPaymentServiceV3();

    try {
        $result = $esewaServiceV3->initiatePayment($testBooking);

        if ($result['success']) {
            $html = '<!DOCTYPE html>
<html>
<head>
    <title>eSewa V3 Payment Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto">
        <h1 class="text-2xl font-bold mb-4 text-center">eSewa V3 Payment Test</h1>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <h3 class="font-semibold text-blue-800">Test Information:</h3>
            <p class="text-blue-700 text-sm">Amount: NPR 1,200</p>
            <p class="text-blue-700 text-sm">Payment Method: ' . $result['payment']->payment_method . '</p>
            <p class="text-blue-700 text-sm">Transaction: ' . $result['payment']->transaction_id . '</p>
            <p class="text-blue-700 text-sm">Is Simulation: ' . ($result['is_simulation'] ? 'Yes' : 'No') . '</p>
        </div>
        ' . $result['form_html'] . '
    </div>
</body>
</html>';

            return response($html)->header('Content-Type', 'text/html');
        } else {
            return response()->json([
                'error' => $result['message'],
                'test_booking' => $testBooking->toArray()
            ], 400, [], JSON_PRETTY_PRINT);
        }
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500, [], JSON_PRETTY_PRINT);
    }
})->name('debug.esewa.v3.test');

// Comprehensive eSewa Status Dashboard
Route::get('/debug/esewa-status-dashboard', function () {
    $html = '<!DOCTYPE html>
<html>
<head>
    <title>eSewa Integration Status Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta http-equiv="refresh" content="30">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-center text-gray-800">eSewa Integration Status Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- URL Status Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">URL Accessibility</h3>
                <div class="space-y-2">
                    <a href="/debug/esewa-url-status" target="_blank" class="block w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-center">
                        Check URL Status
                    </a>
                </div>
            </div>

            <!-- Original Service Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Original eSewa Service</h3>
                <div class="space-y-2">
                    <a href="/debug/esewa-config" target="_blank" class="block w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-center">
                        Check Config
                    </a>
                    <a href="/debug/esewa-form-test" target="_blank" class="block w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-center">
                        Test Form
                    </a>
                </div>
            </div>

            <!-- V2 Service Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">eSewa V2 Service</h3>
                <div class="space-y-2">
                    <a href="/debug/esewa-v2-test" target="_blank" class="block w-full bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 text-center">
                        Test V2 Service
                    </a>
                </div>
            </div>

            <!-- V3 Service Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">eSewa V3 Service (Robust)</h3>
                <div class="space-y-2">
                    <a href="/debug/esewa-v3-test" target="_blank" class="block w-full bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-center">
                        Test V3 Service
                    </a>
                </div>
                <p class="text-sm text-gray-600 mt-2">Includes intelligent fallbacks and simulator</p>
            </div>

            <!-- Simulator Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">eSewa Simulator</h3>
                <div class="space-y-2">
                    <button onclick="testSimulator()" class="block w-full bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 text-center">
                        Test Simulator
                    </button>
                </div>
                <p class="text-sm text-gray-600 mt-2">For testing when eSewa is unavailable</p>
            </div>

            <!-- Current Status Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Current Status</h3>
                <div class="space-y-2">
                    <div class="text-sm">
                        <span class="font-medium">Environment:</span>
                        <span class="text-blue-600">Development</span>
                    </div>
                    <div class="text-sm">
                        <span class="font-medium">Merchant ID:</span>
                        <span class="text-green-600">EPAYTEST</span>
                    </div>
                    <div class="text-sm">
                        <span class="font-medium">Auto-refresh:</span>
                        <span class="text-gray-600">30 seconds</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Implementation Notes</h3>
            <div class="space-y-3 text-sm text-gray-600">
                <div class="flex items-start">
                    <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                    <span><strong>V3 Service:</strong> Automatically detects working eSewa URLs and falls back to simulator when needed</span>
                </div>
                <div class="flex items-start">
                    <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                    <span><strong>Simulator:</strong> Provides realistic payment testing when eSewa test environment is down</span>
                </div>
                <div class="flex items-start">
                    <span class="w-2 h-2 bg-yellow-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                    <span><strong>Error Handling:</strong> Graceful fallbacks ensure payment flow always works</span>
                </div>
                <div class="flex items-start">
                    <span class="w-2 h-2 bg-purple-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                    <span><strong>Production Ready:</strong> Simply update merchant credentials for live environment</span>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center text-sm text-gray-500">
            Last updated: ' . now()->format('Y-m-d H:i:s') . ' | Auto-refresh enabled
        </div>
    </div>

    <script>
        function testSimulator() {
            // Create a test booking and redirect to V3 test (which will use simulator if eSewa is down)
            window.open("/debug/esewa-v3-test", "_blank");
        }
    </script>
</body>
</html>';

    return response($html)->header('Content-Type', 'text/html');
})->name('debug.esewa.dashboard');

// Debug seat selection functionality
Route::get('/debug/seat-selection-test', function () {
    // Find a schedule with available seats for testing
    $schedule = \App\Models\Schedule::with(['route', 'bus'])
        ->where('travel_date', '>=', now()->toDateString())
        ->first();

    if (!$schedule) {
        return response()->json([
            'error' => 'No schedules found for testing',
            'suggestion' => 'Create a schedule first'
        ], 404);
    }

    return response()->json([
        'schedule_id' => $schedule->id,
        'route' => $schedule->route->full_name,
        'travel_date' => $schedule->travel_date->format('Y-m-d'),
        'departure_time' => $schedule->departure_time->format('H:i'),
        'bus' => $schedule->bus->display_name,
        'seat_selection_url' => route('booking.seat-selection', $schedule->id),
        'test_instructions' => [
            '1. Visit the seat selection URL',
            '2. Open browser console (F12)',
            '3. Look for seat selection debug messages',
            '4. Try clicking on green seats',
            '5. Use debugSeatSelection() in console for debug info',
            '6. Use testSeatClick("1") to manually test seat selection'
        ]
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.seat.selection.test');

// Debug eSewa payment integration with official credentials
Route::get('/debug/esewa-test', function () {
    // Test eSewa signature generation with official credentials
    $merchantId = 'EPAYTEST';
    $secretKey = '8gBm/:&EnhH.1/q';
    $totalAmount = 100;
    $transactionUuid = '241028';
    $productCode = 'EPAYTEST';

    // Generate signature exactly as per official documentation
    $signatureData = "total_amount={$totalAmount},transaction_uuid={$transactionUuid},product_code={$productCode}";
    $signature = base64_encode(hash_hmac('sha256', $signatureData, $secretKey, true));

    // Expected signature from documentation: i94zsd3oXF6ZsSr/kGqT4sSzYQzjj1W/waxjWyRwaME=
    $expectedSignature = 'i94zsd3oXF6ZsSr/kGqT4sSzYQzjj1W/waxjWyRwaME=';

    return response()->json([
        'test_data' => [
            'merchant_id' => $merchantId,
            'secret_key' => $secretKey,
            'total_amount' => $totalAmount,
            'transaction_uuid' => $transactionUuid,
            'product_code' => $productCode
        ],
        'signature_generation' => [
            'signature_data' => $signatureData,
            'generated_signature' => $signature,
            'expected_signature' => $expectedSignature,
            'signatures_match' => $signature === $expectedSignature
        ],
        'official_urls' => [
            'test_payment_url' => 'https://rc-epay.esewa.com.np/api/epay/main/v2/form',
            'production_payment_url' => 'https://epay.esewa.com.np/api/epay/main/v2/form',
            'test_status_url' => 'https://rc.esewa.com.np/api/epay/transaction/status/',
            'production_status_url' => 'https://epay.esewa.com.np/api/epay/transaction/status/'
        ],
        'test_credentials' => [
            'esewa_id' => '9806800001/2/3/4/5',
            'password' => 'Nepal@123',
            'mpin' => '1122',
            'token' => '123456'
        ],
        'status' => $signature === $expectedSignature ? 'SUCCESS - Signature matches official documentation' : 'ERROR - Signature mismatch'
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.esewa.test');

// Test eSewa payment with a real booking
Route::get('/debug/esewa-payment-test', function () {
    // Find or create a test booking
    $user = \App\Models\User::first();
    if (!$user) {
        return response()->json(['error' => 'No users found. Create a user first.'], 404);
    }

    $schedule = \App\Models\Schedule::with(['route', 'bus'])
        ->where('travel_date', '>=', now()->toDateString())
        ->first();

    if (!$schedule) {
        return response()->json(['error' => 'No schedules found. Create a schedule first.'], 404);
    }

    // Create a test booking with correct field names
    $booking = \App\Models\Booking::create([
        'user_id' => $user->id,
        'schedule_id' => $schedule->id,
        'seat_numbers' => ['1', '2'],
        'passenger_count' => 2,
        'total_amount' => 1000.00,
        'status' => 'pending',
        'payment_status' => 'pending',
        'booking_reference' => 'TEST-' . time(),
        'passenger_details' => json_encode([
            'name' => 'Test User',
            'phone' => '9800000000',
            'email' => 'test@example.com'
        ]),
        'contact_phone' => '9800000000',
        'contact_email' => 'test@example.com',
        'booking_type' => 'online'
    ]);

    return response()->json([
        'booking_created' => [
            'id' => $booking->id,
            'reference' => $booking->booking_reference,
            'amount' => $booking->total_amount,
            'seats' => $booking->seat_numbers,
            'passenger_count' => $booking->passenger_count,
            'contact_phone' => $booking->contact_phone,
            'contact_email' => $booking->contact_email
        ],
        'payment_test_url' => route('payment.options', $booking->id),
        'instructions' => [
            '1. Visit the payment test URL above',
            '2. Click "Pay with eSewa"',
            '3. You should be redirected to eSewa login page',
            '4. Use test credentials: ID: 9806800001, Password: Nepal@123, Token: 123456',
            '5. Complete the payment to test the full flow'
        ],
        'test_credentials' => [
            'esewa_id' => '9806800001 (or 9806800002/3/4/5)',
            'password' => 'Nepal@123',
            'mpin' => '1122',
            'token' => '123456'
        ]
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.esewa.payment.test');

// Direct eSewa payment form test (bypasses booking creation)
Route::get('/debug/esewa-direct-test', function () {
    // Official eSewa v2 test data from documentation
    $merchantId = 'EPAYTEST';
    $secretKey = '8gBm/:&EnhH.1/q';
    $amount = 100.0;
    $taxAmount = 10.0;
    $totalAmount = 110.0;
    $transactionUuid = '241028-' . time(); // Make it unique
    $serviceCharge = 0.0;
    $deliveryCharge = 0.0;

    // Generate signature
    $signatureData = "total_amount={$totalAmount},transaction_uuid={$transactionUuid},product_code={$merchantId}";
    $signature = base64_encode(hash_hmac('sha256', $signatureData, $secretKey, true));

    // Form data
    $formData = [
        'amount' => $amount,
        'tax_amount' => $taxAmount,
        'total_amount' => $totalAmount,
        'transaction_uuid' => $transactionUuid,
        'product_code' => $merchantId,
        'product_service_charge' => $serviceCharge,
        'product_delivery_charge' => $deliveryCharge,
        'success_url' => 'https://developer.esewa.com.np/success',
        'failure_url' => 'https://developer.esewa.com.np/failure',
        'signed_field_names' => 'total_amount,transaction_uuid,product_code',
        'signature' => $signature
    ];

    // Generate form HTML
    $formFields = '';
    foreach ($formData as $key => $value) {
        $formFields .= '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($value) . '">' . "\n";
    }

    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <title>eSewa Direct Payment Test</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
            .form-container { background: #f8f9fa; padding: 30px; border-radius: 10px; margin: 20px 0; }
            .btn { background: #28a745; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; }
            .btn:hover { background: #218838; }
            .info { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 10px 0; }
            .data { background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; }
        </style>
    </head>
    <body>
        <h1>🔧 eSewa Direct Payment Test</h1>

        <div class="info">
            <h3>📋 Test Information:</h3>
            <p><strong>Purpose:</strong> Direct test of eSewa v2 API with official credentials</p>
            <p><strong>Amount:</strong> Rs. ' . $totalAmount . ' (Amount: ' . $amount . ' + Tax: ' . $taxAmount . ')</p>
            <p><strong>Transaction ID:</strong> ' . $transactionUuid . '</p>
        </div>

        <div class="info">
            <h3>🔑 Test Credentials (Use these on eSewa login page):</h3>
            <p><strong>eSewa ID:</strong> 9806800001 (or 9806800002/3/4/5)</p>
            <p><strong>Password:</strong> Nepal@123</p>
            <p><strong>MPIN:</strong> 1122</p>
            <p><strong>Token:</strong> 123456</p>
        </div>

        <div class="form-container">
            <h3>💳 eSewa Payment Form</h3>
            <form action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST">
                ' . $formFields . '
                <button type="submit" class="btn">Pay Rs. ' . $totalAmount . ' with eSewa</button>
            </form>
        </div>

        <div class="info">
            <h3>📊 Form Data Being Sent:</h3>
            <div class="data">' . json_encode($formData, JSON_PRETTY_PRINT) . '</div>
        </div>

        <div class="info">
            <h3>🔐 Signature Verification:</h3>
            <p><strong>Signature Data:</strong> ' . $signatureData . '</p>
            <p><strong>Generated Signature:</strong> ' . $signature . '</p>
        </div>

        <div class="info">
            <h3>📝 Expected Flow:</h3>
            <ol>
                <li>Click "Pay with eSewa" button above</li>
                <li>Should redirect to eSewa login page (NOT show "Unable to fetch merchant key")</li>
                <li>Enter test credentials provided above</li>
                <li>Complete payment process</li>
                <li>Should redirect to success page</li>
            </ol>
        </div>
    </body>
    </html>';

    return response($html)->header('Content-Type', 'text/html');
})->name('debug.esewa.direct.test');

// Test time-based booking restrictions
Route::get('/test/time-restrictions/{scheduleId?}', function ($scheduleId = null) {
    if ($scheduleId) {
        $schedule = \App\Models\Schedule::find($scheduleId);
        if (!$schedule) {
            return response()->json(['error' => 'Schedule not found'], 404);
        }

        return response()->json([
            'schedule_id' => $schedule->id,
            'travel_date' => $schedule->travel_date,
            'departure_time' => $schedule->departure_time,
            'departure_datetime' => $schedule->departure_datetime,
            'current_time' => \Carbon\Carbon::now(),
            'minutes_until_departure' => $schedule->minutes_until_departure,
            'has_finished' => $schedule->hasFinished(),
            'is_bookable' => $schedule->isBookable(),
            'is_bookable_online' => $schedule->isBookableOnline(),
            'is_bookable_via_counter' => $schedule->isBookableViaCounter(),
            'is_in_counter_only_period' => $schedule->isInCounterOnlyPeriod(),
            'booking_status' => $schedule->booking_status,
        ], JSON_PRETTY_PRINT);
    }

    // Show all schedules with their booking status
    $schedules = \App\Models\Schedule::with(['route', 'bus'])
        ->whereDate('travel_date', '>=', \Carbon\Carbon::today())
        ->orderBy('travel_date')
        ->orderBy('departure_time')
        ->limit(10)
        ->get();

    $results = [];
    foreach ($schedules as $schedule) {
        $results[] = [
            'id' => $schedule->id,
            'route' => $schedule->route->name ?? 'N/A',
            'bus' => $schedule->bus->bus_number ?? 'N/A',
            'travel_date' => $schedule->travel_date,
            'departure_time' => $schedule->departure_time,
            'departure_datetime' => $schedule->departure_datetime,
            'minutes_until_departure' => $schedule->minutes_until_departure,
            'has_finished' => $schedule->hasFinished(),
            'is_bookable_online' => $schedule->isBookableOnline(),
            'is_bookable_via_counter' => $schedule->isBookableViaCounter(),
            'booking_status' => $schedule->booking_status,
        ];
    }

    return response()->json([
        'current_time' => \Carbon\Carbon::now(),
        'schedules' => $results,
    ], JSON_PRETTY_PRINT);
});

// Create test schedules for time restriction testing
Route::get('/test/create-time-test-schedules', function () {
    $bus = \App\Models\Bus::first();
    $route = \App\Models\Route::first();
    $operator = \App\Models\User::role('operator')->first();

    if (!$bus || !$route || !$operator) {
        return response()->json(['error' => 'Missing required data (bus, route, or operator)'], 400);
    }

    $now = \Carbon\Carbon::now();
    $today = $now->format('Y-m-d');

    $testSchedules = [
        [
            'name' => 'Already Departed (30 minutes ago)',
            'departure_time' => $now->copy()->subMinutes(30)->format('H:i:s'),
        ],
        [
            'name' => 'Departing in 5 minutes (Counter only)',
            'departure_time' => $now->copy()->addMinutes(5)->format('H:i:s'),
        ],
        [
            'name' => 'Departing in 15 minutes (Online booking available)',
            'departure_time' => $now->copy()->addMinutes(15)->format('H:i:s'),
        ],
        [
            'name' => 'Departing in 2 hours (Online booking available)',
            'departure_time' => $now->copy()->addHours(2)->format('H:i:s'),
        ],
    ];

    $created = [];

    foreach ($testSchedules as $testSchedule) {
        $schedule = \App\Models\Schedule::create([
            'bus_id' => $bus->id,
            'route_id' => $route->id,
            'operator_id' => $operator->id,
            'travel_date' => $today,
            'departure_time' => $testSchedule['departure_time'],
            'arrival_time' => \Carbon\Carbon::parse($testSchedule['departure_time'])->addHours(3)->format('H:i:s'),
            'fare' => 500,
            'available_seats' => $bus->total_seats,
            'status' => 'scheduled',
            'notes' => 'Test schedule: ' . $testSchedule['name'],
        ]);

        $created[] = [
            'id' => $schedule->id,
            'name' => $testSchedule['name'],
            'departure_time' => $testSchedule['departure_time'],
            'departure_datetime' => $schedule->departure_datetime,
            'minutes_until_departure' => $schedule->minutes_until_departure,
            'has_finished' => $schedule->hasFinished(),
            'is_bookable_online' => $schedule->isBookableOnline(),
            'is_bookable_via_counter' => $schedule->isBookableViaCounter(),
            'booking_status' => $schedule->booking_status,
        ];
    }

    return response()->json([
        'message' => 'Test schedules created successfully',
        'current_time' => $now,
        'created_schedules' => $created,
    ], JSON_PRETTY_PRINT);
});


// Test complete time-based booking system
Route::get('/test/complete-time-system', function () {
    $now = \Carbon\Carbon::now();

    // Get schedules with different time scenarios
    $schedules = \App\Models\Schedule::with(['route', 'bus'])
        ->whereDate('travel_date', $now->format('Y-m-d'))
        ->orderBy('departure_time')
        ->get();

    $results = [
        'current_time' => $now,
        'summary' => [
            'total_schedules' => $schedules->count(),
            'finished_schedules' => $schedules->where('has_finished', true)->count(),
            'online_bookable' => $schedules->where('is_bookable_online', true)->count(),
            'counter_only' => $schedules->where('is_in_counter_only_period', true)->count(),
            'not_bookable' => $schedules->where('booking_status', 'not_available')->count(),
        ],
        'schedules_by_status' => [],
    ];

    foreach ($schedules as $schedule) {
        $status = $schedule->booking_status;
        if (!isset($results['schedules_by_status'][$status])) {
            $results['schedules_by_status'][$status] = [];
        }

        $results['schedules_by_status'][$status][] = [
            'id' => $schedule->id,
            'route' => $schedule->route->name ?? 'N/A',
            'departure_time' => $schedule->departure_time,
            'minutes_until_departure' => $schedule->minutes_until_departure,
            'available_seats' => $schedule->available_seats,
        ];
    }

    return response()->json($results, JSON_PRETTY_PRINT);
});
