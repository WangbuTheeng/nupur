<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Seat Layout</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/seat-map.css') }}">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-center">Seat Layout Test</h1>
        
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-4">2x2 Layout (31 seats)</h2>
            <div id="testLayout" class="mb-8"></div>
            
            <div class="text-sm text-gray-600">
                <p><strong>Expected:</strong> Bus frame with driver seat (top-right), door (top-left), and proper seat arrangement</p>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/realtime-seat-map.js') }}"></script>
    <script>
        // Test layout data
        const testSeatLayout = {
            "layout_type": "2x2",
            "total_seats": 31,
            "rows": 7,
            "columns": 5,
            "aisle_position": 2,
            "has_back_row": true,
            "back_row_seats": 5,
            "driver_seat": {
                "position": "top-right",
                "row": 0,
                "column": 5
            },
            "door": {
                "position": "top-left",
                "row": 0,
                "column": 0
            },
            "seats": [
                {"number": "A1", "row": 1, "column": 1, "type": "regular", "is_window": true, "is_aisle": false, "is_available": true, "side": "left"},
                {"number": "A2", "row": 1, "column": 2, "type": "regular", "is_window": false, "is_aisle": true, "is_available": true, "side": "left"},
                {"number": "A3", "row": 1, "column": 4, "type": "regular", "is_window": false, "is_aisle": true, "is_available": true, "side": "right"},
                {"number": "A4", "row": 1, "column": 5, "type": "regular", "is_window": true, "is_aisle": false, "is_available": true, "side": "right"},
                {"number": "B1", "row": 2, "column": 1, "type": "regular", "is_window": true, "is_aisle": false, "is_available": true, "side": "left"},
                {"number": "B2", "row": 2, "column": 2, "type": "regular", "is_window": false, "is_aisle": true, "is_available": true, "side": "left"},
                {"number": "B3", "row": 2, "column": 4, "type": "regular", "is_window": false, "is_aisle": true, "is_available": true, "side": "right"},
                {"number": "B4", "row": 2, "column": 5, "type": "regular", "is_window": true, "is_aisle": false, "is_available": true, "side": "right"},
                {"number": "C1", "row": 3, "column": 1, "type": "regular", "is_window": true, "is_aisle": false, "is_available": true, "side": "left"},
                {"number": "C2", "row": 3, "column": 2, "type": "regular", "is_window": false, "is_aisle": true, "is_available": true, "side": "left"},
                {"number": "C3", "row": 3, "column": 4, "type": "regular", "is_window": false, "is_aisle": true, "is_available": true, "side": "right"},
                {"number": "C4", "row": 3, "column": 5, "type": "regular", "is_window": true, "is_aisle": false, "is_available": true, "side": "right"},
                {"number": "D1", "row": 4, "column": 1, "type": "regular", "is_window": true, "is_aisle": false, "is_available": true, "side": "left"},
                {"number": "D2", "row": 4, "column": 2, "type": "regular", "is_window": false, "is_aisle": true, "is_available": true, "side": "left"},
                {"number": "D3", "row": 4, "column": 4, "type": "regular", "is_window": false, "is_aisle": true, "is_available": true, "side": "right"},
                {"number": "D4", "row": 4, "column": 5, "type": "regular", "is_window": true, "is_aisle": false, "is_available": true, "side": "right"},
                {"number": "E1", "row": 5, "column": 1, "type": "regular", "is_window": true, "is_aisle": false, "is_available": true, "side": "left"},
                {"number": "E2", "row": 5, "column": 2, "type": "regular", "is_window": false, "is_aisle": true, "is_available": true, "side": "left"},
                {"number": "E3", "row": 5, "column": 4, "type": "regular", "is_window": false, "is_aisle": true, "is_available": true, "side": "right"},
                {"number": "E4", "row": 5, "column": 5, "type": "regular", "is_window": true, "is_aisle": false, "is_available": true, "side": "right"},
                {"number": "F1", "row": 6, "column": 1, "type": "regular", "is_window": true, "is_aisle": false, "is_available": true, "side": "left"},
                {"number": "F2", "row": 6, "column": 2, "type": "regular", "is_window": false, "is_aisle": true, "is_available": true, "side": "left"},
                {"number": "F3", "row": 6, "column": 4, "type": "regular", "is_window": false, "is_aisle": true, "is_available": true, "side": "right"},
                {"number": "F4", "row": 6, "column": 5, "type": "regular", "is_window": true, "is_aisle": false, "is_available": true, "side": "right"},
                {"number": "G1", "row": 7, "column": 1, "type": "back_row", "is_window": true, "is_aisle": false, "is_available": true, "side": "back"},
                {"number": "G2", "row": 7, "column": 2, "type": "back_row", "is_window": false, "is_aisle": false, "is_available": true, "side": "back"},
                {"number": "G3", "row": 7, "column": 3, "type": "back_row", "is_window": false, "is_aisle": false, "is_available": true, "side": "back"},
                {"number": "G4", "row": 7, "column": 4, "type": "back_row", "is_window": false, "is_aisle": false, "is_available": true, "side": "back"},
                {"number": "G5", "row": 7, "column": 5, "type": "back_row", "is_window": true, "is_aisle": false, "is_available": true, "side": "back"}
            ]
        };

        document.addEventListener('DOMContentLoaded', function() {
            console.log('Testing seat layout rendering...');
            console.log('SeatLayoutPreview available:', typeof SeatLayoutPreview !== 'undefined');
            
            const container = document.getElementById('testLayout');
            
            if (typeof SeatLayoutPreview !== 'undefined') {
                try {
                    const preview = new SeatLayoutPreview(testSeatLayout, container);
                    preview.render();
                    console.log('Test layout rendered successfully!');
                } catch (error) {
                    console.error('Error rendering test layout:', error);
                    container.innerHTML = '<div class="text-red-600 p-4">Error: ' + error.message + '</div>';
                }
            } else {
                container.innerHTML = '<div class="text-red-600 p-4">SeatLayoutPreview class not found</div>';
            }
        });
    </script>
</body>
</html>
