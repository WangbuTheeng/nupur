<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fef2f2;
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
        .failure-icon {
            color: #ef4444;
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
        .error-details {
            background: #fef2f2;
            border: 1px solid #fecaca;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: left;
        }
        .error-title {
            color: #dc2626;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .error-message {
            color: #7f1d1d;
            font-size: 14px;
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
        .button-retry {
            background: #10b981;
        }
        .button-retry:hover {
            background: #059669;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="failure-icon">✗</div>
        <h1 class="title">Payment Failed</h1>
        <p class="message">Your payment could not be processed.</p>
        
        @if(isset($error_message))
            <div class="error-details">
                <div class="error-title">Error Details</div>
                <div class="error-message">{{ $error_message }}</div>
            </div>
        @endif
        
        @if(isset($payment_id))
            <div class="details">
                <h3 style="margin-top: 0; color: #1f2937;">Transaction Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Payment ID:</span>
                    <span class="detail-value">{{ $payment_id }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date:</span>
                    <span class="detail-value">{{ now()->format('M d, Y H:i:s') }}</span>
                </div>
            </div>
        @endif
        
        <div style="background: #f0f9ff; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
            <p style="margin: 0; color: #1e40af; font-size: 14px;">
                <strong>Don't worry!</strong> No amount has been charged from your account.
            </p>
        </div>
        
        <div>
            <a href="/" class="button button-retry">Try Again</a>
            <a href="/" class="button">Go to Home</a>
            <a href="/login" class="button button-secondary">Login</a>
        </div>
        
        <p style="margin-top: 30px; color: #6b7280; font-size: 14px;">
            If you continue to experience issues, please contact our support team.
        </p>
    </div>
</body>
</html>
