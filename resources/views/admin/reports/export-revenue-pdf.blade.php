<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Revenue Report - BookNGo</title>
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
            border-bottom: 3px solid #059669;
            padding-bottom: 20px;
        }
        
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #059669;
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
        
        .period-info {
            background-color: #ECFDF5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #A7F3D0;
            text-align: center;
        }
        
        .period-info h3 {
            margin: 0 0 5px 0;
            font-size: 16px;
            color: #059669;
        }
        
        .summary {
            background-color: #F0FDF4;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 8px;
            border: 1px solid #BBF7D0;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-value {
            font-size: 22px;
            font-weight: bold;
            color: #059669;
        }
        
        .summary-label {
            font-size: 11px;
            color: #6B7280;
            margin-top: 5px;
        }
        
        .operator-revenue {
            margin-bottom: 25px;
        }
        
        .operator-revenue h3 {
            font-size: 16px;
            color: #374151;
            margin-bottom: 15px;
            border-bottom: 2px solid #E5E7EB;
            padding-bottom: 5px;
        }
        
        .operator-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin-bottom: 8px;
            background-color: #F9FAFB;
            border-radius: 6px;
            border: 1px solid #E5E7EB;
        }
        
        .operator-name {
            font-weight: bold;
            color: #1F2937;
        }
        
        .operator-stats {
            text-align: right;
        }
        
        .operator-revenue-amount {
            font-size: 14px;
            font-weight: bold;
            color: #059669;
        }
        
        .operator-bookings {
            font-size: 10px;
            color: #6B7280;
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
            background-color: #059669;
            color: white;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background-color: #F9FAFB;
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
        
        .page-break {
            page-break-before: always;
        }
        
        .chart-placeholder {
            background-color: #F3F4F6;
            border: 2px dashed #9CA3AF;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            color: #6B7280;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">BookNGo</div>
        <div class="report-title">Revenue Analysis Report</div>
        <div class="export-info">
            Generated on: {{ $exportDate->format('F j, Y \a\t g:i A') }}<br>
            Report Period: {{ ucfirst($period) }} {{ $period === 'month' ? Carbon\Carbon::create($year, $month)->format('F Y') : $year }}
        </div>
    </div>

    <div class="period-info">
        <h3>Report Period</h3>
        <p>{{ ucfirst($period) }} {{ $period === 'month' ? Carbon\Carbon::create($year, $month)->format('F Y') : $year }}</p>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">Rs. {{ number_format($summary['total_revenue'], 2) }}</div>
                <div class="summary-label">Total Revenue</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $summary['total_bookings'] }}</div>
                <div class="summary-label">Total Bookings</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">Rs. {{ number_format($summary['average_booking_value'], 2) }}</div>
                <div class="summary-label">Average Booking Value</div>
            </div>
        </div>
    </div>

    <div class="operator-revenue">
        <h3>Revenue by Operator</h3>
        @foreach($operatorRevenue->take(10) as $operatorName => $data)
        <div class="operator-item">
            <div class="operator-name">{{ $operatorName }}</div>
            <div class="operator-stats">
                <div class="operator-revenue-amount">Rs. {{ number_format($data['revenue'], 2) }}</div>
                <div class="operator-bookings">{{ $data['bookings'] }} bookings</div>
            </div>
        </div>
        @endforeach
    </div>

    @if($bookings->count() > 0)
    <div class="page-break"></div>
    <h3 style="color: #374151; margin-bottom: 15px; border-bottom: 2px solid #E5E7EB; padding-bottom: 5px;">Detailed Booking Records</h3>
    
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Booking Ref</th>
                <th>Customer</th>
                <th>Operator</th>
                <th>Route</th>
                <th>Seats</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings->take(100) as $booking)
            <tr>
                <td>{{ $booking->created_at->format('M j, Y') }}</td>
                <td>{{ $booking->booking_reference }}</td>
                <td>{{ $booking->user->name ?? 'Guest' }}</td>
                <td>{{ $booking->schedule->operator->company_name ?? 'N/A' }}</td>
                <td>{{ $booking->schedule->route->sourceCity->name ?? 'N/A' }} → {{ $booking->schedule->route->destinationCity->name ?? 'N/A' }}</td>
                <td>
                    @if(is_array($booking->seat_numbers))
                        {{ count($booking->seat_numbers) }}
                    @else
                        1
                    @endif
                </td>
                <td class="amount">Rs. {{ number_format($booking->total_amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    @if($bookings->count() > 100)
    <p style="text-align: center; color: #6B7280; margin-top: 15px; font-style: italic;">
        Showing first 100 bookings out of {{ $bookings->count() }} total bookings.
    </p>
    @endif
    @endif

    <div class="footer">
        <p><strong>BookNGo Bus Management System - Revenue Report</strong></p>
        <p>This report contains confidential financial information. Handle with care.</p>
        <p>Report generated by: Admin Panel | Export Date: {{ $exportDate->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
