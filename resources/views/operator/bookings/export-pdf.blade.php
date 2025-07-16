<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings Export - {{ $operator->company_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 20px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .export-info {
            font-size: 11px;
            color: #666;
        }
        
        .filters {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #e9ecef;
        }
        
        .filters h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #495057;
        }
        
        .filter-item {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 5px;
        }
        
        .summary {
            background-color: #e7f3ff;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #b3d9ff;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #4F46E5;
        }
        
        .summary-label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        
        th {
            background-color: #4F46E5;
            color: white;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .status {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .amount {
            font-weight: bold;
            color: #28a745;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $operator->company_name }}</div>
        <div class="report-title">Bookings Export Report</div>
        <div class="export-info">
            Generated on: {{ $exportDate->format('F j, Y \a\t g:i A') }}<br>
            Total Bookings: {{ $bookings->count() }}
        </div>
    </div>

    @if(!empty($filters))
    <div class="filters">
        <h3>Applied Filters:</h3>
        @if(isset($filters['status']) && $filters['status'])
            <div class="filter-item"><strong>Status:</strong> {{ ucfirst($filters['status']) }}</div>
        @endif
        @if(isset($filters['date_from']) && $filters['date_from'])
            <div class="filter-item"><strong>From Date:</strong> {{ $filters['date_from'] }}</div>
        @endif
        @if(isset($filters['date_to']) && $filters['date_to'])
            <div class="filter-item"><strong>To Date:</strong> {{ $filters['date_to'] }}</div>
        @endif
        @if(isset($filters['search']) && $filters['search'])
            <div class="filter-item"><strong>Search:</strong> {{ $filters['search'] }}</div>
        @endif
    </div>
    @endif

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ $bookings->count() }}</div>
                <div class="summary-label">Total Bookings</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $bookings->where('status', 'confirmed')->count() }}</div>
                <div class="summary-label">Confirmed</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $bookings->where('status', 'pending')->count() }}</div>
                <div class="summary-label">Pending</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">Rs. {{ number_format($bookings->where('status', 'confirmed')->sum('total_amount'), 2) }}</div>
                <div class="summary-label">Total Revenue</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Booking Ref</th>
                <th>Customer</th>
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
                    <small>{{ $booking->user->email ?? 'N/A' }}</small>
                </td>
                <td>{{ $booking->schedule->route->sourceCity->name }} → {{ $booking->schedule->route->destinationCity->name }}</td>
                <td>{{ $booking->schedule->travel_date->format('M j, Y') }}</td>
                <td>{{ $booking->schedule->departure_time->format('H:i') }}</td>
                <td>{{ $booking->schedule->bus->bus_number ?? 'N/A' }}</td>
                <td>{{ implode(', ', $booking->seat_numbers ?? []) }}</td>
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
        <p>This report was generated by BookNGo Bus Management System</p>
        <p>{{ $operator->company_name }} - {{ $operator->company_address ?? 'N/A' }}</p>
    </div>
</body>
</html>
