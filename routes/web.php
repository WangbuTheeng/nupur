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

// Khalti Payment Callback Routes (must be outside auth middleware)
Route::get('/payment/khalti/success', [App\Http\Controllers\PaymentController::class, 'khaltiSuccess'])->name('payment.khalti.success');
Route::get('/payment/khalti/failure', [App\Http\Controllers\PaymentController::class, 'khaltiFailure'])->name('payment.khalti.failure');

// Test Payment Completion Route (for bypassing eSewa issues) - TEMPORARILY ENABLED
Route::get('/payment/test-complete/{booking}', [App\Http\Controllers\PaymentController::class, 'testComplete'])->name('payment.test.complete');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Payment Routes (require authentication)
    Route::get('/payment/{booking}/options', [App\Http\Controllers\PaymentController::class, 'showPaymentOptions'])->name('payment.options');
    Route::post('/payment/{booking}/esewa', [App\Http\Controllers\PaymentController::class, 'initiateEsewaPayment'])->name('payment.esewa.initiate');
    Route::post('/payment/{booking}/khalti', [App\Http\Controllers\PaymentController::class, 'initiateKhaltiPayment'])->name('payment.khalti.initiate');
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

// Test Khalti payment integration
Route::get('/debug/khalti-test', function () {
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

    // Create a test booking for Khalti
    $booking = \App\Models\Booking::create([
        'user_id' => $user->id,
        'schedule_id' => $schedule->id,
        'seat_numbers' => ['1', '2'],
        'passenger_count' => 2,
        'total_amount' => 500.00, // Rs. 500 (within test limit of Rs. 999)
        'status' => 'pending',
        'payment_status' => 'pending',
        'booking_reference' => 'KHALTI-TEST-' . time(),
        'passenger_details' => json_encode([
            'name' => 'Test User',
            'phone' => '9800000000',
            'email' => 'test@khalti.com'
        ]),
        'contact_phone' => '9800000000',
        'contact_email' => 'test@khalti.com',
        'booking_type' => 'online'
    ]);

    return response()->json([
        'booking_created' => [
            'id' => $booking->id,
            'reference' => $booking->booking_reference,
            'amount' => $booking->total_amount,
            'seats' => $booking->seat_numbers,
            'passenger_count' => $booking->passenger_count
        ],
        'payment_test_url' => route('payment.options', $booking->id),
        'khalti_test_credentials' => [
            'khalti_id' => '9800000000 to 9800000005',
            'mpin' => '1111',
            'otp' => '987654'
        ],
        'instructions' => [
            '1. Visit the payment test URL above',
            '2. Click "Khalti" payment option',
            '3. You should be redirected to Khalti payment page',
            '4. Use test credentials provided above',
            '5. Complete the payment to test the full flow'
        ],
        'test_info' => [
            'environment' => 'Sandbox',
            'max_amount' => 'Rs. 999 (without contract)',
            'api_endpoint' => 'https://dev.khalti.com/api/v2',
            'documentation' => 'https://docs.khalti.com/khalti-epayment/'
        ]
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.khalti.test');

// Test Khalti API configuration
Route::get('/debug/khalti-config-test', function () {
    $khaltiService = app(\App\Services\KhaltiPaymentService::class);

    // Test configuration
    $config = [
        'secret_key' => config('services.khalti.secret_key'),
        'base_url' => config('services.khalti.base_url'),
        'success_url' => config('services.khalti.success_url'),
        'failure_url' => config('services.khalti.failure_url')
    ];

    // Test API connectivity
    try {
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Key ' . $config['secret_key'],
            'Content-Type' => 'application/json'
        ])->timeout(10)->post($config['base_url'] . '/epayment/initiate/', [
            'return_url' => 'https://example.com/return',
            'website_url' => 'https://example.com',
            'amount' => 1000, // Rs. 10 in paisa
            'purchase_order_id' => 'TEST-' . time(),
            'purchase_order_name' => 'Test Payment',
            'customer_info' => [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'phone' => '9800000000'
            ]
        ]);

        $apiTest = [
            'status' => $response->successful() ? 'SUCCESS' : 'FAILED',
            'status_code' => $response->status(),
            'response' => $response->json()
        ];
    } catch (\Exception $e) {
        $apiTest = [
            'status' => 'ERROR',
            'error' => $e->getMessage()
        ];
    }

    return response()->json([
        'khalti_configuration' => $config,
        'api_connectivity_test' => $apiTest,
        'test_credentials' => [
            'khalti_id' => '9800000000 to 9800000005',
            'mpin' => '1111',
            'otp' => '987654'
        ],
        'documentation' => 'https://docs.khalti.com/khalti-epayment/',
        'status' => $apiTest['status'] === 'SUCCESS' ? 'Khalti integration is working!' : 'Khalti integration needs attention'
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.khalti.config.test');

// Test Khalti callback URLs
Route::get('/debug/khalti-callback-test', function () {
    // Create a test payment record for callback testing
    $user = \App\Models\User::first();
    $booking = \App\Models\Booking::first();

    if (!$user || !$booking) {
        return response()->json(['error' => 'Need at least one user and booking for testing'], 404);
    }

    $payment = \App\Models\Payment::create([
        'booking_id' => $booking->id,
        'user_id' => $user->id,
        'payment_method' => 'khalti',
        'amount' => 500.00,
        'currency' => 'NPR',
        'status' => 'pending',
        'transaction_id' => 'TEST-KHALTI-' . time(),
        'gateway_data' => [
            'test' => true,
            'pidx' => 'test_pidx_' . time()
        ]
    ]);

    $successUrl = route('payment.khalti.success') . '?payment_id=' . $payment->id . '&pidx=test_pidx_123&status=Completed&transaction_id=test_txn_123';
    $failureUrl = route('payment.khalti.failure') . '?payment_id=' . $payment->id . '&status=User canceled';

    return response()->json([
        'test_payment_created' => [
            'id' => $payment->id,
            'transaction_id' => $payment->transaction_id,
            'amount' => $payment->amount,
            'status' => $payment->status
        ],
        'callback_urls' => [
            'success_url' => $successUrl,
            'failure_url' => $failureUrl
        ],
        'test_instructions' => [
            '1. Click on the success_url to test successful payment callback',
            '2. Click on the failure_url to test failed payment callback',
            '3. Check if the callbacks work without route parameter errors'
        ],
        'khalti_callback_format' => [
            'success_parameters' => 'pidx, status, transaction_id, amount',
            'failure_parameters' => 'pidx, status',
            'note' => 'payment_id is passed as query parameter in return_url'
        ]
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.khalti.callback.test');

// Complete Khalti integration test summary
Route::get('/debug/khalti-integration-summary', function () {
    return response()->json([
        'khalti_integration_status' => '✅ COMPLETE',
        'implementation_summary' => [
            'service' => 'KhaltiPaymentService - Full API integration',
            'controller' => 'PaymentController - Initiation and callbacks',
            'routes' => 'Authenticated initiation + Public callbacks',
            'ui' => 'Payment options page updated',
            'configuration' => 'Test credentials configured'
        ],
        'test_urls' => [
            'config_test' => route('debug.khalti.config.test'),
            'payment_flow_test' => route('debug.khalti.test'),
            'callback_test' => route('debug.khalti.callback.test')
        ],
        'test_credentials' => [
            'khalti_id' => '9800000000 to 9800000005',
            'mpin' => '1111',
            'otp' => '987654',
            'max_amount' => 'Rs. 999 (sandbox limit)'
        ],
        'api_endpoints' => [
            'initiate' => 'https://dev.khalti.com/api/v2/epayment/initiate/',
            'lookup' => 'https://dev.khalti.com/api/v2/epayment/lookup/',
            'environment' => 'Sandbox/Test'
        ],
        'callback_urls' => [
            'success' => route('payment.khalti.success'),
            'failure' => route('payment.khalti.failure'),
            'format' => 'Uses query parameters: ?payment_id=X&pidx=Y&status=Z'
        ],
        'next_steps' => [
            '1. Test payment flow using the test URLs above',
            '2. Use provided test credentials for Khalti login',
            '3. Verify success/failure callbacks work correctly',
            '4. For production: Get live credentials from Khalti merchant dashboard'
        ],
        'documentation' => 'https://docs.khalti.com/khalti-epayment/',
        'status' => '🎉 Ready for testing!'
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.khalti.integration.summary');

// Debug Khalti payment initiation step by step
Route::get('/debug/khalti-payment-debug/{booking?}', function ($bookingId = null) {
    try {
        // Get or create a test booking
        if ($bookingId) {
            $booking = \App\Models\Booking::findOrFail($bookingId);
        } else {
            // Create a test booking
            $user = \App\Models\User::first();
            $schedule = \App\Models\Schedule::with(['route', 'bus'])
                ->where('travel_date', '>=', now()->toDateString())
                ->first();

            if (!$user || !$schedule) {
                return response()->json(['error' => 'Need user and schedule for testing'], 404);
            }

            $booking = \App\Models\Booking::create([
                'user_id' => $user->id,
                'schedule_id' => $schedule->id,
                'seat_numbers' => ['1', '2'],
                'passenger_count' => 2,
                'total_amount' => 500.00,
                'status' => 'pending',
                'payment_status' => 'pending',
                'booking_reference' => 'DEBUG-KHALTI-' . time(),
                'passenger_details' => json_encode([
                    'name' => 'Debug User',
                    'phone' => '9800000000',
                    'email' => 'debug@khalti.com'
                ]),
                'contact_phone' => '9800000000',
                'contact_email' => 'debug@khalti.com',
                'booking_type' => 'online'
            ]);
        }

        // Test the KhaltiPaymentService directly
        $khaltiService = app(\App\Services\KhaltiPaymentService::class);

        // Step 1: Test service instantiation
        $debug = [
            'step_1_service_created' => 'SUCCESS',
            'booking_info' => [
                'id' => $booking->id,
                'amount' => $booking->total_amount,
                'reference' => $booking->booking_reference
            ]
        ];

        // Step 2: Test payment initiation
        $result = $khaltiService->initiatePayment($booking);

        $debug['step_2_payment_initiation'] = [
            'success' => $result['success'],
            'result' => $result
        ];

        if ($result['success']) {
            $debug['step_3_khalti_response'] = [
                'payment_url' => $result['payment_url'],
                'pidx' => $result['pidx'],
                'expires_at' => $result['expires_at']
            ];

            $debug['next_step'] = 'Visit payment_url to complete payment';
            $debug['test_payment_url'] = $result['payment_url'];
        } else {
            $debug['step_3_error'] = $result;
        }

        return response()->json($debug, 200, [], JSON_PRETTY_PRINT);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Debug failed',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500, [], JSON_PRETTY_PRINT);
    }
})->name('debug.khalti.payment.debug');

// Test actual payment flow simulation
Route::get('/debug/khalti-flow-test', function () {
    // Create a test booking and simulate the exact flow
    $user = \App\Models\User::first();
    $schedule = \App\Models\Schedule::with(['route', 'bus'])
        ->where('travel_date', '>=', now()->toDateString())
        ->first();

    if (!$user || !$schedule) {
        return response()->json(['error' => 'Need user and schedule for testing'], 404);
    }

    $booking = \App\Models\Booking::create([
        'user_id' => $user->id,
        'schedule_id' => $schedule->id,
        'seat_numbers' => ['1', '2'],
        'passenger_count' => 2,
        'total_amount' => 500.00,
        'status' => 'pending',
        'payment_status' => 'pending',
        'booking_reference' => 'FLOW-TEST-' . time(),
        'passenger_details' => json_encode([
            'name' => 'Flow Test User',
            'phone' => '9800000000',
            'email' => 'flowtest@khalti.com'
        ]),
        'contact_phone' => '9800000000',
        'contact_email' => 'flowtest@khalti.com',
        'booking_type' => 'online'
    ]);

    return response()->json([
        'booking_created' => [
            'id' => $booking->id,
            'reference' => $booking->booking_reference,
            'amount' => $booking->total_amount
        ],
        'test_urls' => [
            'payment_options' => route('payment.options', $booking->id),
            'direct_khalti_initiate' => route('payment.khalti.initiate', $booking->id)
        ],
        'instructions' => [
            '1. Visit payment_options URL',
            '2. Click Khalti payment button',
            '3. Check if it redirects to Khalti or shows success immediately',
            '4. If it shows success immediately, there is a routing/controller issue'
        ]
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.khalti.flow.test');

// Test which payment system is being used
Route::get('/debug/payment-system-check', function () {
    $user = \App\Models\User::first();
    $schedule = \App\Models\Schedule::first();

    if (!$user || !$schedule) {
        return response()->json(['error' => 'Need user and schedule'], 404);
    }

    $booking = \App\Models\Booking::create([
        'user_id' => $user->id,
        'schedule_id' => $schedule->id,
        'seat_numbers' => ['1'],
        'passenger_count' => 1,
        'total_amount' => 300.00,
        'status' => 'pending',
        'payment_status' => 'pending',
        'booking_reference' => 'SYSTEM-CHECK-' . time(),
        'passenger_details' => json_encode(['name' => 'System Check']),
        'contact_phone' => '9800000000',
        'contact_email' => 'check@test.com',
        'booking_type' => 'online'
    ]);

    return response()->json([
        'booking_created' => $booking->id,
        'available_payment_routes' => [
            'main_payment_options' => route('payment.options', $booking->id),
            'main_khalti_initiate' => route('payment.khalti.initiate', $booking->id),
            'customer_payment_index' => url('/customer/payment/' . $booking->id),
        ],
        'route_analysis' => [
            'main_system' => 'App\\Http\\Controllers\\PaymentController (Real Khalti)',
            'customer_system' => 'App\\Http\\Controllers\\Customer\\PaymentController (Now Fixed)',
        ],
        'test_instructions' => [
            '1. Visit main_payment_options to use the main payment system',
            '2. Visit customer_payment_index to use the customer payment system',
            '3. Both should now use real Khalti integration (simulation removed)',
            '4. Check which one you are actually using in your app'
        ]
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.payment.system.check');

// Khalti credentials setup guide
Route::get('/debug/khalti-credentials-guide', function () {
    return response()->json([
        'issue' => 'Invalid token error means the Khalti API credentials are incorrect',
        'solution' => 'You need to get real test credentials from Khalti merchant dashboard',
        'steps_to_get_credentials' => [
            '1. Visit: https://test-admin.khalti.com/#/join/merchant',
            '2. Sign up for a merchant account (use 987654 as OTP)',
            '3. Complete the merchant registration',
            '4. Go to "Keys" section in merchant dashboard',
            '5. Copy the "Live Secret Key" and "Live Public Key"',
            '6. Update your .env file with these credentials'
        ],
        'env_file_update' => [
            'KHALTI_PUBLIC_KEY=your_actual_public_key_from_dashboard',
            'KHALTI_SECRET_KEY=your_actual_secret_key_from_dashboard',
            'KHALTI_BASE_URL=https://dev.khalti.com/api/v2'
        ],
        'current_credentials_status' => 'INVALID - Using dummy credentials',
        'current_config' => [
            'public_key' => config('services.khalti.public_key'),
            'secret_key' => config('services.khalti.secret_key'),
            'base_url' => config('services.khalti.base_url')
        ],
        'test_user_credentials' => [
            'khalti_id' => '9800000000 to 9800000005',
            'mpin' => '1111',
            'otp' => '987654'
        ],
        'important_notes' => [
            'The credentials in config are dummy/example credentials',
            'You must get real credentials from Khalti merchant dashboard',
            'Without real credentials, you will get "Invalid token" error',
            'After getting credentials, payment will work properly'
        ],
        'alternative_solution' => 'Create a payment simulator for testing without real credentials'
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.khalti.credentials.guide');

// Khalti Payment Simulator (for testing without real credentials)
Route::get('/khalti-simulator/{payment_id}', function ($paymentId) {
    try {
        $payment = \App\Models\Payment::findOrFail($paymentId);
        $booking = $payment->booking;

        return view('payment.khalti-simulator', [
            'payment' => $payment,
            'booking' => $booking,
            'amount' => $payment->amount,
            'pidx' => 'SIM_' . time() . '_' . $payment->id
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Payment not found'], 404);
    }
})->name('khalti.simulator');

// Handle Khalti simulator payment completion
Route::post('/khalti-simulator/complete/{payment_id}', function ($paymentId) {
    try {
        $payment = \App\Models\Payment::findOrFail($paymentId);
        $booking = $payment->booking;

        // Simulate successful payment
        $payment->update([
            'status' => 'completed',
            'gateway_transaction_id' => 'SIM_TXN_' . time(),
            'gateway_response' => [
                'simulated' => true,
                'status' => 'Completed',
                'transaction_id' => 'SIM_TXN_' . time(),
                'amount' => $payment->amount * 100 // in paisa
            ],
            'paid_at' => now(),
        ]);

        // Update booking
        $booking->update([
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        // Redirect to success page
        $successUrl = route('payment.khalti.success') . '?payment_id=' . $payment->id . '&pidx=SIM_' . time() . '&status=Completed&transaction_id=SIM_TXN_' . time();
        return redirect($successUrl);

    } catch (\Exception $e) {
        return response()->json(['error' => 'Payment completion failed'], 500);
    }
})->name('khalti.simulator.complete');

// Debug Khalti callback processing
Route::get('/debug/khalti-callback-debug', function () {
    // Create a test payment for callback debugging
    $user = \App\Models\User::first();
    $booking = \App\Models\Booking::first();

    if (!$user || !$booking) {
        return response()->json(['error' => 'Need user and booking'], 404);
    }

    $payment = \App\Models\Payment::create([
        'booking_id' => $booking->id,
        'user_id' => $user->id,
        'payment_method' => 'khalti',
        'amount' => 500.00,
        'currency' => 'NPR',
        'status' => 'pending',
        'transaction_id' => 'DEBUG-KHALTI-' . time(),
        'gateway_data' => [
            'test' => true,
            'pidx' => 'SIM_' . time()
        ]
    ]);

    $pidx = 'SIM_' . time();
    $transactionId = 'SIM_TXN_' . time();

    $successUrl = route('payment.khalti.success') . '?' . http_build_query([
        'payment_id' => $payment->id,
        'pidx' => $pidx,
        'status' => 'Completed',
        'transaction_id' => $transactionId,
        'amount' => 50000, // Rs. 500 in paisa
        'total_amount' => 50000
    ]);

    $failureUrl = route('payment.khalti.failure') . '?' . http_build_query([
        'payment_id' => $payment->id,
        'pidx' => $pidx,
        'status' => 'User canceled'
    ]);

    return response()->json([
        'debug_payment_created' => [
            'id' => $payment->id,
            'amount' => $payment->amount,
            'status' => $payment->status
        ],
        'test_callback_urls' => [
            'success_callback' => $successUrl,
            'failure_callback' => $failureUrl
        ],
        'instructions' => [
            '1. Click success_callback to test successful payment verification',
            '2. Click failure_callback to test failed payment',
            '3. Check if simulated payment verification works correctly'
        ],
        'expected_result' => 'Success callback should show payment success page'
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.khalti.callback.debug');

// Test Khalti verification directly
Route::get('/debug/khalti-verify-test/{payment_id}/{pidx}', function ($paymentId, $pidx) {
    try {
        $khaltiService = app(\App\Services\KhaltiPaymentService::class);

        $result = $khaltiService->verifyPayment($paymentId, $pidx);

        return response()->json([
            'verification_result' => $result,
            'payment_id' => $paymentId,
            'pidx' => $pidx,
            'is_simulated' => strpos($pidx, 'SIM_') === 0,
            'test_status' => $result['success'] ? 'SUCCESS' : 'FAILED'
        ], 200, [], JSON_PRETTY_PRINT);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Verification test failed',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500, [], JSON_PRETTY_PRINT);
    }
})->name('debug.khalti.verify.test');

// Debug specific payment issue
Route::get('/debug/payment-check/{payment_id}', function ($paymentId) {
    try {
        $payment = \App\Models\Payment::find($paymentId);

        if (!$payment) {
            return response()->json([
                'error' => 'Payment not found',
                'payment_id' => $paymentId,
                'suggestion' => 'This payment ID does not exist in the database'
            ], 404, [], JSON_PRETTY_PRINT);
        }

        $booking = $payment->booking;

        return response()->json([
            'payment_found' => true,
            'payment_details' => [
                'id' => $payment->id,
                'status' => $payment->status,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'transaction_id' => $payment->transaction_id,
                'gateway_data' => $payment->gateway_data,
                'created_at' => $payment->created_at,
                'updated_at' => $payment->updated_at
            ],
            'booking_details' => [
                'id' => $booking->id,
                'status' => $booking->status,
                'payment_status' => $booking->payment_status,
                'booking_reference' => $booking->booking_reference,
                'total_amount' => $booking->total_amount
            ],
            'test_verification_url' => route('debug.khalti.verify.test', [$payment->id, 'SIM_' . time()]),
            'status' => 'Payment exists and can be processed'
        ], 200, [], JSON_PRETTY_PRINT);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Payment check failed',
            'message' => $e->getMessage()
        ], 500, [], JSON_PRETTY_PRINT);
    }
})->name('debug.payment.check');

// Reproduce the exact error scenario
Route::get('/debug/reproduce-khalti-error', function () {
    // Create a test payment that matches the failing scenario
    $user = \App\Models\User::first();
    $booking = \App\Models\Booking::first();

    if (!$user || !$booking) {
        return response()->json(['error' => 'Need user and booking'], 404);
    }

    $payment = \App\Models\Payment::create([
        'booking_id' => $booking->id,
        'user_id' => $user->id,
        'payment_method' => 'khalti',
        'amount' => 500.00,
        'currency' => 'NPR',
        'status' => 'pending',
        'transaction_id' => 'REPRODUCE-TEST-' . time(),
        'gateway_data' => [
            'test' => true,
            'pidx' => 'SIM_' . time()
        ]
    ]);

    // Create the exact URL that was failing
    $failingUrl = route('payment.khalti.success') . '?' . http_build_query([
        'payment_id' => $payment->id,
        'pidx' => 'SIM_17526700828',
        'status' => 'Completed',
        'transaction_id' => 'SIM_TXN_17526700828'
    ]);

    return response()->json([
        'test_payment_created' => [
            'id' => $payment->id,
            'status' => $payment->status,
            'amount' => $payment->amount
        ],
        'failing_scenario_url' => $failingUrl,
        'verification_test_url' => route('debug.khalti.verify.test', [$payment->id, 'SIM_17526700828']),
        'instructions' => [
            '1. Click failing_scenario_url to reproduce the exact error',
            '2. Click verification_test_url to test verification directly',
            '3. Check what specific error occurs'
        ],
        'expected_fix' => 'Should now work with simulated payment verification'
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.reproduce.khalti.error');

// Test booking success view
Route::get('/debug/test-booking-success', function () {
    $booking = \App\Models\Booking::with(['schedule.route.sourceCity', 'schedule.route.destinationCity', 'schedule.bus', 'schedule.operator'])->first();

    if (!$booking) {
        return response()->json(['error' => 'No bookings found for testing'], 404);
    }

    return response()->json([
        'booking_found' => true,
        'booking_id' => $booking->id,
        'booking_reference' => $booking->booking_reference,
        'test_urls' => [
            'booking_success_view' => route('booking.success', $booking->id),
            'payment_success_view' => route('payment.success', $booking->id),
            'customer_booking_show' => route('customer.bookings.show', $booking->id)
        ],
        'view_status' => [
            'customer.booking.success' => 'NOW CREATED ✅',
            'customer.payment.success' => 'EXISTS ✅',
            'payment.simple-success' => 'EXISTS ✅'
        ],
        'instructions' => [
            '1. Click booking_success_view to test the newly created view',
            '2. All views should now work without "View not found" errors'
        ]
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.test.booking.success');

// Debug payment status issue
Route::get('/debug/payment-status-check/{booking_id}', function ($bookingId) {
    try {
        $booking = \App\Models\Booking::with(['payments'])->findOrFail($bookingId);

        $payments = $booking->payments()->orderBy('created_at', 'desc')->get();

        $paymentDetails = $payments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'status' => $payment->status,
                'payment_method' => $payment->payment_method,
                'amount' => $payment->amount,
                'gateway_transaction_id' => $payment->gateway_transaction_id,
                'gateway_data' => $payment->gateway_data,
                'gateway_response' => $payment->gateway_response,
                'paid_at' => $payment->paid_at,
                'created_at' => $payment->created_at,
                'updated_at' => $payment->updated_at
            ];
        });

        return response()->json([
            'booking_details' => [
                'id' => $booking->id,
                'booking_reference' => $booking->booking_reference,
                'status' => $booking->status,
                'payment_status' => $booking->payment_status,
                'total_amount' => $booking->total_amount,
                'payment_completed_at' => $booking->payment_completed_at,
                'created_at' => $booking->created_at,
                'updated_at' => $booking->updated_at
            ],
            'payment_records' => $paymentDetails,
            'payment_count' => $payments->count(),
            'latest_payment' => $payments->first() ? [
                'id' => $payments->first()->id,
                'status' => $payments->first()->status,
                'paid_at' => $payments->first()->paid_at
            ] : null,
            'diagnosis' => [
                'booking_payment_status' => $booking->payment_status,
                'latest_payment_status' => $payments->first()->status ?? 'No payments',
                'issue' => $booking->payment_status !== 'paid' ? 'Booking payment status not updated' : 'Payment status is correct',
                'solution' => $booking->payment_status !== 'paid' ? 'Check if handleSuccessfulPayment was called' : 'No issues found'
            ]
        ], 200, [], JSON_PRETTY_PRINT);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Payment status check failed',
            'message' => $e->getMessage()
        ], 500, [], JSON_PRETTY_PRINT);
    }
})->name('debug.payment.status.check');

// Fix payment status manually (for testing)
Route::get('/debug/fix-payment-status/{booking_id}', function ($bookingId) {
    try {
        $booking = \App\Models\Booking::with(['payments'])->findOrFail($bookingId);

        $latestPayment = $booking->payments()->orderBy('created_at', 'desc')->first();

        if (!$latestPayment) {
            return response()->json(['error' => 'No payments found for this booking'], 404);
        }

        $beforeStatus = [
            'booking_status' => $booking->status,
            'booking_payment_status' => $booking->payment_status,
            'payment_status' => $latestPayment->status,
            'payment_paid_at' => $latestPayment->paid_at
        ];

        // Check if payment is completed but booking is not updated
        if ($latestPayment->status === 'completed' && $booking->payment_status !== 'paid') {
            // Update booking status
            $booking->update([
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'payment_completed_at' => $latestPayment->paid_at ?? now()
            ]);

            $afterStatus = [
                'booking_status' => $booking->fresh()->status,
                'booking_payment_status' => $booking->fresh()->payment_status,
                'payment_status' => $latestPayment->status,
                'payment_paid_at' => $latestPayment->paid_at
            ];

            return response()->json([
                'action' => 'FIXED',
                'booking_id' => $booking->id,
                'before' => $beforeStatus,
                'after' => $afterStatus,
                'message' => 'Booking payment status has been updated to match completed payment',
                'test_urls' => [
                    'booking_details' => route('customer.bookings.show', $booking->id),
                    'payment_options' => route('payment.options', $booking->id)
                ]
            ], 200, [], JSON_PRETTY_PRINT);
        } else {
            return response()->json([
                'action' => 'NO_FIX_NEEDED',
                'booking_id' => $booking->id,
                'current_status' => $beforeStatus,
                'message' => 'Payment and booking statuses are already correct',
                'test_urls' => [
                    'booking_details' => route('customer.bookings.show', $booking->id),
                    'payment_options' => route('payment.options', $booking->id)
                ]
            ], 200, [], JSON_PRETTY_PRINT);
        }

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Fix payment status failed',
            'message' => $e->getMessage()
        ], 500, [], JSON_PRETTY_PRINT);
    }
})->name('debug.fix.payment.status');

// Find bookings with payment issues
Route::get('/debug/find-payment-issues', function () {
    try {
        // Find bookings with completed payments but pending payment status
        $problematicBookings = \App\Models\Booking::with(['payments'])
            ->where('payment_status', '!=', 'paid')
            ->whereHas('payments', function ($query) {
                $query->where('status', 'completed');
            })
            ->get();

        $issues = $problematicBookings->map(function ($booking) {
            $completedPayments = $booking->payments()->where('status', 'completed')->get();

            return [
                'booking_id' => $booking->id,
                'booking_reference' => $booking->booking_reference,
                'booking_status' => $booking->status,
                'booking_payment_status' => $booking->payment_status,
                'total_amount' => $booking->total_amount,
                'completed_payments_count' => $completedPayments->count(),
                'latest_completed_payment' => $completedPayments->first() ? [
                    'id' => $completedPayments->first()->id,
                    'amount' => $completedPayments->first()->amount,
                    'payment_method' => $completedPayments->first()->payment_method,
                    'paid_at' => $completedPayments->first()->paid_at,
                    'gateway_transaction_id' => $completedPayments->first()->gateway_transaction_id
                ] : null,
                'fix_url' => route('debug.fix.payment.status', $booking->id),
                'check_url' => route('debug.payment.status.check', $booking->id)
            ];
        });

        return response()->json([
            'total_issues_found' => $issues->count(),
            'problematic_bookings' => $issues,
            'summary' => [
                'issue_description' => 'Bookings with completed payments but payment_status != paid',
                'likely_cause' => 'handleSuccessfulPayment method not called or failed',
                'solution' => 'Use fix_url for each booking to manually update status'
            ],
            'bulk_fix_instructions' => [
                '1. Review each booking in the list above',
                '2. Click fix_url for each booking to update status',
                '3. Verify the booking shows as paid after fix'
            ]
        ], 200, [], JSON_PRETTY_PRINT);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Find payment issues failed',
            'message' => $e->getMessage()
        ], 500, [], JSON_PRETTY_PRINT);
    }
})->name('debug.find.payment.issues');

// Test complete Khalti payment flow with status updates
Route::get('/debug/test-complete-khalti-flow', function () {
    try {
        // Create a fresh test booking
        $user = \App\Models\User::first();
        $schedule = \App\Models\Schedule::with(['route', 'bus'])->first();

        if (!$user || !$schedule) {
            return response()->json(['error' => 'Need user and schedule'], 404);
        }

        $booking = \App\Models\Booking::create([
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'seat_numbers' => ['1', '2'],
            'passenger_count' => 2,
            'total_amount' => 500.00,
            'status' => 'pending',
            'payment_status' => 'pending',
            'booking_reference' => 'COMPLETE-FLOW-' . time(),
            'passenger_details' => json_encode([
                'name' => 'Complete Flow Test',
                'phone' => '9800000000',
                'email' => 'completeflow@test.com'
            ]),
            'contact_phone' => '9800000000',
            'contact_email' => 'completeflow@test.com',
            'booking_type' => 'online'
        ]);

        // Test the complete flow
        $khaltiService = app(\App\Services\KhaltiPaymentService::class);

        // Step 1: Initiate payment
        $initiateResult = $khaltiService->initiatePayment($booking);

        if (!$initiateResult['success']) {
            return response()->json([
                'error' => 'Payment initiation failed',
                'result' => $initiateResult
            ], 500);
        }

        // Get the payment record
        $payment = \App\Models\Payment::where('booking_id', $booking->id)->latest()->first();

        // Step 2: Simulate payment completion (like what happens in simulator)
        $pidx = 'SIM_' . time();
        $transactionId = 'SIM_TXN_' . time();

        // Step 3: Verify payment (this should update statuses)
        $verifyResult = $khaltiService->verifyPayment($payment->id, $pidx);

        // Step 4: Check final statuses
        $finalBooking = $booking->fresh();
        $finalPayment = $payment->fresh();

        return response()->json([
            'test_flow_complete' => true,
            'booking_created' => [
                'id' => $booking->id,
                'reference' => $booking->booking_reference,
                'initial_status' => 'pending',
                'initial_payment_status' => 'pending'
            ],
            'step_1_initiate' => [
                'success' => $initiateResult['success'],
                'payment_url' => $initiateResult['payment_url'] ?? null,
                'is_simulator' => $initiateResult['is_simulator'] ?? false
            ],
            'step_2_payment_record' => [
                'payment_id' => $payment->id,
                'initial_status' => $payment->status,
                'amount' => $payment->amount
            ],
            'step_3_verify' => [
                'success' => $verifyResult['success'],
                'message' => $verifyResult['message'],
                'status' => $verifyResult['status'] ?? null
            ],
            'step_4_final_status' => [
                'booking_status' => $finalBooking->status,
                'booking_payment_status' => $finalBooking->payment_status,
                'payment_status' => $finalPayment->status,
                'payment_paid_at' => $finalPayment->paid_at
            ],
            'test_urls' => [
                'booking_success' => route('booking.success', $finalBooking->id),
                'payment_status_check' => route('debug.payment.status.check', $finalBooking->id),
                'booking_details' => route('customer.bookings.show', $finalBooking->id)
            ],
            'status_check' => [
                'is_payment_completed' => $finalPayment->status === 'completed',
                'is_booking_paid' => $finalBooking->payment_status === 'paid',
                'is_booking_confirmed' => $finalBooking->status === 'confirmed',
                'all_statuses_correct' => $finalPayment->status === 'completed' &&
                                        $finalBooking->payment_status === 'paid' &&
                                        $finalBooking->status === 'confirmed'
            ]
        ], 200, [], JSON_PRETTY_PRINT);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Complete flow test failed',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500, [], JSON_PRETTY_PRINT);
    }
})->name('debug.test.complete.khalti.flow');

// Payment status issue summary and solutions
Route::get('/debug/payment-status-summary', function () {
    return response()->json([
        'issue_description' => 'Khalti payment successful but booking still shows "Payment Required"',
        'root_causes' => [
            '1. Payment verification callback not updating booking status',
            '2. handleSuccessfulPayment method not being called',
            '3. Database transaction issues during status update',
            '4. View showing cached/old booking data'
        ],
        'solutions_implemented' => [
            '✅ Fixed booking success view to show dynamic payment status',
            '✅ Added comprehensive debugging routes',
            '✅ Enhanced error logging in payment callbacks',
            '✅ Created manual fix routes for problematic bookings'
        ],
        'diagnostic_tools' => [
            'find_issues' => route('debug.find.payment.issues'),
            'test_complete_flow' => route('debug.test.complete.khalti.flow'),
            'check_specific_booking' => route('debug.payment.status.check', '{booking_id}'),
            'fix_specific_booking' => route('debug.fix.payment.status', '{booking_id}')
        ],
        'immediate_actions' => [
            '1. Visit find_issues URL to identify problematic bookings',
            '2. Use fix_specific_booking URL for each problematic booking',
            '3. Test new payments with test_complete_flow URL',
            '4. Verify booking success page shows correct status'
        ],
        'expected_behavior' => [
            'After successful Khalti payment:',
            '• Payment status should be "completed"',
            '• Booking payment_status should be "paid"',
            '• Booking status should be "confirmed"',
            '• Booking success page should show "Payment Completed" (green)',
            '• No "Proceed to Payment" button should be visible'
        ],
        'test_instructions' => [
            '1. Create new booking via debug/khalti-test',
            '2. Complete payment in simulator',
            '3. Check if booking success page shows correct status',
            '4. If still showing "Payment Required", use fix URL'
        ]
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.payment.status.summary');

// Test new interactive Khalti payment flow
Route::get('/debug/test-interactive-khalti', function () {
    try {
        // Create a test booking
        $user = \App\Models\User::first();
        $schedule = \App\Models\Schedule::with(['route', 'bus'])->first();

        if (!$user || !$schedule) {
            return response()->json(['error' => 'Need user and schedule'], 404);
        }

        $booking = \App\Models\Booking::create([
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'seat_numbers' => ['1', '2'],
            'passenger_count' => 2,
            'total_amount' => 750.00,
            'status' => 'pending',
            'payment_status' => 'pending',
            'booking_reference' => 'INTERACTIVE-' . time(),
            'passenger_details' => json_encode([
                'name' => 'Interactive Test User',
                'phone' => '9800000000',
                'email' => 'interactive@test.com'
            ]),
            'contact_phone' => '9800000000',
            'contact_email' => 'interactive@test.com',
            'booking_type' => 'online'
        ]);

        return response()->json([
            'interactive_flow_test' => true,
            'booking_created' => [
                'id' => $booking->id,
                'reference' => $booking->booking_reference,
                'amount' => $booking->total_amount
            ],
            'test_urls' => [
                'payment_options' => route('payment.options', $booking->id),
                'direct_khalti_initiate' => route('payment.khalti.initiate', $booking->id)
            ],
            'new_features' => [
                '✅ Interactive Khalti payment page with countdown',
                '✅ Beautiful loading animations and status updates',
                '✅ Test credentials display for simulator',
                '✅ Security notices and user guidance',
                '✅ Automatic redirect to payment success page',
                '✅ Enhanced payment success page with gateway info'
            ],
            'flow_description' => [
                '1. Click payment_options URL above',
                '2. Select Khalti payment method',
                '3. See new interactive payment page with countdown',
                '4. Auto-redirect to Khalti simulator',
                '5. Complete payment with test credentials',
                '6. Redirect to enhanced payment success page'
            ],
            'expected_improvements' => [
                'Better user experience with loading states',
                'Clear instructions and test credentials',
                'Professional payment processing interface',
                'Proper success page with transaction details'
            ]
        ], 200, [], JSON_PRETTY_PRINT);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Interactive Khalti test failed',
            'message' => $e->getMessage()
        ], 500, [], JSON_PRETTY_PRINT);
    }
})->name('debug.test.interactive.khalti');

// Summary of all Khalti improvements
Route::get('/debug/khalti-improvements-summary', function () {
    return response()->json([
        'khalti_payment_improvements' => [
            'issue_resolved' => 'After successful Khalti payment, now redirects to proper payment success page',
            'interactive_payment_page' => 'Created beautiful interactive Khalti payment page with animations',
            'enhanced_user_experience' => 'Added loading states, countdown timers, and clear instructions'
        ],
        'key_features_implemented' => [
            '✅ Interactive Khalti Payment Page' => [
                'Beautiful gradient design with Khalti branding',
                'Countdown timer before redirect',
                'Loading animations and status updates',
                'Test credentials display for simulator',
                'Security notices and user guidance',
                'Auto-redirect with smooth transitions'
            ],
            '✅ Proper Success Redirect' => [
                'Khalti success callback now redirects to payment.success route',
                'Enhanced payment success page with gateway information',
                'Transaction ID display',
                'Success message with payment method'
            ],
            '✅ Dynamic Status Updates' => [
                'Booking success page shows correct payment status',
                'Conditional action buttons based on payment status',
                'Green success indicators for paid bookings',
                'Smart information display'
            ]
        ],
        'user_flow_improvements' => [
            'before' => [
                '1. Select Khalti → Direct redirect to Khalti',
                '2. Complete payment → Generic success page',
                '3. Return to booking → Still shows "Payment Required"'
            ],
            'after' => [
                '1. Select Khalti → Interactive payment page with countdown',
                '2. Auto-redirect to Khalti with clear instructions',
                '3. Complete payment → Redirect to proper payment success page',
                '4. Return to booking → Shows "Payment Completed" status'
            ]
        ],
        'test_urls' => [
            'interactive_khalti_test' => route('debug.test.interactive.khalti'),
            'payment_status_summary' => route('debug.payment.status.summary'),
            'find_payment_issues' => route('debug.find.payment.issues'),
            'complete_flow_test' => route('debug.test.complete.khalti.flow')
        ],
        'files_created_modified' => [
            'created' => [
                'resources/views/payments/khalti-redirect.blade.php' => 'Interactive Khalti payment page'
            ],
            'modified' => [
                'app/Http/Controllers/PaymentController.php' => 'Updated success callback to redirect properly',
                'app/Http/Controllers/Customer/PaymentController.php' => 'Updated to use interactive payment page',
                'resources/views/customer/payment/success.blade.php' => 'Enhanced with gateway information',
                'resources/views/customer/booking/success.blade.php' => 'Dynamic payment status display'
            ]
        ],
        'next_steps' => [
            '1. Test the interactive Khalti flow using the test URLs above',
            '2. Verify that payments now redirect to proper success page',
            '3. Check that booking status updates correctly after payment',
            '4. Ensure all existing bookings show correct payment status'
        ]
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.khalti.improvements.summary');

// Debug seat reservation issues
Route::get('/debug/seat-reservation-issue/{schedule_id}', function ($scheduleId) {
    try {
        $schedule = \App\Models\Schedule::with(['bus', 'route'])->findOrFail($scheduleId);
        $user = \App\Models\User::first();

        if (!$user) {
            return response()->json(['error' => 'No user found for testing'], 404);
        }

        // Test seat reservation
        $testSeats = ['11']; // Try to reserve seat 11
        $reservationService = app(\App\Services\SeatReservationService::class);

        // Get current seat status
        $bookedSeats = \App\Models\Booking::where('schedule_id', $scheduleId)
            ->whereIn('status', ['confirmed', 'pending'])
            ->get()
            ->pluck('seat_numbers')
            ->flatten()
            ->unique()
            ->toArray();

        $reservedSeats = \App\Models\SeatReservation::where('schedule_id', $scheduleId)
            ->active()
            ->get()
            ->pluck('seat_numbers')
            ->flatten()
            ->unique()
            ->toArray();

        $existingReservation = \App\Models\SeatReservation::where('user_id', $user->id)
            ->where('schedule_id', $scheduleId)
            ->active()
            ->first();

        // Try to reserve seats
        $result = $reservationService->reserveSeats($user->id, $scheduleId, $testSeats, 60);

        return response()->json([
            'debug_seat_reservation' => true,
            'schedule_info' => [
                'id' => $schedule->id,
                'route' => $schedule->route->name ?? 'N/A',
                'bus' => $schedule->bus->bus_number ?? 'N/A',
                'total_seats' => $schedule->bus->total_seats ?? 'N/A',
                'available_seats' => $schedule->available_seats,
                'travel_date' => $schedule->travel_date->format('Y-m-d'),
                'departure_time' => $schedule->departure_time->format('H:i'),
                'status' => $schedule->status
            ],
            'seat_status' => [
                'booked_seats' => $bookedSeats,
                'reserved_seats' => $reservedSeats,
                'test_seats' => $testSeats,
                'seat_11_status' => [
                    'is_booked' => in_array('11', $bookedSeats),
                    'is_reserved' => in_array('11', $reservedSeats),
                    'is_available' => !in_array('11', $bookedSeats) && !in_array('11', $reservedSeats)
                ]
            ],
            'user_info' => [
                'user_id' => $user->id,
                'existing_reservation' => $existingReservation ? [
                    'id' => $existingReservation->id,
                    'seat_numbers' => $existingReservation->seat_numbers,
                    'expires_at' => $existingReservation->expires_at,
                    'status' => $existingReservation->status
                ] : null
            ],
            'reservation_attempt' => [
                'success' => $result['success'],
                'message' => $result['message'],
                'test_seats' => $testSeats
            ],
            'database_checks' => [
                'seat_reservations_table_exists' => \Schema::hasTable('seat_reservations'),
                'schedules_table_exists' => \Schema::hasTable('schedules'),
                'bookings_table_exists' => \Schema::hasTable('bookings'),
                'users_table_exists' => \Schema::hasTable('users')
            ],
            'possible_issues' => [
                'schedule_not_bookable' => !$schedule->isBookableOnline(),
                'seats_already_taken' => in_array('11', array_merge($bookedSeats, $reservedSeats)),
                'database_error' => !$result['success'] && $result['message'] === 'Failed to reserve seats. Please try again.',
                'validation_error' => !$result['success'] && strpos($result['message'], 'available') !== false
            ]
        ], 200, [], JSON_PRETTY_PRINT);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Debug seat reservation failed',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500, [], JSON_PRETTY_PRINT);
    }
})->name('debug.seat.reservation.issue');

// Debug seat reservation with actual user authentication
Route::post('/debug/test-seat-reservation/{schedule_id}', function (Illuminate\Http\Request $request, $scheduleId) {
    try {
        $schedule = \App\Models\Schedule::with(['bus', 'route'])->findOrFail($scheduleId);
        $user = \Illuminate\Support\Facades\Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Test seat reservation with actual authenticated user
        $testSeats = $request->input('seat_numbers', ['11']); // Default to seat 11
        $reservationService = app(\App\Services\SeatReservationService::class);

        // Get current seat status
        $bookedSeats = \App\Models\Booking::where('schedule_id', $scheduleId)
            ->whereIn('status', ['confirmed', 'pending'])
            ->get()
            ->pluck('seat_numbers')
            ->flatten()
            ->unique()
            ->toArray();

        $reservedSeats = \App\Models\SeatReservation::where('schedule_id', $scheduleId)
            ->active()
            ->get()
            ->pluck('seat_numbers')
            ->flatten()
            ->unique()
            ->toArray();

        $existingReservation = \App\Models\SeatReservation::where('user_id', $user->id)
            ->where('schedule_id', $scheduleId)
            ->active()
            ->first();

        // Try to reserve seats
        $result = $reservationService->reserveSeats($user->id, $scheduleId, $testSeats, 60);

        return response()->json([
            'debug_authenticated_seat_reservation' => true,
            'authenticated_user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_active' => $user->is_active,
                'role' => $user->role ?? 'customer'
            ],
            'schedule_info' => [
                'id' => $schedule->id,
                'route' => $schedule->route->name ?? 'N/A',
                'bus' => $schedule->bus->bus_number ?? 'N/A',
                'total_seats' => $schedule->bus->total_seats ?? 'N/A',
                'available_seats' => $schedule->available_seats,
                'travel_date' => $schedule->travel_date->format('Y-m-d'),
                'departure_time' => $schedule->departure_time->format('H:i'),
                'status' => $schedule->status,
                'is_bookable_online' => $schedule->isBookableOnline()
            ],
            'seat_status' => [
                'booked_seats' => $bookedSeats,
                'reserved_seats' => $reservedSeats,
                'test_seats' => $testSeats,
                'requested_seats_status' => array_map(function($seat) use ($bookedSeats, $reservedSeats) {
                    return [
                        'seat' => $seat,
                        'is_booked' => in_array($seat, $bookedSeats),
                        'is_reserved' => in_array($seat, $reservedSeats),
                        'is_available' => !in_array($seat, $bookedSeats) && !in_array($seat, $reservedSeats)
                    ];
                }, $testSeats)
            ],
            'existing_reservation' => $existingReservation ? [
                'id' => $existingReservation->id,
                'seat_numbers' => $existingReservation->seat_numbers,
                'expires_at' => $existingReservation->expires_at,
                'status' => $existingReservation->status
            ] : null,
            'reservation_attempt' => [
                'success' => $result['success'],
                'message' => $result['message'],
                'test_seats' => $testSeats
            ],
            'middleware_checks' => [
                'auth_check' => \Illuminate\Support\Facades\Auth::check(),
                'user_active' => $user->is_active ?? false,
                'csrf_token' => csrf_token()
            ]
        ], 200, [], JSON_PRETTY_PRINT);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Debug authenticated seat reservation failed',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500, [], JSON_PRETTY_PRINT);
    }
})->middleware(['auth', 'user'])->name('debug.authenticated.seat.reservation');

// Debug seat reservation test page
Route::get('/debug/seat-reservation-test', function () {
    return view('debug.seat-reservation-test');
})->middleware(['auth', 'user'])->name('debug.seat.reservation.test');
