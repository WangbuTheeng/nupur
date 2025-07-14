# eSewa Payment Integration (v2 API)

This document describes the updated eSewa payment integration using the v2 API as specified in the testpaymentintegration.md file.

## Overview

The eSewa integration has been updated to use the new v2 API which includes:
- HMAC SHA256 signature generation
- Base64 encoded response handling
- Status check API for payment verification
- Updated parameter structure

## Configuration

### Environment Variables

Add the following to your `.env` file:

```env
# eSewa Payment Gateway
ESEWA_MERCHANT_ID=EPAYTEST
ESEWA_SECRET_KEY="8gBm/:&EnhH.1/q"
ESEWA_BASE_URL=https://rc-epay.esewa.com.np
ESEWA_PAYMENT_URL=https://rc-epay.esewa.com.np/api/epay/main/v2/form
ESEWA_STATUS_CHECK_URL=https://rc.esewa.com.np/api/epay/transaction/status/
ESEWA_SUCCESS_URL="${APP_URL}/payment/esewa/success"
ESEWA_FAILURE_URL="${APP_URL}/payment/esewa/failure"
```

### Test Credentials

For testing, use the following credentials:
- **eSewa ID**: 9806800001/2/3/4/5
- **Password**: Nepal@123
- **MPIN**: 1122
- **Token**: 123456
- **Merchant ID**: EPAYTEST
- **Secret Key**: 8gBm/:&EnhH.1/q

## API Changes

### Payment Initiation

The new v2 API uses different parameters:

**Old Parameters (v1):**
- `amt` → `amount`
- `pdc` → `product_delivery_charge`
- `psc` → `product_service_charge`
- `txAmt` → `tax_amount`
- `tAmt` → `total_amount`
- `pid` → `transaction_uuid`
- `scd` → `product_code`
- `su` → `success_url`
- `fu` → `failure_url`

**New Parameters (v2):**
- `amount`: Product amount
- `tax_amount`: Tax amount
- `product_service_charge`: Service charge
- `product_delivery_charge`: Delivery charge
- `total_amount`: Sum of all above amounts
- `transaction_uuid`: Unique transaction identifier
- `product_code`: Merchant code (EPAYTEST for testing)
- `success_url`: Success callback URL
- `failure_url`: Failure callback URL
- `signed_field_names`: Fields used for signature generation
- `signature`: HMAC SHA256 signature

### Signature Generation

The signature is generated using HMAC SHA256:

1. Create message from signed field names: `total_amount,transaction_uuid,product_code`
2. Generate HMAC SHA256 hash using the secret key
3. Encode the result in Base64

Example:
```php
$message = $totalAmount . ',' . $transactionUuid . ',' . $productCode;
$signature = base64_encode(hash_hmac('sha256', $message, $secretKey, true));
```

### Response Handling

The success callback now receives Base64 encoded JSON data:

```php
// Decode the response
$decodedData = base64_decode($encodedData);
$responseData = json_decode($decodedData, true);

// Verify signature
$isValid = $this->verifyResponseSignature($responseData);
```

## Usage

### Initiating Payment

```php
use App\Services\EsewaPaymentService;

$esewaService = new EsewaPaymentService();
$result = $esewaService->initiatePayment($booking);

if ($result['success']) {
    // Display the payment form
    return view('payments.esewa-redirect', [
        'booking' => $booking,
        'payment' => $result['payment'],
        'form_html' => $result['form_html']
    ]);
}
```

### Verifying Payment

```php
// In the success callback
$result = $esewaService->verifyPayment($paymentId, $encodedData);

if ($result['success']) {
    // Payment verified successfully
    $payment = $result['payment'];
    $booking = $payment->booking;
    // Redirect to success page
}
```

### Status Check

```php
// Check payment status using the status check API
$result = $esewaService->checkPaymentStatus($transactionUuid, $totalAmount);

if ($result['success'] && $result['status'] === 'COMPLETE') {
    // Payment is complete
}
```

## Routes

The following routes are available:

- `POST /payment/{booking}/esewa` - Initiate eSewa payment
- `GET /payment/esewa/success` - Success callback
- `GET /payment/esewa/failure` - Failure callback
- `GET /payment/{payment}/esewa/check-status` - Check payment status

## Testing

Run the eSewa payment tests:

```bash
php artisan test tests/Feature/EsewaPaymentTest.php
```

## Production Setup

For production:

1. Update the URLs to production endpoints:
   - Payment URL: `https://epay.esewa.com.np/api/epay/main/v2/form`
   - Status Check URL: `https://epay.esewa.com.np/api/epay/transaction/status/`

2. Use live credentials provided by eSewa

3. Update the merchant ID and secret key

4. Ensure GD extension is enabled for QR code generation:
   ```bash
   # Check if GD is enabled
   php -m | grep -i gd

   # If not enabled, install/enable it
   # Ubuntu/Debian: sudo apt-get install php-gd
   # CentOS/RHEL: sudo yum install php-gd
   # Windows XAMPP: Uncomment extension=gd in php.ini
   ```

## Security Notes

- Always verify the response signature
- Use HTTPS for all callbacks
- Store sensitive credentials securely
- Implement proper error handling
- Log all payment transactions for audit purposes

## Troubleshooting

### Common Issues

1. **Invalid Payload Signature**:
   - Check that the message format is correct: `total_amount,transaction_uuid,product_code`
   - Ensure all values are converted to strings
   - Verify the secret key is correct
   - Check the logs for the exact message being signed

2. **Invalid Response**: Check that the Base64 decoding is working correctly

3. **Payment Not Found**: Use the status check API to verify payment status

4. **Timeout Issues**: Implement proper timeout handling for API calls

### Debug Mode

Enable debug logging in your `.env`:

```env
LOG_LEVEL=debug
```

This will log all eSewa API interactions including:
- The exact message being signed
- The generated signature
- Payment data being sent
- Response verification details

### Signature Debugging

If you're getting "Invalid payload signature" errors, check the Laravel logs for entries like:
- `eSewa Signature Generation`
- `eSewa Signature Generated`
- `eSewa Payment Data`

These will show you exactly what's being sent to eSewa for debugging purposes.
