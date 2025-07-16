<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Booking Reports - BookNGo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #1F2937;
            padding-bottom: 20px;
        }
        
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #374151;
        }
        
        .export-info {
            font-size: 12px;
            color: #6B7280;
        }
        
        .filters {
            background-color: #F9FAFB;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #E5E7EB;
        }
        
        .filters h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #374151;
        }
        
        .filter-item {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 5px;
        }
        
        .summary {
            background-color: #EFF6FF;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 8px;
            border: 1px solid #BFDBFE;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-value {
            font-size: 20px;
            font-weight: bold;
            color: #1D4ED8;
        }
        
        .summary-label {
            font-size: 11px;
            color: #6B7280;
            margin-top: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            border: 1px solid #D1D5DB;
            padding: 8px;
            text-align: left;
            font-size: 9px;
        }
        
        th {
            background-color: #1F2937;
            color: white;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        
        .status {
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-confirmed {
            background-color: #D1FAE5;
            color: #065F46;
        }
        
        .status-pending {
            background-color: #FEF3C7;
            color: #92400E;
        }
        
        .status-cancelled {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        
        .amount {
            font-weight: bold;
            color: #059669;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6B7280;
            border-top: 1px solid #D1D5DB;
            padding-top: 15px;
        }
        
        .operator-name {
            font-weight: bold;
            color: #1F2937;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">BookNGo</div>
        <div class="report-title">Admin Booking Reports</div>
        <div class="export-info">
            Generated on: {{ $exportDate->format('F j, Y \a\t g:i A') }}<br>
            Total Bookings: {{ $bookings->count() }}
        </div>
    </div>

    @if(!empty($filters))
    <div class="filters">
        <h3>Applied Filters:</h3>
        @if(isset($filters['date_from']) && $filters['date_from'])
            <div class="filter-item"><strong>From Date:</strong> {{ $filters['date_from'] }}</div>
        @endif
        @if(isset($filters['date_to']) && $filters['date_to'])
            <div class="filter-item"><strong>To Date:</strong> {{ $filters['date_to'] }}</div>
        @endif
        @if(isset($filters['operator']) && $filters['operator'])
            <div class="filter-item"><strong>Operator ID:</strong> {{ $filters['operator'] }}</div>
        @endif
        @if(isset($filters['status']) && $filters['status'])
            <div class="filter-item"><strong>Status:</strong> {{ ucfirst($filters['status']) }}</div>
        @endif
    </div>
    @endif

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ $summary['total_bookings'] }}</div>
                <div class="summary-label">Total Bookings</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $summary['confirmed_bookings'] }}</div>
                <div class="summary-label">Confirmed</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $summary['cancelled_bookings'] }}</div>
                <div class="summary-label">Cancelled</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">Rs. {{ number_format($summary['total_revenue'], 2) }}</div>
                <div class="summary-label">Total Revenue</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Booking Ref</th>
                <th>Customer</th>
                <th>Operator</th>
                <th>Route</th>
                <th>Travel Date</th>
                <th>Departure</th>
                <th>Bus</th>
                <th>Seats</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Booking Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings as $booking)
            <tr>
                <td>{{ $booking->booking_reference }}</td>
                <td>
                    <strong>{{ $booking->user->name ?? 'Guest' }}</strong><br>
                    <small>{{ $booking->user->email ?? $booking->contact_email ?? 'N/A' }}</small>
                </td>
                <td class="operator-name">{{ $booking->schedule->operator->company_name ?? 'N/A' }}</td>
                <td>{{ $booking->schedule->route->sourceCity->name ?? 'N/A' }} → {{ $booking->schedule->route->destinationCity->name ?? 'N/A' }}</td>
                <td>{{ $booking->schedule->travel_date->format('M j, Y') }}</td>
                <td>{{ $booking->schedule->departure_time->format('H:i') }}</td>
                <td>{{ $booking->schedule->bus->bus_number ?? 'N/A' }}</td>
                <td>
                    @if(is_array($booking->seat_numbers))
                        {{ implode(', ', $booking->seat_numbers) }}
                    @else
                        {{ $booking->seat_numbers ?? 'N/A' }}
                    @endif
                </td>
                <td class="amount">Rs. {{ number_format($booking->total_amount, 2) }}</td>
                <td>
                    <span class="status status-{{ $booking->status }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </td>
                <td>{{ $booking->created_at->format('M j, Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p><strong>BookNGo Bus Management System - Admin Report</strong></p>
        <p>This report contains confidential business information. Handle with care.</p>
        <p>Report generated by: Admin Panel | Export Date: {{ $exportDate->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
