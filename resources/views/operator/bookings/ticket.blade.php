<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Ticket - {{ $booking->booking_reference }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .ticket {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .ticket-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .ticket-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .ticket-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        .ticket-body {
            padding: 30px;
        }
        .route-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .route-point {
            text-align: center;
            flex: 1;
        }
        .route-point h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .route-point p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }
        .route-arrow {
            flex: 0 0 auto;
            margin: 0 20px;
            font-size: 24px;
            color: #007bff;
        }
        .ticket-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        .detail-group h4 {
            margin: 0 0 15px 0;
            color: #333;
            font-size: 16px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-item:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #555;
        }
        .detail-value {
            color: #333;
        }
        .passenger-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .passenger-info h4 {
            margin: 0 0 15px 0;
            color: #333;
        }
        .passenger-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        .passenger-item {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #007bff;
        }
        .seat-numbers {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }
        .seat-badge {
            background: #007bff;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .qr-section {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .booking-ref {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            color: #666;
            font-size: 12px;
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
        }
        .print-btn:hover {
            background: #0056b3;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .print-btn {
                display: none;
            }
            .ticket {
                box-shadow: none;
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ Print Ticket</button>
    
    <div class="ticket">
        <div class="ticket-header">
            <h1>🚌 Bus Ticket</h1>
            <p>{{ config('app.name', 'BookNGo') }} - Your Journey Partner</p>
        </div>
        
        <div class="ticket-body">
            <!-- Route Information -->
            <div class="route-info">
                <div class="route-point">
                    <h3>{{ $booking->schedule->route->sourceCity->name }}</h3>
                    <p>Departure</p>
                    <p><strong>{{ $booking->schedule->departure_time }}</strong></p>
                </div>
                <div class="route-arrow">→</div>
                <div class="route-point">
                    <h3>{{ $booking->schedule->route->destinationCity->name }}</h3>
                    <p>Arrival</p>
                    <p><strong>{{ $booking->schedule->arrival_time }}</strong></p>
                </div>
            </div>

            <!-- Ticket Details -->
            <div class="ticket-details">
                <div class="detail-group">
                    <h4>Journey Details</h4>
                    <div class="detail-item">
                        <span class="detail-label">Travel Date:</span>
                        <span class="detail-value">{{ $booking->schedule->travel_date->format('l, F d, Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Bus Number:</span>
                        <span class="detail-value">{{ $booking->schedule->bus->bus_number }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Bus Type:</span>
                        <span class="detail-value">{{ $booking->schedule->bus->busType->name }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Distance:</span>
                        <span class="detail-value">{{ $booking->schedule->route->distance_km }} km</span>
                    </div>
                </div>

                <div class="detail-group">
                    <h4>Booking Details</h4>
                    <div class="detail-item">
                        <span class="detail-label">Booking Reference:</span>
                        <span class="detail-value">{{ $booking->booking_reference }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Passenger Count:</span>
                        <span class="detail-value">{{ $booking->passenger_count }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Total Amount:</span>
                        <span class="detail-value">Rs. {{ number_format($booking->total_amount, 2) }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Booking Type:</span>
                        <span class="detail-value">{{ ucfirst($booking->booking_type) }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Booked On:</span>
                        <span class="detail-value">{{ $booking->created_at->format('M d, Y H:i A') }}</span>
                    </div>
                </div>
            </div>

            <!-- Seat Numbers -->
            <div class="detail-group">
                <h4>Seat Numbers</h4>
                <div class="seat-numbers">
                    @foreach($booking->seat_numbers as $seat)
                        <span class="seat-badge">{{ $seat }}</span>
                    @endforeach
                </div>
            </div>

            <!-- Passenger Information -->
            <div class="passenger-info">
                <h4>Passenger Information</h4>
                <div class="passenger-list">
                    @if($booking->passenger_details)
                        @foreach($booking->passenger_details as $index => $passenger)
                            <div class="passenger-item">
                                <strong>Passenger {{ $index + 1 }}</strong><br>
                                <small>
                                    Name: {{ $passenger['name'] ?? 'N/A' }}<br>
                                    @if(isset($passenger['age']))
                                        Age: {{ $passenger['age'] }}<br>
                                    @endif
                                    @if(isset($passenger['gender']))
                                        Gender: {{ ucfirst($passenger['gender']) }}<br>
                                    @endif
                                    @if(isset($passenger['phone']))
                                        Phone: {{ $passenger['phone'] }}<br>
                                    @endif
                                </small>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Contact Information -->
            <div class="detail-group">
                <h4>Contact Information</h4>
                <div class="detail-item">
                    <span class="detail-label">Primary Contact:</span>
                    <span class="detail-value">{{ $booking->user->name }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Phone:</span>
                    <span class="detail-value">{{ $booking->contact_phone }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">{{ $booking->contact_email }}</span>
                </div>
            </div>

            <!-- QR Code Section -->
            <div class="qr-section">
                <div class="booking-ref">{{ $booking->booking_reference }}</div>
                <p>Show this ticket to the conductor</p>
            </div>

            @if($booking->special_requests)
                <div class="detail-group">
                    <h4>Special Requests</h4>
                    <p>{{ $booking->special_requests }}</p>
                </div>
            @endif
        </div>

        <div class="footer">
            <p><strong>Important Instructions:</strong></p>
            <p>• Please arrive at the departure point at least 15 minutes before departure time</p>
            <p>• Carry a valid ID proof during travel</p>
            <p>• This ticket is non-transferable and non-refundable</p>
            <p>• For any queries, contact our customer support</p>
            <br>
            <p>Generated on {{ now()->format('F d, Y \a\t H:i A') }}</p>
            <p>Thank you for choosing {{ config('app.name', 'BookNGo') }}!</p>
        </div>
    </div>

    <script>
        // Auto-print when opened in new window
        if (window.location.search.includes('print=1')) {
            window.onload = function() {
                window.print();
            };
        }
    </script>
</body>
</html>
