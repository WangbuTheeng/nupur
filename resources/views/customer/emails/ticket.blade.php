<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookNGO E-Ticket</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #e5e5e5;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .tagline {
            color: #666;
            font-size: 14px;
        }
        .booking-ref {
            background-color: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .booking-ref-label {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 5px;
        }
        .booking-ref-number {
            font-size: 24px;
            font-weight: bold;
            color: #1e293b;
        }
        .journey-details {
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .journey-header {
            font-size: 18px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 15px;
            text-align: center;
        }
        .route {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 16px;
            font-weight: 600;
        }
        .route-arrow {
            margin: 0 15px;
            color: #2563eb;
        }
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .detail-item {
            text-align: center;
        }
        .detail-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .detail-value {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
        }
        .message-section {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }
        .message-label {
            font-weight: bold;
            color: #92400e;
            margin-bottom: 5px;
        }
        .message-content {
            color: #78350f;
            font-style: italic;
        }
        .important-info {
            background-color: #dbeafe;
            border: 1px solid #93c5fd;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .important-info h4 {
            color: #1d4ed8;
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        .important-info ul {
            margin: 0;
            padding-left: 20px;
            color: #1e40af;
        }
        .important-info li {
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            border-top: 2px solid #e5e5e5;
            padding-top: 20px;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
        .contact-info {
            margin-top: 15px;
        }
        .contact-info a {
            color: #2563eb;
            text-decoration: none;
        }
        .compact-ticket {
            border: 2px solid #333;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            background: #fff;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        .ticket-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .email-container {
                padding: 20px;
            }
            .details-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            .route {
                flex-direction: column;
                gap: 10px;
            }
            .route-arrow {
                transform: rotate(90deg);
                margin: 5px 0;
            }
            .ticket-flex {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">BookNGO</div>
            <div class="tagline">Your Journey, Our Priority</div>
        </div>

        <!-- Greeting -->
        <h2 style="color: #1e293b; margin-bottom: 20px;">Hello {{ $booking->user->name }},</h2>

        <p>Thank you for booking with BookNGO! Here is your e-ticket:</p>

        <!-- Compact Ticket -->
        <div class="compact-ticket">
            <!-- Ticket Header -->
            <div style="text-align: center; padding: 15px; border-bottom: 1px solid #333;">
                <div style="font-size: 24px; font-weight: bold; color: #2563eb; margin-bottom: 5px;">BookNGO</div>
                <div style="font-size: 14px; color: #666;">Bus Ticket</div>
            </div>

            <!-- Booking Reference -->
            <div style="text-align: center; padding: 10px; border-bottom: 1px solid #333; background: #f8f9fa;">
                <div style="font-size: 20px; font-weight: bold; color: #333;">{{ $booking->booking_reference }}</div>
            </div>

            <!-- Journey Info -->
            <div style="padding: 15px; border-bottom: 1px solid #333;">
                <div class="ticket-flex" style="margin-bottom: 10px;">
                    <span style="font-weight: bold;">{{ $booking->schedule->route->sourceCity->name }}</span>
                    <span style="color: #666;">→</span>
                    <span style="font-weight: bold;">{{ $booking->schedule->route->destinationCity->name }}</span>
                </div>
                <div class="ticket-flex" style="font-size: 14px;">
                    <span>{{ $booking->schedule->travel_date->format('M d, Y') }}</span>
                    <span>{{ $booking->schedule->departure_time->format('H:i') }}</span>
                </div>
                <div style="margin-top: 10px; font-size: 14px;">
                    <strong>Bus:</strong> {{ $booking->schedule->bus->bus_number }}
                    <span style="margin-left: 20px;"><strong>Seats:</strong> {{ implode(', ', $booking->seat_numbers) }}</span>
                </div>
            </div>

            <!-- Passenger Details -->
            <div style="padding: 15px; border-bottom: 1px solid #333;">
                @foreach($booking->passenger_details as $index => $passenger)
                <div style="margin-bottom: 8px; font-size: 14px;">
                    <strong>Seat {{ $booking->seat_numbers[$index] ?? ($index + 1) }}:</strong> {{ $passenger['name'] }} ({{ $passenger['age'] }}/{{ strtoupper(substr($passenger['gender'], 0, 1)) }})
                </div>
                @endforeach
            </div>

            <!-- Total Amount -->
            <div style="padding: 15px; text-align: center; background: #f8f9fa;">
                <div style="font-size: 14px; color: #666; margin-bottom: 5px;">Total: NPR {{ number_format($booking->total_amount, 0) }}</div>
                <div style="font-size: 16px; font-weight: bold; color: #28a745;">{{ ucfirst($booking->status) }}</div>
            </div>

            <!-- Instructions -->
            <div style="padding: 10px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #333;">
                Show this ticket to conductor
            </div>
        </div>

        <!-- Personal Message -->
        @if($personalMessage)
        <div class="message-section">
            <div class="message-label">Personal Message:</div>
            <div class="message-content">{{ $personalMessage }}</div>
        </div>
        @endif

        <!-- Important Information -->
        <div class="important-info">
            <h4>Important Information:</h4>
            <ul>
                <li>Please arrive at the departure point at least 15 minutes before departure time</li>
                <li>Carry a valid ID proof along with this ticket</li>
                <li>Show your booking reference ({{ $booking->booking_reference }}) to the conductor</li>
                <li>Keep your ticket safe throughout the journey</li>
                <li>Contact us immediately if you need to make any changes</li>
            </ul>
        </div>

        <p>We wish you a safe and comfortable journey!</p>

        <!-- Footer -->
        <div class="footer">
            <p><strong>BookNGO - Bus Booking System</strong></p>
            <div class="contact-info">
                <p>For support, contact us at: <a href="mailto:support@bookngo.com">support@bookngo.com</a></p>
                <p>Visit our website: <a href="{{ config('app.url', 'http://localhost') }}">{{ config('app.url', 'http://localhost') }}</a></p>
            </div>
            <p style="margin-top: 20px; font-size: 12px; color: #999;">
                This is an automated email. Please do not reply to this email address.
            </p>
        </div>
    </div>
</body>
</html>
