<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Seat Colors</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/seat-map.css') }}">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-center">Seat Color Status Test</h1>
        
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Color Requirements</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-green-500 rounded"></div>
                    <span>Green = Available</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-yellow-500 rounded"></div>
                    <span>Yellow = Selected</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-red-500 rounded"></div>
                    <span>Red = Booked</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-gray-500 rounded"></div>
                    <span>Grey = Reserved</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Actual Seat Colors</h2>
            <div class="seat-map-container">
                <div class="seat-legend">
                    <div class="legend-item"><span class="seat available"></span> Available</div>
                    <div class="legend-item"><span class="seat selected"></span> Selected</div>
                    <div class="legend-item"><span class="seat booked"></span> Booked</div>
                    <div class="legend-item"><span class="seat reserved"></span> Reserved</div>
                </div>
                
                <div class="bus-layout-container">
                    <div class="bus-frame">
                        <!-- Top section with driver seat and door -->
                        <div class="bus-top-section">
                            <div class="bus-door" title="Front Door">🚪</div>
                            <div class="bus-front-space"></div>
                            <div class="driver-seat" title="Driver">👨‍✈️</div>
                        </div>
                        
                        <!-- Test seats with different statuses -->
                        <div class="main-seating-area">
                            <div class="seat-row regular-row">
                                <div class="seat available" title="Available Seat">A1</div>
                                <div class="seat available" title="Available Seat">A2</div>
                                <div class="aisle-space"></div>
                                <div class="seat selected" title="Selected Seat">A3</div>
                                <div class="seat selected" title="Selected Seat">A4</div>
                            </div>
                            
                            <div class="seat-row regular-row">
                                <div class="seat booked" title="Booked Seat">B1</div>
                                <div class="seat booked" title="Booked Seat">B2</div>
                                <div class="aisle-space"></div>
                                <div class="seat reserved" title="Reserved Seat">B3</div>
                                <div class="seat reserved" title="Reserved Seat">B4</div>
                            </div>
                            
                            <div class="seat-row regular-row">
                                <div class="seat available window-seat" title="Available Window Seat">C1</div>
                                <div class="seat selected aisle-seat" title="Selected Aisle Seat">C2</div>
                                <div class="aisle-space"></div>
                                <div class="seat booked aisle-seat" title="Booked Aisle Seat">C3</div>
                                <div class="seat reserved window-seat" title="Reserved Window Seat">C4</div>
                            </div>
                            
                            <!-- Back row -->
                            <div class="seat-row back-row">
                                <div class="back-row-container">
                                    <div class="seat available back-row-seat" title="Back Row Available">D1</div>
                                    <div class="seat selected back-row-seat" title="Back Row Selected">D2</div>
                                    <div class="seat booked back-row-seat" title="Back Row Booked">D3</div>
                                    <div class="seat reserved back-row-seat" title="Back Row Reserved">D4</div>
                                    <div class="seat available back-row-seat" title="Back Row Available">D5</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 text-sm text-gray-600">
                <p><strong>Test Results:</strong></p>
                <ul class="list-disc list-inside mt-2 space-y-1">
                    <li>Available seats should be <strong>Green</strong> (#10b981)</li>
                    <li>Selected seats should be <strong>Yellow</strong> (#eab308)</li>
                    <li>Booked seats should be <strong>Red</strong> (#ef4444)</li>
                    <li>Reserved seats should be <strong>Grey</strong> (#6b7280)</li>
                    <li>All seats should have proper hover effects</li>
                    <li>Booked and Reserved seats should have cursor: not-allowed</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
