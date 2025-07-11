<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    /**
     * Show e-ticket for booking.
     */
    public function show(Booking $booking)
    {
        // Ensure user can only view their own tickets
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to ticket.');
        }

        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Ticket is only available for confirmed bookings.');
        }

        $booking->load([
            'schedule.route.sourceCity',
            'schedule.route.destinationCity',
            'schedule.bus.busType',
            'schedule.operator'
        ]);

        // Generate QR code for ticket verification
        $qrCodeData = $this->generateQrCodeData($booking);
        $qrCodeImage = $this->generateQrCode($qrCodeData);

        return view('customer.tickets.show', compact('booking', 'qrCodeImage'));
    }

    /**
     * Download e-ticket as PDF.
     */
    public function download(Booking $booking)
    {
        // Ensure user can only download their own tickets
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to ticket.');
        }

        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Ticket is only available for confirmed bookings.');
        }

        $booking->load([
            'schedule.route.sourceCity',
            'schedule.route.destinationCity',
            'schedule.bus.busType',
            'schedule.operator'
        ]);

        // Generate QR code
        $qrCodeData = $this->generateQrCodeData($booking);
        $qrCodeImage = $this->generateQrCode($qrCodeData);

        // Generate PDF
        $pdf = Pdf::loadView('customer.tickets.pdf', compact('booking', 'qrCodeImage'));
        $pdf->setPaper('A4', 'portrait');

        $filename = 'BookNGO-Ticket-' . $booking->booking_reference . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Show email ticket form.
     */
    public function email(Booking $booking)
    {
        // Ensure user can only email their own tickets
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to ticket.');
        }

        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Ticket is only available for confirmed bookings.');
        }

        return view('customer.tickets.email', compact('booking'));
    }

    /**
     * Send e-ticket via email.
     */
    public function sendEmail(Request $request, Booking $booking)
    {
        // Ensure user can only email their own tickets
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to ticket.');
        }

        $request->validate([
            'email' => 'required|email|max:255',
            'message' => 'nullable|string|max:500',
        ]);

        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Ticket is only available for confirmed bookings.');
        }

        try {
            $booking->load([
                'schedule.route.sourceCity',
                'schedule.route.destinationCity',
                'schedule.bus.busType',
                'schedule.operator'
            ]);

            // Generate QR code and PDF
            $qrCodeData = $this->generateQrCodeData($booking);
            $qrCodeImage = $this->generateQrCode($qrCodeData);

            $pdf = Pdf::loadView('customer.tickets.pdf', compact('booking', 'qrCodeImage'));
            $pdf->setPaper('A4', 'portrait');

            // Send email with PDF attachment
            Mail::send('customer.emails.ticket', [
                'booking' => $booking,
                'message' => $request->message,
            ], function ($mail) use ($booking, $request, $pdf) {
                $mail->to($request->email)
                     ->subject('BookNGO E-Ticket - ' . $booking->booking_reference)
                     ->attachData(
                         $pdf->output(),
                         'BookNGO-Ticket-' . $booking->booking_reference . '.pdf',
                         ['mime' => 'application/pdf']
                     );
            });

            return back()->with('success', 'E-ticket sent successfully to ' . $request->email);

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send e-ticket. Please try again.');
        }
    }

    /**
     * Generate QR code for ticket verification.
     */
    public function qrCode(Booking $booking)
    {
        // Ensure user can only view their own QR codes
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to QR code.');
        }

        $qrCodeData = $this->generateQrCodeData($booking);
        $qrCodeImage = $this->generateQrCode($qrCodeData);

        return response($qrCodeImage)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'inline; filename="qr-code.png"');
    }

    /**
     * Generate QR code data for booking verification.
     */
    private function generateQrCodeData(Booking $booking): string
    {
        return json_encode([
            'booking_reference' => $booking->booking_reference,
            'passenger_count' => $booking->passenger_count,
            'seat_numbers' => $booking->seat_numbers,
            'travel_date' => $booking->schedule->travel_date,
            'departure_time' => $booking->schedule->departure_time,
            'route' => $booking->schedule->route->name,
            'bus_number' => $booking->schedule->bus->bus_number,
            'operator' => $booking->schedule->operator->company_name ?? $booking->schedule->operator->name,
            'verification_url' => route('tickets.verify', $booking->booking_reference),
            'generated_at' => now()->toISOString(),
        ]);
    }

    /**
     * Generate QR code image.
     */
    private function generateQrCode(string $data): string
    {
        $qrCode = new QrCode($data);
        $qrCode->setSize(200);
        $qrCode->setMargin(10);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return base64_encode($result->getString());
    }
}
