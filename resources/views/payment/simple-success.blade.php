<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f9ff;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .success-icon {
            color: #10b981;
            font-size: 64px;
            margin-bottom: 20px;
        }
        .title {
            color: #1f2937;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .message {
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 30px;
        }
        .details {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: left;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .detail-label {
            color: #6b7280;
            font-weight: 500;
        }
        .detail-value {
            color: #1f2937;
            font-weight: 600;
        }
        .button {
            background: #3b82f6;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            margin: 0 10px;
        }
        .button:hover {
            background: #2563eb;
        }
        .button-secondary {
            background: #6b7280;
        }
        .button-secondary:hover {
            background: #4b5563;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✓</div>
        <h1 class="title">Payment Successful!</h1>
        <p class="message">{{ $message ?? 'Your payment has been processed successfully.' }}</p>
        
        @if(isset($payment_id) || isset($encoded_data))
            <div class="details">
                <h3 style="margin-top: 0; color: #1f2937;">Payment Details</h3>
                @if(isset($payment_id))
                    <div class="detail-row">
                        <span class="detail-label">Payment ID:</span>
                        <span class="detail-value">{{ $payment_id }}</span>
                    </div>
                @endif
                @if(isset($encoded_data))
                    <div class="detail-row">
                        <span class="detail-label">Transaction Data:</span>
                        <span class="detail-value">{{ $encoded_data ? 'Verified' : 'Pending' }}</span>
                    </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">Date:</span>
                    <span class="detail-value">{{ now()->format('M d, Y H:i:s') }}</span>
                </div>
            </div>
        @endif
        
        <div>
            <a href="/" class="button">Go to Home</a>
            <a href="/login" class="button button-secondary">Login</a>
        </div>
        
        <p style="margin-top: 30px; color: #6b7280; font-size: 14px;">
            If you have any questions, please contact our support team.
        </p>
    </div>
</body>
</html>
