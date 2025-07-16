# BookNGo Bus Management System - Algorithms Documentation

This document outlines the various algorithms and complex logic implementations used in the BookNGo bus management system.

## Table of Contents
1. [Seat Layout Generation Algorithm](#1-seat-layout-generation-algorithm)
2. [Seat Reservation Algorithm](#2-seat-reservation-algorithm)
3. [Payment Processing Algorithm](#3-payment-processing-algorithm)
4. [Search and Filtering Algorithm](#4-search-and-filtering-algorithm)
5. [Fare Calculation Algorithm](#5-fare-calculation-algorithm)
6. [Schedule Management Algorithm](#6-schedule-management-algorithm)
7. [Real-time Seat Status Algorithm](#7-real-time-seat-status-algorithm)
8. [Booking Confirmation Algorithm](#8-booking-confirmation-algorithm)

---

## 1. Seat Layout Generation Algorithm

**Location**: `app/Services/SeatLayoutService.php`

### Purpose
Dynamically generates bus seat layouts based on total seats and layout type (2x2, 2x1, 3x2).

### Algorithm Steps
1. **Configuration Selection**: Choose layout config based on type
2. **Seat Distribution Calculation**: 
   - Calculate regular seats vs back row seats
   - Determine number of rows needed
3. **Sequential Numbering**: Generate seats using side-grouped approach
   - Left side seats first (1, 2)
   - Then right side seats (3, 4)
   - Continue row by row
4. **Back Row Generation**: Add continuous back row seats
5. **Layout Validation**: Ensure no duplicate seat numbers

### Key Features
- **Consistent Numbering**: Seats numbered 1,2,3,4... sequentially
- **Layout Types**: Supports 2x2 (4 seats/row), 2x1 (3 seats/row), 3x2 (5 seats/row)
- **Back Row Logic**: Always 5 seats for 2x2/3x2, 4 seats for 2x1
- **Validation**: Prevents duplicate seat numbers

### Code Example
```php
public function generateSeatLayout($totalSeats, $layoutType = '2x2', $hasBackRow = true)
{
    $config = self::LAYOUT_CONFIGS[$layoutType];
    $seatsPerRow = $config['total_per_row'];
    
    if ($hasBackRow) {
        $backRowSeats = $config['back_row_seats'];
        $regularSeats = $totalSeats - $backRowSeats;
        $regularRows = ceil($regularSeats / $seatsPerRow);
    }
    
    // Generate seats using side-grouped numbering...
}
```

---

## 2. Seat Reservation Algorithm

**Location**: `app/Services/SeatReservationService.php`

### Purpose
Manages temporary seat reservations with automatic expiration and conflict resolution.

### Algorithm Steps
1. **Conflict Detection**: Check if requested seats are already booked/reserved
2. **Reservation Merging**: Merge new seats with existing user reservations
3. **Expiration Management**: Set 1-hour expiration timer
4. **Cache Updates**: Update real-time seat status cache
5. **Event Broadcasting**: Fire seat update events for real-time UI updates

### Key Features
- **Automatic Expiration**: Reservations expire after 1 hour
- **Conflict Resolution**: Prevents double-booking
- **Reservation Merging**: Allows users to add more seats to existing reservations
- **Real-time Updates**: Broadcasts seat status changes

### Code Example
```php
public function reserveSeats($userId, $scheduleId, $seatNumbers, $duration = 60)
{
    // Check for conflicts
    $conflictingSeats = $this->checkSeatConflicts($scheduleId, $seatNumbers, $userId);
    
    if (!empty($conflictingSeats)) {
        return ['success' => false, 'message' => 'Seats already taken'];
    }
    
    // Create/update reservation with expiration
    $reservation = SeatReservation::createOrUpdate($userId, $scheduleId, $seatNumbers, $expiresAt);
    
    // Fire events for real-time updates
    foreach ($newSeats as $seatNumber) {
        event(new SeatUpdated($schedule, $seatNumber, 'reserved', $userId));
    }
}
```

---

## 3. Payment Processing Algorithm

**Location**: `app/Services/EsewaPaymentServiceV3.php`

### Purpose
Intelligent payment processing with multiple fallback mechanisms for eSewa integration.

### Algorithm Steps
1. **URL Availability Check**: Test eSewa production and test URLs
2. **Primary Payment Attempt**: Try real eSewa payment with working URL
3. **Fallback Mechanism**: Use payment simulator if eSewa is unavailable
4. **Error Handling**: Comprehensive logging and error recovery
5. **Status Tracking**: Track payment status throughout process

### Key Features
- **Intelligent Fallbacks**: Automatically switches to simulator if eSewa is down
- **URL Testing**: Tests multiple eSewa endpoints for availability
- **Comprehensive Logging**: Detailed logs for debugging
- **Error Recovery**: Graceful handling of payment failures

### Code Example
```php
public function initiatePayment(Booking $booking)
{
    // Step 1: Check eSewa URL availability
    $workingUrl = $this->findWorkingEsewaUrl();
    
    if ($workingUrl) {
        // Step 2: Try real eSewa payment
        $result = $this->processRealEsewaPayment($booking, $workingUrl);
        
        if ($result['success']) {
            return $result;
        }
        
        // Fallback to simulator
        Log::warning('eSewa payment failed, falling back to simulator');
    }
    
    // Step 3: Use payment simulator
    return $this->processSimulatedPayment($booking);
}
```

---

## 4. Search and Filtering Algorithm

**Location**: `app/Http/Controllers/Customer/SearchController.php`

### Purpose
Advanced bus search with multiple filtering and sorting options.

### Algorithm Steps
1. **Route Matching**: Find routes between source and destination cities
2. **Schedule Filtering**: Filter by travel date and availability
3. **Advanced Filters**: Apply bus type, price range, departure time filters
4. **Sorting Logic**: Sort by price, departure time, duration, or rating
5. **Availability Check**: Ensure schedules have available seats

### Key Features
- **Multi-criteria Search**: Source, destination, date, passengers
- **Price Range Filtering**: Budget (<1000), Standard (1000-2000), Premium (>2000)
- **Time-based Filtering**: Morning, afternoon, evening, night
- **Dynamic Sorting**: Multiple sorting options with SQL optimization

### Code Example
```php
// Price range filtering
if ($request->filled('price_range')) {
    switch ($request->price_range) {
        case 'budget':
            $query->where('fare', '<=', 1000);
            break;
        case 'standard':
            $query->whereBetween('fare', [1000, 2000]);
            break;
        case 'premium':
            $query->where('fare', '>=', 2000);
            break;
    }
}

// Sorting algorithm
switch ($sortBy) {
    case 'price_low':
        $query->orderBy('fare', 'asc');
        break;
    case 'duration':
        $query->orderByRaw('TIME_TO_SEC(arrival_time) - TIME_TO_SEC(departure_time)');
        break;
}
```

---

## 5. Fare Calculation Algorithm

**Location**: `app/Models/Schedule.php`

### Purpose
Dynamic fare calculation based on route base fare and bus type multipliers.

### Algorithm Steps
1. **Base Fare Retrieval**: Get route's base fare
2. **Multiplier Application**: Apply bus type fare multiplier
3. **Festival Pricing**: Apply festival mode price multipliers (if enabled)
4. **Final Calculation**: Calculate total fare per seat

### Key Features
- **Dynamic Pricing**: Fare varies by bus type (economy, deluxe, VIP)
- **Festival Mode**: Automatic price increases during festivals
- **Route-based Pricing**: Different base fares for different routes

### Code Example
```php
public function getCalculatedFareAttribute()
{
    $baseFare = $this->route->base_fare;
    $multiplier = $this->bus->busType->base_fare_multiplier;
    $festivalMultiplier = Cache::get('festival_price_multiplier', 1.0);
    
    return $baseFare * $multiplier * $festivalMultiplier;
}
```

---

## 6. Schedule Management Algorithm

**Location**: `app/Models/Schedule.php`

### Purpose
Intelligent schedule status management and booking availability logic.

### Algorithm Steps
1. **Time-based Status**: Determine if schedule is active, finished, or upcoming
2. **Booking Window**: Calculate online booking cutoff (10 minutes before departure)
3. **Availability Check**: Verify seat availability and booking eligibility
4. **Status Updates**: Automatically update schedule status based on time

### Key Features
- **Automatic Status Updates**: Schedules automatically become inactive after departure
- **Booking Cutoffs**: Online booking stops 10 minutes before departure
- **Counter Booking**: Operators can book until departure time

### Code Example
```php
public function isBookableOnline()
{
    $departureDateTime = Carbon::parse($this->travel_date->format('Y-m-d') . ' ' . $this->departure_time);
    $cutoffTime = $departureDateTime->subMinutes(10);
    
    return now()->lt($cutoffTime) && $this->available_seats > 0 && $this->status === 'active';
}

public function hasFinished()
{
    $departureDateTime = Carbon::parse($this->travel_date->format('Y-m-d') . ' ' . $this->departure_time);
    return now()->gt($departureDateTime);
}
```

---

## 7. Real-time Seat Status Algorithm

**Location**: `app/Events/SeatUpdated.php`, `app/Services/SeatReservationService.php`

### Purpose
Real-time seat status updates using event broadcasting and caching.

### Algorithm Steps
1. **Event Broadcasting**: Fire seat update events on status changes
2. **Cache Management**: Update seat status cache for fast retrieval
3. **Conflict Resolution**: Handle simultaneous seat selection attempts
4. **Status Synchronization**: Keep UI synchronized across multiple users

### Key Features
- **Real-time Updates**: Instant seat status updates across all connected users
- **Cache Optimization**: Fast seat status retrieval using Redis/file cache
- **Conflict Prevention**: Prevents race conditions in seat selection

---

## 8. Booking Confirmation Algorithm

**Location**: `app/Http/Controllers/Customer/BookingController.php`

### Purpose
Secure booking confirmation with transaction management and seat allocation.

### Algorithm Steps
1. **Reservation Validation**: Verify user has valid seat reservation
2. **Availability Recheck**: Double-check seat availability before booking
3. **Transaction Management**: Use database transactions for data consistency
4. **Seat Allocation**: Convert reserved seats to booked status
5. **Notification Triggers**: Fire events for email/SMS notifications

### Key Features
- **ACID Compliance**: Database transactions ensure data consistency
- **Double Validation**: Multiple checks prevent overbooking
- **Automatic Notifications**: Triggers email and SMS notifications
- **Rollback Capability**: Automatic rollback on booking failures

### Code Example
```php
DB::beginTransaction();
try {
    // Validate reservation
    $reservation = SeatReservation::where('user_id', Auth::id())
                                ->where('schedule_id', $schedule->id)
                                ->active()
                                ->first();
    
    // Create booking
    $booking = Booking::create([...]);
    
    // Update seat availability
    $schedule->decrement('available_seats', $passengerCount);
    
    // Fire events
    foreach ($seatNumbers as $seatNumber) {
        event(new SeatUpdated($schedule, $seatNumber, 'booked', Auth::id()));
    }
    
    DB::commit();
} catch (\Exception $e) {
    DB::rollback();
    throw $e;
}
```

---

## Future Algorithm Implementations

Based on the requirements document, the following algorithms are planned for future implementation:

1. **Bus Recommendation Algorithm**: ML-based recommendations using user booking history
2. **Waiting List Algorithm**: Smart seat assignment when cancellations occur
3. **Festival Traffic Prediction**: Historical data analysis for demand forecasting
4. **Route Optimization**: Dynamic route planning based on demand patterns
5. **Dynamic Pricing Algorithm**: AI-driven pricing based on demand and competition

---

## Performance Considerations

- **Database Indexing**: Optimized queries with proper indexing on frequently searched columns
- **Caching Strategy**: Redis/file caching for seat status and frequently accessed data
- **Event Broadcasting**: Efficient real-time updates using Laravel Broadcasting
- **Transaction Management**: Proper use of database transactions for data consistency
- **Query Optimization**: Eager loading and optimized SQL queries to reduce database load
