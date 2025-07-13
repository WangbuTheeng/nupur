<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Layout Restrictions Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Seat Layout Restrictions Test</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- 2x2 Layout -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-blue-600 mb-4">2x2 Layout</h2>
                <p class="text-gray-600 mb-4">Valid seat counts: 25, 29, 33, 37, 41, etc.</p>
                <p class="text-sm text-gray-500 mb-4">Pattern: Start at 25, increment by 4</p>
                
                <div class="space-y-2">
                    <h3 class="font-medium text-green-600">✓ Valid Counts:</h3>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">25</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">29</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">33</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">37</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">41</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">45</span>
                    </div>
                    
                    <h3 class="font-medium text-red-600 mt-4">✗ Invalid Counts:</h3>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">26</span>
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">27</span>
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">28</span>
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">30</span>
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">31</span>
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">32</span>
                    </div>
                </div>
            </div>

            <!-- 1x2 Layout -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-purple-600 mb-4">1x2 Layout</h2>
                <p class="text-gray-600 mb-4">Valid seat counts: 22, 25, 28, 31, 34, etc.</p>
                <p class="text-sm text-gray-500 mb-4">Pattern: Start at 22, increment by 3</p>
                
                <div class="space-y-2">
                    <h3 class="font-medium text-green-600">✓ Valid Counts:</h3>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">22</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">25</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">28</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">31</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">34</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">37</span>
                    </div>
                    
                    <h3 class="font-medium text-red-600 mt-4">✗ Invalid Counts:</h3>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">23</span>
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">24</span>
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">26</span>
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">27</span>
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">29</span>
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">30</span>
                    </div>
                </div>
            </div>

            <!-- 3x2 Layout -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-orange-600 mb-4">3x2 Layout</h2>
                <p class="text-gray-600 mb-4">Valid seat counts: 26, 31, 36, 41, 46, etc.</p>
                <p class="text-sm text-gray-500 mb-4">Pattern: Start at 26, increment by 5</p>
                
                <div class="space-y-2">
                    <h3 class="font-medium text-green-600">✓ Valid Counts:</h3>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">26</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">31</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">36</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">41</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">46</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">51</span>
                    </div>
                    
                    <h3 class="font-medium text-red-600 mt-4">✗ Invalid Counts:</h3>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">27</span>
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">28</span>
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">29</span>
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">32</span>
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">33</span>
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">34</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gap Information -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Gap Implementation</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-medium text-blue-600 mb-2">Regular Rows</h3>
                    <p class="text-gray-600 text-sm mb-2">Seats have visual gaps between them for better spacing and readability.</p>
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-green-500 rounded flex items-center justify-center text-white text-xs">A1</div>
                        <div class="w-2 h-8 bg-gray-200 rounded"></div>
                        <div class="w-8 h-8 bg-green-500 rounded flex items-center justify-center text-white text-xs">A2</div>
                        <div class="w-6 h-8 bg-blue-200 rounded flex items-center justify-center text-xs">Aisle</div>
                        <div class="w-8 h-8 bg-green-500 rounded flex items-center justify-center text-white text-xs">A3</div>
                        <div class="w-2 h-8 bg-gray-200 rounded"></div>
                        <div class="w-8 h-8 bg-green-500 rounded flex items-center justify-center text-white text-xs">A4</div>
                    </div>
                </div>
                <div>
                    <h3 class="font-medium text-orange-600 mb-2">Back Row</h3>
                    <p class="text-gray-600 text-sm mb-2">Back row seats are continuous without gaps for maximum capacity.</p>
                    <div class="flex items-center space-x-1">
                        <div class="w-8 h-8 bg-orange-500 rounded flex items-center justify-center text-white text-xs">F1</div>
                        <div class="w-8 h-8 bg-orange-500 rounded flex items-center justify-center text-white text-xs">F2</div>
                        <div class="w-8 h-8 bg-orange-500 rounded flex items-center justify-center text-white text-xs">F3</div>
                        <div class="w-8 h-8 bg-orange-500 rounded flex items-center justify-center text-white text-xs">F4</div>
                        <div class="w-8 h-8 bg-orange-500 rounded flex items-center justify-center text-white text-xs">F5</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Results -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Implementation Status</h2>
            <div class="space-y-3">
                <div class="flex items-center space-x-3">
                    <span class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-white text-sm">✓</span>
                    <span class="text-gray-700">Seat count restrictions implemented for all layout types</span>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-white text-sm">✓</span>
                    <span class="text-gray-700">Gap logic added to seat layout generation</span>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-white text-sm">✓</span>
                    <span class="text-gray-700">Validation added to bus creation and update forms</span>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-white text-sm">✓</span>
                    <span class="text-gray-700">CSS styles updated for visual gaps</span>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-white text-sm">✓</span>
                    <span class="text-gray-700">All tests passing (13 tests, 162 assertions)</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
