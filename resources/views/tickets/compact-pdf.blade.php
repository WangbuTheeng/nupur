<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookNGO Compact Ticket - {{ $booking->booking_reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.2;
            color: #000;
            background: #fff;
            width: 380px;
            margin: 0;
            padding: 10px;
        }
        
        .compact-ticket {
            width: 100%;
            border: 1px solid #000;
            padding: 8px;
        }
        
        .compact-header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }
        
        .compact-title {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }
        
        .compact-subtitle {
            font-size: 10px;
            margin: 2px 0;
        }
        
        .compact-ref {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin: 5px 0;
            padding: 3px;
            border: 1px solid #000;
            background: #f0f0f0;
        }
        
        .compact-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
            font-size: 11px;
        }
        
        .compact-row.center {
            justify-content: center;
            text-align: center;
        }
        
        .compact-row.large {
            font-size: 12px;
            font-weight: bold;
        }
        
        .route-info {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin: 5px 0;
            padding: 3px;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
        }
        
        .passenger-info {
            margin: 5px 0;
            padding: 3px 0;
        }
        
        .seat-info {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            margin: 5px 0;
            padding: 3px;
            background: #f5f5f5;
            border: 1px solid #ccc;
        }
        
        .amount-section {
            border-top: 1px solid #000;
            padding-top: 3px;
            margin-top: 5px;
            font-weight: bold;
        }
        
        .footer-info {
            text-align: center;
            font-size: 9px;
            margin-top: 8px;
            padding-top: 5px;
            border-top: 1px dashed #000;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 4px;
            border: 1px solid #000;
            font-size: 10px;
            font-weight: bold;
        }
        
        .operator-info {
            font-size: 10px;
            text-align: center;
            margin: 3px 0;
        }
        
        .bus-info {
            font-size: 10px;
            margin: 2px 0;
        }
    </style>
</head>
<body>
    <div class="compact-ticket">
        <!-- Header -->
        <div class="compact-header">
            <div class="compact-title">BookNGO</div>
            <div class="compact-subtitle">Bus Ticket</div>
        </div>

        <!-- Booking Reference -->
        <div class="compact-ref">{{ $booking->booking_reference }}</div>

        <!-- Route Information -->
        <div class="route-info">
            {{ $booking->schedule->route->sourceCity->name }} → {{ $booking->schedule->route->destinationCity->name }}
        </div>

        <!-- Travel Details -->
        <div class="compact-row large">
            <span>{{ $booking->schedule->travel_date->format('M d, Y') }}</span>
            <span>{{ $booking->schedule->departure_time->format('H:i') }}</span>
        </div>

        <!-- Passenger Information -->
        <div class="passenger-info">
            <div class="compact-row">
                <span>Passenger:</span>
                <span>{{ $booking->user->name }}</span>
            </div>
            <div class="compact-row">
                <span>Count:</span>
                <span>{{ $booking->passenger_count }} person(s)</span>
            </div>
        </div>

        <!-- Seat Information -->
        <div class="seat-info">
            Seat(s): {{ implode(', ', $booking->seat_numbers) }}
        </div>

        <!-- Bus Information -->
        <div class="bus-info">
            <div class="compact-row">
                <span>Bus:</span>
                <span>{{ $booking->schedule->bus->bus_number }}</span>
            </div>
            <div class="compact-row">
                <span>Type:</span>
                <span>{{ $booking->schedule->bus->busType->name ?? 'Standard' }}</span>
            </div>
        </div>

        <!-- Operator Information -->
        <div class="operator-info">
            Operator: {{ $booking->schedule->operator->name }}
        </div>

        <!-- Amount and Status -->
        <div class="amount-section">
            <div class="compact-row">
                <span>Total: NPR {{ number_format($booking->total_amount) }}</span>
                <span class="status-badge">{{ ucfirst($booking->status) }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-info">
            <div>Show this ticket to conductor</div>
            <div style="margin-top: 3px;">BookNGO - Your Journey Partner</div>
            <div style="margin-top: 2px;">Generated: {{ now()->format('M d, Y H:i') }}</div>
        </div>
    </div>
</body>
</html>
