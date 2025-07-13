<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Seat Layout Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/seat-map.css') }}">
    <style>
        .viewport-indicator {
            position: fixed;
            top: 10px;
            left: 10px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            z-index: 1000;
        }
        
        .test-section {
            border: 2px dashed #e5e7eb;
            margin: 20px 0;
            padding: 20px;
            border-radius: 8px;
        }
        
        .breakpoint-info {
            background: #f3f4f6;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="viewport-indicator">
        <span id="viewport-size"></span>
    </div>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8 text-center">Responsive Seat Layout Test</h1>
        
        <div class="breakpoint-info">
            <strong>Responsive Breakpoints:</strong><br>
            • Desktop: > 768px (Normal size)<br>
            • Tablet: ≤ 768px (Smaller seats, reduced padding)<br>
            • Mobile: ≤ 480px (Smallest seats, vertical legend)
        </div>

        <!-- Test Section 1: 2x2 Layout -->
        <div class="test-section">
            <h2 class="text-xl font-semibold mb-4">2x2 Layout (31 seats) - Most Common</h2>
            <div id="layout-2x2"></div>
        </div>

        <!-- Test Section 2: 2x1 Layout -->
        <div class="test-section">
            <h2 class="text-xl font-semibold mb-4">2x1 Layout (25 seats) - Compact</h2>
            <div id="layout-2x1"></div>
        </div>

        <!-- Test Section 3: 3x2 Layout -->
        <div class="test-section">
            <h2 class="text-xl font-semibold mb-4">3x2 Layout (39 seats) - Large Bus</h2>
            <div id="layout-3x2"></div>
        </div>

        <!-- Instructions -->
        <div class="bg-white rounded-lg shadow-lg p-6 mt-8">
            <h3 class="text-lg font-semibold mb-4">Testing Instructions</h3>
            <ol class="list-decimal list-inside space-y-2 text-sm">
                <li>Resize your browser window to test different screen sizes</li>
                <li>Check that seats remain clickable and properly sized on mobile</li>
                <li>Verify that the legend adapts properly (vertical on mobile)</li>
                <li>Ensure aisle spaces maintain proper proportions</li>
                <li>Test that the bus frame doesn't overflow on small screens</li>
                <li>Verify that driver seat and door icons remain visible</li>
                <li>Check that back row seats display correctly across all sizes</li>
            </ol>
        </div>
    </div>

    <script src="{{ asset('js/realtime-seat-map.js') }}"></script>
    <script>
        // Test layouts with different configurations
        const testLayouts = {
            '2x2': {
                layout_type: '2x2',
                total_seats: 31,
                rows: 7,
                columns: 5,
                aisle_position: 2,
                has_back_row: true,
                back_row_seats: 5,
                driver_seat: { position: 'top-right', row: 0, column: 5 },
                door: { position: 'top-left', row: 0, column: 0 },
                seats: generateTestSeats(31, '2x2')
            },
            '2x1': {
                layout_type: '2x1',
                total_seats: 25,
                rows: 8,
                columns: 4,
                aisle_position: 2,
                has_back_row: true,
                back_row_seats: 4,
                driver_seat: { position: 'top-right', row: 0, column: 4 },
                door: { position: 'top-left', row: 0, column: 0 },
                seats: generateTestSeats(25, '2x1')
            },
            '3x2': {
                layout_type: '3x2',
                total_seats: 39,
                rows: 7,
                columns: 6,
                aisle_position: 3,
                has_back_row: true,
                back_row_seats: 6,
                driver_seat: { position: 'top-right', row: 0, column: 6 },
                door: { position: 'top-left', row: 0, column: 0 },
                seats: generateTestSeats(39, '3x2')
            }
        };

        function generateTestSeats(totalSeats, layoutType) {
            const seats = [];
            let seatNumber = 1;
            const configs = {
                '2x2': { leftSeats: 2, rightSeats: 2, backRowSeats: 5 },
                '2x1': { leftSeats: 2, rightSeats: 1, backRowSeats: 4 },
                '3x2': { leftSeats: 3, rightSeats: 2, backRowSeats: 6 }
            };
            
            const config = configs[layoutType];
            const regularSeats = totalSeats - config.backRowSeats;
            const seatsPerRow = config.leftSeats + config.rightSeats;
            const regularRows = Math.ceil(regularSeats / seatsPerRow);
            
            // Generate regular rows with mixed statuses for testing
            for (let row = 1; row <= regularRows; row++) {
                const rowLetter = String.fromCharCode(64 + row);
                
                // Left side seats
                for (let i = 1; i <= config.leftSeats && seatNumber <= regularSeats; i++) {
                    const status = getTestStatus(seatNumber);
                    seats.push({
                        number: rowLetter + i,
                        row: row,
                        column: i,
                        type: 'regular',
                        is_window: i === 1,
                        is_aisle: i === config.leftSeats,
                        is_available: status === 'available',
                        is_booked: status === 'booked',
                        is_reserved: status === 'reserved',
                        is_selected: status === 'selected',
                        side: 'left'
                    });
                    seatNumber++;
                }
                
                // Right side seats
                for (let i = 1; i <= config.rightSeats && seatNumber <= regularSeats; i++) {
                    const status = getTestStatus(seatNumber);
                    seats.push({
                        number: rowLetter + (config.leftSeats + i),
                        row: row,
                        column: config.leftSeats + 1 + i,
                        type: 'regular',
                        is_window: i === config.rightSeats,
                        is_aisle: i === 1,
                        is_available: status === 'available',
                        is_booked: status === 'booked',
                        is_reserved: status === 'reserved',
                        is_selected: status === 'selected',
                        side: 'right'
                    });
                    seatNumber++;
                }
            }
            
            // Generate back row
            const backRowLetter = String.fromCharCode(64 + regularRows + 1);
            for (let i = 1; i <= config.backRowSeats; i++) {
                const status = getTestStatus(seatNumber);
                seats.push({
                    number: backRowLetter + i,
                    row: regularRows + 1,
                    column: i,
                    type: 'back_row',
                    is_window: i === 1 || i === config.backRowSeats,
                    is_aisle: false,
                    is_available: status === 'available',
                    is_booked: status === 'booked',
                    is_reserved: status === 'reserved',
                    is_selected: status === 'selected',
                    side: 'back'
                });
                seatNumber++;
            }
            
            return seats;
        }

        function getTestStatus(seatNumber) {
            // Create a pattern of different seat statuses for testing
            const statuses = ['available', 'booked', 'reserved', 'selected'];
            return statuses[seatNumber % 4];
        }

        // Update viewport size indicator
        function updateViewportSize() {
            const width = window.innerWidth;
            const height = window.innerHeight;
            let breakpoint = 'Desktop';
            
            if (width <= 480) {
                breakpoint = 'Mobile';
            } else if (width <= 768) {
                breakpoint = 'Tablet';
            }
            
            document.getElementById('viewport-size').textContent = 
                `${width}×${height} (${breakpoint})`;
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Render test layouts
            Object.keys(testLayouts).forEach(layoutType => {
                const container = document.getElementById(`layout-${layoutType}`);
                if (container && typeof SeatLayoutPreview !== 'undefined') {
                    const preview = new SeatLayoutPreview(testLayouts[layoutType], container);
                    preview.render();
                }
            });

            // Update viewport size
            updateViewportSize();
            window.addEventListener('resize', updateViewportSize);
        });
    </script>
</body>
</html>
