<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Ticket - {{ $booking->booking_reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .ticket-header h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .ticket-header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .ticket-body {
            padding: 30px;
        }
        
        .booking-ref {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .booking-ref h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .booking-ref p {
            color: #666;
            font-size: 14px;
        }
        
        .ticket-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .detail-section h3 {
            color: #333;
            font-size: 18px;
            margin-bottom: 15px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 5px;
        }
        
        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .detail-label {
            color: #666;
            font-weight: 500;
        }
        
        .detail-value {
            color: #333;
            font-weight: 600;
        }
        
        .passenger-section {
            margin-bottom: 30px;
        }
        
        .passenger-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .passenger-table th,
        .passenger-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .passenger-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        .verification-section {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .verification-section h3 {
            color: #333;
            margin-bottom: 15px;
        }

        .booking-ref {
            display: inline-block;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        
        .instructions {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .instructions h4 {
            color: #1976d2;
            margin-bottom: 10px;
        }
        
        .instructions ul {
            color: #333;
            padding-left: 20px;
        }
        
        .instructions li {
            margin-bottom: 5px;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            color: #666;
            font-size: 12px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            
            .ticket {
                box-shadow: none;
                max-width: none;
            }
        }
        
        @media (max-width: 768px) {
            .ticket-details {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .ticket-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <!-- Header -->
        <div class="ticket-header">
            <h1>BookNGo</h1>
            <p>Digital Bus Ticket</p>
        </div>
        
        <!-- Body -->
        <div class="ticket-body">
            <!-- Booking Reference -->
            <div class="booking-ref">
                <h2>{{ $booking->booking_reference }}</h2>
                <p>Booking Reference Number</p>
                <span class="status-badge status-confirmed">{{ ucfirst($booking->status) }}</span>
            </div>
            
            <!-- Trip Details -->
            <div class="ticket-details">
                <div class="detail-section">
                    <h3>Trip Information</h3>
                    <div class="detail-item">
                        <span class="detail-label">Route:</span>
                        <span class="detail-value">{{ $booking->schedule->route->full_name }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Bus:</span>
                        <span class="detail-value">{{ $booking->schedule->bus->display_name }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Bus Type:</span>
                        <span class="detail-value">{{ $booking->schedule->bus->busType->name }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Travel Date:</span>
                        <span class="detail-value">{{ $booking->schedule->travel_date->format('l, M d, Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Departure:</span>
                        <span class="detail-value">{{ $booking->schedule->departure_time->format('h:i A') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Arrival:</span>
                        <span class="detail-value">{{ $booking->schedule->arrival_time->format('h:i A') }}</span>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h3>Booking Details</h3>
                    <div class="detail-item">
                        <span class="detail-label">Passenger:</span>
                        <span class="detail-value">{{ $booking->user->name }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Contact:</span>
                        <span class="detail-value">{{ $booking->contact_phone }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value">{{ $booking->contact_email }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Seats:</span>
                        <span class="detail-value">{{ $booking->seat_numbers_string }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Passengers:</span>
                        <span class="detail-value">{{ $booking->passenger_count }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Total Amount:</span>
                        <span class="detail-value">NPR {{ number_format($booking->total_amount) }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Passenger Details -->
            <div class="passenger-section">
                <h3>Passenger Information</h3>
                <table class="passenger-table">
                    <thead>
                        <tr>
                            <th>Seat</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($booking->passenger_details as $index => $passenger)
                            <tr>
                                <td>{{ $booking->seat_numbers[$index] ?? 'N/A' }}</td>
                                <td>{{ $passenger['name'] }}</td>
                                <td>{{ $passenger['age'] }}</td>
                                <td>{{ ucfirst($passenger['gender']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Verification -->
            <div class="verification-section">
                <h3>Ticket Verification</h3>
                <div class="booking-ref">
                    {{ $booking->booking_reference }}
                </div>
                <p style="margin-top: 10px; color: #666; font-size: 14px;">
                    Show this booking reference to the bus conductor for verification
                </p>
            </div>
            
            <!-- Instructions -->
            <div class="instructions">
                <h4>Important Instructions:</h4>
                <ul>
                    <li>Please arrive at the departure point at least 15 minutes before departure time</li>
                    <li>Show this ticket (digital or printed) to the bus conductor</li>
                    <li>Keep your ID proof ready for verification</li>
                    <li>Contact {{ $booking->contact_phone }} for any queries</li>
                    <li>Cancellation is allowed up to 2 hours before departure</li>
                </ul>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Generated on {{ now()->format('M d, Y h:i A') }} | BookNGo - Your Trusted Travel Partner</p>
            <p>For support, visit our website or call our helpline</p>
        </div>
    </div>
</body>
</html>
