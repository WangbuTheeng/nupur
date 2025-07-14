<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar Test</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    @include('layouts.navigation')
    
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Navbar Functionality Test</h1>
            
            <div class="space-y-4">
                <div class="p-4 bg-blue-50 rounded-lg">
                    <h2 class="font-semibold text-blue-800">Desktop Features to Test:</h2>
                    <ul class="list-disc list-inside text-blue-700 mt-2">
                        <li>Click the notification bell icon - dropdown should appear</li>
                        <li>Click the user profile button - dropdown should appear</li>
                        <li>Click outside dropdowns - they should close</li>
                        <li>Press ESC key - all dropdowns should close</li>
                        <li>Only one dropdown should be open at a time</li>
                    </ul>
                </div>
                
                <div class="p-4 bg-green-50 rounded-lg">
                    <h2 class="font-semibold text-green-800">Mobile Features to Test:</h2>
                    <ul class="list-disc list-inside text-green-700 mt-2">
                        <li>Resize window to mobile size (< 1024px)</li>
                        <li>Click the hamburger menu button - mobile menu should slide down</li>
                        <li>Click the backdrop - mobile menu should close</li>
                        <li>Navigation links should be properly spaced and touchable</li>
                        <li>User profile section should appear at bottom of mobile menu</li>
                    </ul>
                </div>
                
                <div class="p-4 bg-yellow-50 rounded-lg">
                    <h2 class="font-semibold text-yellow-800">Responsive Features:</h2>
                    <ul class="list-disc list-inside text-yellow-700 mt-2">
                        <li>Dropdowns should not overflow screen edges</li>
                        <li>Mobile menu should have smooth animations</li>
                        <li>Touch targets should be at least 44px for mobile</li>
                        <li>Logo should scale appropriately on different screen sizes</li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h2 class="font-semibold text-gray-800">Console Debug Info:</h2>
                <p class="text-gray-600 text-sm">Open browser developer tools (F12) to see Alpine.js debug messages and verify component initialization.</p>
            </div>
        </div>
    </div>
    
    <script>
        // Additional debug information
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🧪 Navbar test page loaded');
            console.log('🧪 Alpine available:', typeof Alpine !== 'undefined');
            console.log('🧪 Window width:', window.innerWidth);
            
            // Test responsive behavior
            window.addEventListener('resize', function() {
                console.log('🧪 Window resized to:', window.innerWidth);
            });
        });
    </script>
</body>
</html>
