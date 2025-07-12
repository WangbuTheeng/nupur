<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookNGO - Dynamic Seat Layout Demo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/seat-map.css') }}">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">BookNGO Dynamic Seat Layout System</h1>
                <p class="text-xl text-gray-600 mb-6">Real-world bus seat configurations with driver seat and front door</p>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 inline-block">
                    <p class="text-blue-800 font-medium">✨ Features: Driver Seat (Top-Right) • Front Door (Top-Left) • Continuous Back Row • Multiple Layout Types</p>
                </div>
            </div>

            <!-- Layout Examples -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
                <!-- 2x2 Layout -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                        <h3 class="text-xl font-bold text-white">2x2 Standard Layout</h3>
                        <p class="text-blue-100">31 Passengers • Most Common</p>
                    </div>
                    <div class="p-6">
                        <div id="layout-2x2" class="mb-4"></div>
                        <div class="text-sm text-gray-600">
                            <p><strong>Configuration:</strong> 2 seats | aisle | 2 seats</p>
                            <p><strong>Back Row:</strong> 5 continuous seats</p>
                            <p><strong>Best for:</strong> Standard buses, city routes</p>
                        </div>
                    </div>
                </div>

                <!-- 2x1 Layout -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                        <h3 class="text-xl font-bold text-white">2x1 Compact Layout</h3>
                        <p class="text-green-100">25 Passengers • Space Efficient</p>
                    </div>
                    <div class="p-6">
                        <div id="layout-2x1" class="mb-4"></div>
                        <div class="text-sm text-gray-600">
                            <p><strong>Configuration:</strong> 2 seats | aisle | 1 seat</p>
                            <p><strong>Back Row:</strong> 4 continuous seats</p>
                            <p><strong>Best for:</strong> Smaller buses, rural routes</p>
                        </div>
                    </div>
                </div>

                <!-- 3x2 Layout -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                        <h3 class="text-xl font-bold text-white">3x2 Large Layout</h3>
                        <p class="text-purple-100">39 Passengers • High Capacity</p>
                    </div>
                    <div class="p-6">
                        <div id="layout-3x2" class="mb-4"></div>
                        <div class="text-sm text-gray-600">
                            <p><strong>Configuration:</strong> 3 seats | aisle | 2 seats</p>
                            <p><strong>Back Row:</strong> 6 continuous seats</p>
                            <p><strong>Best for:</strong> Large buses, long-distance routes</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Interactive Demo -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-gray-800 to-gray-900 px-6 py-4">
                    <h3 class="text-xl font-bold text-white">Interactive Layout Builder</h3>
                    <p class="text-gray-300">Customize your bus layout in real-time</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Controls -->
                        <div class="lg:col-span-1">
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Seats</label>
                                    <input type="range" id="totalSeats" min="20" max="50" value="31" 
                                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                                        <span>20</span>
                                        <span id="seatCount">31</span>
                                        <span>50</span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Layout Type</label>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="radio" name="layoutType" value="2x2" checked class="text-blue-600">
                                            <span class="ml-2">2x2 (Standard)</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="layoutType" value="2x1" class="text-blue-600">
                                            <span class="ml-2">2x1 (Compact)</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="layoutType" value="3x2" class="text-blue-600">
                                            <span class="ml-2">3x2 (Large)</span>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" id="hasBackRow" checked class="text-blue-600 rounded">
                                        <span class="ml-2 font-medium">Include Back Row</span>
                                    </label>
                                </div>

                                <button id="generateLayout" 
                                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                                    Generate Layout
                                </button>
                            </div>
                        </div>

                        <!-- Preview -->
                        <div class="lg:col-span-2">
                            <div id="customLayout" class="min-h-96 bg-gray-50 rounded-lg flex items-center justify-center">
                                <p class="text-gray-500">Click "Generate Layout" to see your custom configuration</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900">Key Features</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                                <span class="text-2xl">👨‍✈️</span>
                            </div>
                            <h4 class="font-medium text-gray-900 mb-2">Driver Seat</h4>
                            <p class="text-sm text-gray-600">Positioned at top-right corner as in real buses</p>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                                <span class="text-2xl">🚪</span>
                            </div>
                            <h4 class="font-medium text-gray-900 mb-2">Front Door</h4>
                            <p class="text-sm text-gray-600">Entry door shown at top-left side</p>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                                <span class="text-2xl">🪑</span>
                            </div>
                            <h4 class="font-medium text-gray-900 mb-2">Back Row</h4>
                            <p class="text-sm text-gray-600">Continuous line of seats spanning full width</p>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                                <span class="text-2xl">🎯</span>
                            </div>
                            <h4 class="font-medium text-gray-900 mb-2">Real-time</h4>
                            <p class="text-sm text-gray-600">Live seat availability updates</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Demo layouts
    const demoLayouts = {
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
            seats: generateDemoSeats(31, '2x2')
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
            seats: generateDemoSeats(25, '2x1')
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
            seats: generateDemoSeats(39, '3x2')
        }
    };

    function generateDemoSeats(totalSeats, layoutType) {
        // Simplified seat generation for demo
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
        
        // Generate regular rows
        for (let row = 1; row <= regularRows; row++) {
            const rowLetter = String.fromCharCode(64 + row);
            
            // Left side seats
            for (let i = 1; i <= config.leftSeats && seatNumber <= regularSeats; i++) {
                seats.push({
                    number: rowLetter + i,
                    row: row,
                    column: i,
                    type: 'regular',
                    is_window: i === 1,
                    is_aisle: i === config.leftSeats,
                    is_available: true,
                    side: 'left'
                });
                seatNumber++;
            }
            
            // Right side seats
            for (let i = 1; i <= config.rightSeats && seatNumber <= regularSeats; i++) {
                seats.push({
                    number: rowLetter + (config.leftSeats + i),
                    row: row,
                    column: config.leftSeats + 1 + i,
                    type: 'regular',
                    is_window: i === config.rightSeats,
                    is_aisle: i === 1,
                    is_available: true,
                    side: 'right'
                });
                seatNumber++;
            }
        }
        
        // Generate back row
        const backRowLetter = String.fromCharCode(64 + regularRows + 1);
        for (let i = 1; i <= config.backRowSeats; i++) {
            seats.push({
                number: backRowLetter + i,
                row: regularRows + 1,
                column: i,
                type: 'back_row',
                is_window: i === 1 || i === config.backRowSeats,
                is_aisle: false,
                is_available: true,
                side: 'back'
            });
        }
        
        return seats;
    }

    // Render demo layouts
    document.addEventListener('DOMContentLoaded', function() {
        Object.keys(demoLayouts).forEach(layoutType => {
            const container = document.getElementById(`layout-${layoutType}`);
            if (container) {
                const preview = new SeatLayoutPreview(demoLayouts[layoutType], container);
                preview.render();
            }
        });

        // Interactive demo
        const totalSeatsSlider = document.getElementById('totalSeats');
        const seatCountDisplay = document.getElementById('seatCount');
        const generateBtn = document.getElementById('generateLayout');
        const customContainer = document.getElementById('customLayout');

        totalSeatsSlider.addEventListener('input', function() {
            seatCountDisplay.textContent = this.value;
        });

        generateBtn.addEventListener('click', function() {
            const totalSeats = parseInt(totalSeatsSlider.value);
            const layoutType = document.querySelector('input[name="layoutType"]:checked').value;
            const hasBackRow = document.getElementById('hasBackRow').checked;

            // Generate custom layout (simplified for demo)
            const customLayout = {
                layout_type: layoutType,
                total_seats: totalSeats,
                has_back_row: hasBackRow,
                seats: generateDemoSeats(totalSeats, layoutType)
            };

            // Add layout metadata
            const configs = {
                '2x2': { columns: 5, aisle_position: 2 },
                '2x1': { columns: 4, aisle_position: 2 },
                '3x2': { columns: 6, aisle_position: 3 }
            };
            
            Object.assign(customLayout, configs[layoutType]);
            customLayout.driver_seat = { position: 'top-right', row: 0, column: customLayout.columns };
            customLayout.door = { position: 'top-left', row: 0, column: 0 };

            const preview = new SeatLayoutPreview(customLayout, customContainer);
            preview.render();
        });
    });

    // Seat Layout Preview Class
    class SeatLayoutPreview {
        constructor(layout, container) {
            this.layout = layout;
            this.container = container;
        }

        render() {
            const { layout_type, rows, seats, driver_seat, door, has_back_row } = this.layout;

            let html = '<div class="seat-map-container">';

            // Bus layout container
            html += '<div class="bus-layout-container">';
            html += '<div class="bus-frame">';

            // Top section with driver seat and door
            html += '<div class="bus-top-section">';
            html += '<div class="bus-door" title="Front Door">🚪</div>';
            html += '<div class="bus-front-space"></div>';
            html += '<div class="driver-seat" title="Driver">👨‍✈️</div>';
            html += '</div>';

            // Main seating area
            html += this.renderMainSeatingArea();

            html += '</div></div></div>';

            this.container.innerHTML = html;
        }

        renderMainSeatingArea() {
            const { rows, seats, has_back_row, aisle_position } = this.layout;

            let html = '<div class="main-seating-area">';

            // Group seats by row
            const seatsByRow = {};
            seats.forEach(seat => {
                if (!seatsByRow[seat.row]) {
                    seatsByRow[seat.row] = [];
                }
                seatsByRow[seat.row].push(seat);
            });

            // Render each row
            const maxRow = Math.max(...seats.map(s => s.row));
            for (let rowNum = 1; rowNum <= maxRow; rowNum++) {
                const rowSeats = seatsByRow[rowNum] || [];
                const isBackRow = has_back_row && rowNum === maxRow;

                html += `<div class="seat-row ${isBackRow ? 'back-row' : 'regular-row'}" data-row="${rowNum}">`;

                if (isBackRow) {
                    html += this.renderBackRow(rowSeats);
                } else {
                    html += this.renderRegularRow(rowSeats, aisle_position);
                }

                html += '</div>';
            }

            html += '</div>';
            return html;
        }

        renderRegularRow(rowSeats, aislePosition) {
            let html = '';

            rowSeats.sort((a, b) => a.column - b.column);

            let currentColumn = 1;

            rowSeats.forEach(seat => {
                if (currentColumn === aislePosition + 1) {
                    html += '<div class="aisle-space"></div>';
                }

                const isWindow = seat.is_window ? 'window-seat' : '';

                html += `<div class="seat available ${isWindow}" title="Seat ${seat.number}">
                            ${seat.number}
                         </div>`;

                currentColumn = seat.column + 1;
            });

            return html;
        }

        renderBackRow(rowSeats) {
            let html = '<div class="back-row-container">';

            rowSeats.sort((a, b) => a.column - b.column);

            rowSeats.forEach(seat => {
                html += `<div class="seat available back-row-seat" title="Seat ${seat.number}">
                            ${seat.number}
                         </div>`;
            });

            html += '</div>';
            return html;
        }
    }
    </script>
</body>
</html>
