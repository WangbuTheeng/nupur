@extends('layouts.app')

@section('title', 'Redirecting to eSewa')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center py-12">
        <!-- Loading Animation -->
        <div class="mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                <svg class="animate-spin w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Redirecting to eSewa</h1>
            <p class="text-gray-600">Please wait while we redirect you to eSewa for secure payment...</p>
        </div>

        <!-- Payment Details -->
        <div class="bg-white shadow-lg rounded-xl p-6 mb-8 text-left">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Details</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Booking Reference:</span>
                    <span class="font-medium">{{ $booking->booking_reference }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Route:</span>
                    <span class="font-medium">{{ $booking->schedule->route->full_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Amount:</span>
                    <span class="font-bold text-green-600">NRs {{ number_format($booking->total_amount) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Payment Method:</span>
                    <span class="font-medium">eSewa</span>
                </div>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                <div class="text-left">
                    <h4 class="text-sm font-medium text-blue-900">Secure Payment</h4>
                    <p class="text-sm text-blue-700 mt-1">
                        You will be redirected to eSewa's secure payment gateway. Please complete your payment there and you will be redirected back to BookNGO.
                    </p>
                </div>
            </div>
        </div>

        <!-- Manual Redirect Button (fallback) -->
        <div id="manual-redirect" class="hidden">
            <p class="text-gray-600 mb-4">If you are not redirected automatically, click the button below:</p>
            <button onclick="submitPaymentForm()" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-200">
                Continue to eSewa
            </button>
        </div>

        <!-- Cancel Payment -->
        <div class="mt-8">
            <a href="{{ route('payment.options', $booking) }}" 
               class="text-gray-500 hover:text-gray-700 text-sm underline">
                Cancel Payment
            </a>
        </div>
    </div>

    <!-- Hidden eSewa Payment Form -->
    <div id="esewa-form-container" class="hidden">
        {!! $form_html !!}
    </div>
</div>

@push('scripts')
<script>
let redirectAttempted = false;

function submitPaymentForm() {
    if (redirectAttempted) return;
    
    redirectAttempted = true;
    const form = document.getElementById('esewa-payment-form');
    if (form) {
        form.submit();
    } else {
        console.error('eSewa payment form not found');
        showManualRedirect();
    }
}

function showManualRedirect() {
    document.getElementById('manual-redirect').classList.remove('hidden');
}

// Auto-submit form after 2 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        submitPaymentForm();
    }, 2000);
    
    // Show manual redirect option after 10 seconds if auto-redirect fails
    setTimeout(function() {
        if (!redirectAttempted) {
            showManualRedirect();
        }
    }, 10000);
});

// Handle page visibility change (user comes back from payment)
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        // User came back to the page, check payment status
        setTimeout(function() {
            window.location.href = '{{ route("bookings.show", $booking) }}';
        }, 2000);
    }
});
</script>
@endpush

@endsection
