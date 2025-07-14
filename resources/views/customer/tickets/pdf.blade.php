<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookNGO E-Ticket - {{ $booking->booking_reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }
        
        .ticket-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        
        .tagline {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .ticket-title {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin-top: 15px;
        }
        
        .booking-ref {
            font-size: 16px;
            color: #2563eb;
            font-weight: bold;
            margin-top: 5px;
        }
        
        .ticket-body {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .left-section {
            display: table-cell;
            width: 65%;
            vertical-align: top;
            padding-right: 20px;
        }
        
        .right-section {
            display: table-cell;
            width: 35%;
            vertical-align: top;
            text-align: center;
            border-left: 2px dashed #e5e7eb;
            padding-left: 20px;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .info-grid {
            display: table;
            width: 100%;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            color: #4b5563;
            padding: 5px 10px 5px 0;
            width: 40%;
        }
        
        .info-value {
            display: table-cell;
            color: #1f2937;
            padding: 5px 0;
        }
        
        .journey-info {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .route-display {
            text-align: center;
            margin-bottom: 15px;
        }
        
        .route-cities {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
        }
        
        .route-arrow {
            margin: 0 10px;
            color: #6b7280;
        }
        
        .journey-details {
            display: table;
            width: 100%;
        }
        
        .journey-col {
            display: table-cell;
            text-align: center;
            width: 33.33%;
        }
        
        .journey-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        
        .journey-value {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
        }
        
        .passenger-list {
            background: #fefefe;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px;
        }
        
        .passenger-item {
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .passenger-item:last-child {
            border-bottom: none;
        }
        
        .passenger-name {
            font-weight: bold;
            color: #1f2937;
        }
        
        .passenger-details {
            font-size: 11px;
            color: #6b7280;
            margin-top: 2px;
        }
        
        .seat-numbers {
            background: #dbeafe;
            color: #1e40af;
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }
        
        .verification-section {
            text-align: center;
        }

        .booking-reference {
            margin: 15px 0;
            font-size: 24px;
            font-weight: bold;
            color: #333;
            padding: 10px;
            border: 2px solid #333;
            display: inline-block;
        }

        .verification-instruction {
            font-size: 10px;
            color: #6b7280;
            margin-top: 10px;
        }
        
        .amount-section {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
        }
        
        .amount-label {
            font-size: 12px;
            color: #166534;
            margin-bottom: 5px;
        }
        
        .amount-value {
            font-size: 18px;
            font-weight: bold;
            color: #15803d;
        }
        
        .footer {
            border-top: 2px solid #e5e7eb;
            padding-top: 20px;
            margin-top: 30px;
            text-align: center;
        }
        
        .terms {
            font-size: 10px;
            color: #6b7280;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        
        .contact-info {
            font-size: 11px;
            color: #4b5563;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-confirmed {
            background: #dcfce7;
            color: #166534;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60px;
            color: rgba(37, 99, 235, 0.1);
            font-weight: bold;
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="watermark">BookNGO</div>
    
    <div class="ticket-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">BookNGO</div>
            <div class="tagline">Nepal's Premier Bus Booking Platform</div>
            <div class="ticket-title">E-TICKET</div>
            <div class="booking-ref">{{ $booking->booking_reference }}</div>
        </div>

        <!-- Ticket Body -->
        <div class="ticket-body">
            <!-- Left Section -->
            <div class="left-section">
                <!-- Journey Information -->
                <div class="section">
                    <div class="section-title">Journey Information</div>
                    <div class="journey-info">
                        <div class="route-display">
                            <span class="route-cities">
                                {{ $booking->schedule->route->sourceCity->name }}
                                <span class="route-arrow">→</span>
                                {{ $booking->schedule->route->destinationCity->name }}
                            </span>
                        </div>
                        
                        <div class="journey-details">
                            <div class="journey-col">
                                <div class="journey-label">Departure</div>
                                <div class="journey-value">{{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('g:i A') }}</div>
                            </div>
                            <div class="journey-col">
                                <div class="journey-label">Travel Date</div>
                                <div class="journey-value">{{ \Carbon\Carbon::parse($booking->schedule->travel_date)->format('M j, Y') }}</div>
                            </div>
                            <div class="journey-col">
                                <div class="journey-label">Arrival</div>
                                <div class="journey-value">{{ \Carbon\Carbon::parse($booking->schedule->arrival_time)->format('g:i A') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bus & Operator Information -->
                <div class="section">
                    <div class="section-title">Bus & Operator Details</div>
                    <div class="info-grid">
                        <div class="info-row">
                            <div class="info-label">Operator:</div>
                            <div class="info-value">{{ $booking->schedule->operator->company_name ?? $booking->schedule->operator->name }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Bus Number:</div>
                            <div class="info-value">{{ $booking->schedule->bus->bus_number }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Bus Type:</div>
                            <div class="info-value">{{ $booking->schedule->bus->busType->name }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Status:</div>
                            <div class="info-value">
                                <span class="status-badge status-confirmed">{{ ucfirst($booking->status) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Passenger Information -->
                <div class="section">
                    <div class="section-title">Passenger Details</div>
                    <div class="passenger-list">
                        @foreach($booking->passenger_details as $index => $passenger)
                            <div class="passenger-item">
                                <div class="passenger-name">{{ $passenger['name'] }}</div>
                                <div class="passenger-details">
                                    Age: {{ $passenger['age'] }} | Gender: {{ ucfirst($passenger['gender']) }}
                                    @if(isset($passenger['phone']) && $passenger['phone'])
                                        | Phone: {{ $passenger['phone'] }}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Booking Information -->
                <div class="section">
                    <div class="section-title">Booking Details</div>
                    <div class="info-grid">
                        <div class="info-row">
                            <div class="info-label">Booking Date:</div>
                            <div class="info-value">{{ $booking->created_at->format('M j, Y g:i A') }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Contact Phone:</div>
                            <div class="info-value">{{ $booking->contact_phone }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Contact Email:</div>
                            <div class="info-value">{{ $booking->contact_email }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Payment Method:</div>
                            <div class="info-value">{{ ucfirst($booking->payment_method ?? 'N/A') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Section -->
            <div class="right-section">
                <!-- Seat Numbers -->
                <div class="seat-numbers">
                    Seat(s): {{ implode(', ', $booking->seat_numbers) }}
                </div>

                <!-- Verification -->
                <div class="verification-section">
                    <div class="section-title">Ticket Verification</div>
                    <div class="booking-reference">
                        {{ $booking->booking_reference }}
                    </div>
                    <div class="verification-instruction">
                        Show this booking reference for ticket verification
                    </div>
                </div>

                <!-- Amount -->
                <div class="amount-section">
                    <div class="amount-label">Total Amount Paid</div>
                    <div class="amount-value">Rs. {{ number_format($booking->total_amount, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="terms">
                <strong>Terms & Conditions:</strong><br>
                • This e-ticket is valid only for the specified journey date and time.<br>
                • Please arrive at the departure point at least 30 minutes before departure.<br>
                • Carry a valid photo ID along with this e-ticket.<br>
                • Cancellation and refund policies apply as per operator terms.<br>
                • This ticket is non-transferable and non-refundable after departure.
            </div>
            
            <div class="contact-info">
                <strong>BookNGO Customer Support:</strong><br>
                Email: support@bookngo.com | Phone: +977-1-4444444<br>
                Website: www.bookngo.com
            </div>
        </div>
    </div>
</body>
</html>
