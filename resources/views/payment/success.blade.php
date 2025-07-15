<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <div class="mx-auto h-24 w-24 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-4xl text-green-600"></i>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Payment Successful!
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    {{ $message ?? 'Your payment has been processed successfully.' }}
                </p>
            </div>

            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="space-y-4">
                    @if(isset($payment))
                        <div class="border-b pb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Payment Details</h3>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Payment ID:</span>
                                    <span class="font-medium">#{{ $payment->id }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Amount:</span>
                                    <span class="font-medium">NPR {{ number_format($payment->amount, 2) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Transaction ID:</span>
                                    <span class="font-medium">{{ $payment->transaction_id }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Status:</span>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if(isset($booking))
                            <div class="border-b pb-4">
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Booking Details</h3>
                                <div class="grid grid-cols-1 gap-2 text-sm">
                                    <div>
                                        <span class="text-gray-500">Booking Reference:</span>
                                        <span class="font-medium">{{ $booking->booking_reference }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Route:</span>
                                        <span class="font-medium">{{ $booking->schedule->route->origin }} → {{ $booking->schedule->route->destination }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Date:</span>
                                        <span class="font-medium">{{ $booking->schedule->departure_date->format('M d, Y') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Seats:</span>
                                        <span class="font-medium">{{ implode(', ', $booking->selected_seats) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    <div class="text-center space-y-3">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            A confirmation email has been sent to your registered email address.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            @auth
                                <a href="{{ route('dashboard') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-tachometer-alt mr-2"></i>
                                    Go to Dashboard
                                </a>
                                @if(isset($booking))
                                    <a href="{{ route('bookings.show', $booking) }}"
                                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-ticket-alt mr-2"></i>
                                        View Booking
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('home') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-home mr-2"></i>
                                    Go to Home
                                </a>
                                <a href="{{ route('login') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-sign-in-alt mr-2"></i>
                                    Login to View Details
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <p class="text-xs text-gray-500">
                    If you have any questions, please contact our support team.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
