# Ticket System Documentation

## Overview

The ticket system allows customers to view, download, and manage their bus tickets after successful booking and payment.

## Current Features

### Ticket Display
- **Digital Ticket View**: Customers can view their tickets online
- **PDF Download**: Tickets can be downloaded as PDF files
- **Email Delivery**: Tickets can be sent via email
- **Booking Reference**: Each ticket shows a unique booking reference for verification

### Ticket Information
Each ticket displays:
- Passenger details
- Journey information (route, date, time)
- Bus details (number, type, operator)
- Seat numbers
- Booking reference
- Payment status

### Verification
- **Booking Reference**: Primary method for ticket verification
- **Manual Verification**: Conductors can verify tickets using booking reference
- **Ticket Status**: Shows confirmed/pending status

## Routes

### Customer Routes (Authenticated)
- `GET /tickets/{booking}` - Show ticket details
- `GET /tickets/{booking}/view` - Detailed ticket view
- `GET /tickets/{booking}/download` - Download PDF ticket
- `GET /tickets/{booking}/email` - Email ticket form
- `POST /tickets/{booking}/email` - Send ticket via email
- `GET /tickets/{booking}/qr` - QR code placeholder (future implementation)

### Public Routes
- `GET /verify-ticket` - Ticket verification form
- `POST /verify-ticket` - Verify ticket by booking reference
- `POST /verify-ticket/manual` - Manual verification by conductor

## Controllers

### Customer\TicketController
Handles authenticated customer ticket operations:
- `show()` - Display ticket summary
- `view()` - Detailed ticket view
- `download()` - Generate and download PDF
- `email()` - Show email form
- `sendEmail()` - Send ticket via email
- `qrCode()` - Placeholder for future QR implementation

### TicketController
Handles public ticket verification:
- `showVerifyForm()` - Display verification form
- `verify()` - Verify ticket by reference
- `verifyManual()` - Manual verification process

## Templates

### Ticket Views
- `customer/tickets/show.blade.php` - Customer ticket summary
- `tickets/view.blade.php` - Detailed ticket view
- `customer/tickets/pdf.blade.php` - PDF template
- `tickets/template.blade.php` - General ticket template
- `tickets/verify.blade.php` - Verification form

## Security

### Access Control
- Customers can only view their own tickets
- Booking ownership is verified before displaying tickets
- Unauthorized access returns 403 errors

### Verification
- Booking references are unique and secure
- Manual verification requires conductor authentication
- Ticket status is validated before display

## Future Enhancements

### QR Code Integration
- **QR Code Generation**: Generate QR codes for tickets
- **Mobile Scanning**: Allow conductors to scan QR codes
- **Offline Verification**: QR codes work without internet
- **Enhanced Security**: Encrypted QR data with timestamps

### Additional Features
- **Real-time Updates**: Live ticket status updates
- **Seat Maps**: Visual seat selection display
- **Journey Tracking**: Real-time bus location
- **Digital Wallet**: Integration with mobile wallets

## Implementation Notes

### Current Limitations
- QR code functionality is disabled (will be implemented later)
- Verification relies on booking reference only
- No real-time status updates

### Dependencies
- Laravel PDF generation (barryvdh/laravel-dompdf)
- Email system for ticket delivery
- Authentication system for customer access

### Configuration
No special configuration required. The ticket system uses:
- Default Laravel mail configuration
- PDF generation settings
- Standard authentication middleware

## Usage Examples

### Viewing a Ticket
```php
// Customer views their ticket
Route::get('/tickets/{booking}', [TicketController::class, 'show']);
```

### Downloading PDF
```php
// Generate PDF ticket
$pdf = Pdf::loadView('customer.tickets.pdf', compact('booking'));
return $pdf->download('ticket-' . $booking->booking_reference . '.pdf');
```

### Email Delivery
```php
// Send ticket via email
Mail::to($booking->user->email)->send(new TicketMail($booking, $pdf));
```

### Verification
```php
// Verify ticket by booking reference
$booking = Booking::where('booking_reference', $reference)->first();
```

## Troubleshooting

### Common Issues
1. **PDF Generation Fails**: Check dompdf configuration
2. **Email Not Sent**: Verify mail configuration
3. **Access Denied**: Check user authentication and booking ownership
4. **Ticket Not Found**: Verify booking reference format

### Debug Tips
- Check Laravel logs for PDF generation errors
- Verify email queue processing
- Ensure booking status is 'confirmed'
- Check user permissions and authentication
