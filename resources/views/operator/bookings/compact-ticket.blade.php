<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compact Bus Ticket - {{ $booking->booking_reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            background-color: #f8f9fa;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .compact-ticket {
            width: 380px;
            background: white;
            border: 2px solid #000;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .compact-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        
        .compact-title {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        
        .compact-subtitle {
            font-size: 12px;
            margin: 3px 0;
            color: #666;
        }
        
        .compact-ref {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 8px 0;
            padding: 5px;
            border: 2px solid #000;
            background: #f0f0f0;
        }
        
        .route-info {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 8px 0;
            padding: 5px;
            border-top: 2px dashed #000;
            border-bottom: 2px dashed #000;
        }
        
        .compact-row {
            display: flex;
            justify-content: space-between;
            margin: 4px 0;
            font-size: 12px;
        }
        
        .compact-row.large {
            font-size: 14px;
            font-weight: bold;
        }
        
        .passenger-info {
            margin: 8px 0;
            padding: 5px 0;
        }
        
        .seat-info {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 8px 0;
            padding: 5px;
            background: #e9ecef;
            border: 1px solid #ccc;
        }
        
        .bus-info {
            font-size: 11px;
            margin: 4px 0;
        }
        
        .operator-info {
            font-size: 11px;
            text-align: center;
            margin: 4px 0;
            color: #666;
        }
        
        .amount-section {
            border-top: 2px solid #000;
            padding-top: 5px;
            margin-top: 8px;
            font-weight: bold;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border: 1px solid #000;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .footer-info {
            text-align: center;
            font-size: 10px;
            margin-top: 12px;
            padding-top: 8px;
            border-top: 2px dashed #000;
            color: #666;
        }
        
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .print-btn:hover {
            background: #0056b3;
        }
        
        .download-btn {
            position: fixed;
            top: 20px;
            right: 140px;
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            text-decoration: none;
            display: inline-block;
        }
        
        .download-btn:hover {
            background: #1e7e34;
            text-decoration: none;
            color: white;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .print-btn, .download-btn {
                display: none;
            }
            .compact-ticket {
                box-shadow: none;
                border: 2px solid #000;
            }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ Print Ticket</button>
    <a href="{{ route('operator.bookings.download-compact-ticket', $booking) }}" class="download-btn">📄 Download PDF</a>
    
    <div class="compact-ticket">
        <!-- Header -->
        <div class="compact-header">
            <div class="compact-title">BookNGO</div>
            <div class="compact-subtitle">Bus Ticket - Operator Copy</div>
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
                <span>Phone:</span>
                <span>{{ $booking->user->phone ?? 'N/A' }}</span>
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

        <!-- Booking Type -->
        <div class="compact-row">
            <span>Booking Type:</span>
            <span>{{ ucfirst($booking->booking_type) }}</span>
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
            <div>Operator Copy - For Internal Use</div>
            <div style="margin-top: 3px;">BookNGO - Your Journey Partner</div>
            <div style="margin-top: 2px;">Generated: {{ now()->format('M d, Y H:i') }}</div>
        </div>
    </div>
</body>
</html>
