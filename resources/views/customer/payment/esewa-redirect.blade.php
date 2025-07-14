@extends('layouts.app')

@section('title', 'Redirecting to eSewa')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                
                <h2 class="text-lg font-medium text-gray-900 mb-2">Redirecting to eSewa</h2>
                <p class="text-sm text-gray-600 mb-6">
                    You will be redirected to eSewa payment gateway to complete your payment.
                </p>

                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600">Booking ID:</span>
                        <span class="font-medium">#{{ $booking->id }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm mt-2">
                        <span class="text-gray-600">Amount:</span>
                        <span class="font-medium">NPR {{ number_format($booking->total_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm mt-2">
                        <span class="text-gray-600">Payment Method:</span>
                        <span class="font-medium">eSewa</span>
                    </div>
                </div>

                <div class="text-xs text-gray-500 mb-4">
                    <p>If you are not redirected automatically, click the button below:</p>
                </div>

                <!-- eSewa Payment Form -->
                <div id="esewa-form-container">
                    {!! $form_html !!}
                </div>

                <div class="mt-4 text-xs text-gray-500">
                    <p>Secure payment powered by eSewa</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-submit the form after a short delay
    setTimeout(function() {
        const form = document.getElementById('esewa-payment-form');
        if (form) {
            form.submit();
        }
    }, 2000);

    // Show loading state
    document.addEventListener('DOMContentLoaded', function() {
        const button = document.querySelector('#esewa-payment-form button');
        if (button) {
            button.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Redirecting to eSewa...';
            button.disabled = true;
            button.className = 'w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50';
        }
    });
</script>
@endsection
