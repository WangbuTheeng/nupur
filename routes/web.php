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
