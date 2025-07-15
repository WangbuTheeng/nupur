<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

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

        return view('customer.tickets.show', compact('booking'));
    }

    /**
     * Show detailed ticket view for booking.
     */
    public function view(Booking $booking)
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

        return view('tickets.view', compact('booking'));
    }

    /**
     * Download e-ticket as PDF (Compact Format).
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

        // Generate Compact PDF
        $pdf = Pdf::loadView('tickets.compact-pdf', compact('booking'));
        $pdf->setPaper([0, 0, 288, 432], 'portrait'); // 4x6 inches in points for compact size

        $filename = 'BookNGO-Compact-Ticket-' . $booking->booking_reference . '.pdf';

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

            // Generate Compact PDF
            $pdf = Pdf::loadView('tickets.compact-pdf', compact('booking'));
            $pdf->setPaper([0, 0, 288, 432], 'portrait'); // 4x6 inches in points for compact size

            // Send email with compact PDF attachment
            Mail::send('customer.emails.ticket', [
                'booking' => $booking,
                'personalMessage' => $request->message,
            ], function ($message) use ($booking, $request, $pdf) {
                $message->to($request->email)
                        ->subject('BookNGO Compact E-Ticket - ' . $booking->booking_reference)
                        ->attachData(
                            $pdf->output(),
                            'BookNGO-Compact-Ticket-' . $booking->booking_reference . '.pdf',
                            ['mime' => 'application/pdf']
                        );
            });

            return back()->with('success', 'Compact e-ticket sent successfully to ' . $request->email);

        } catch (\Exception $e) {
            // Log the actual error for debugging
            \Log::error('Email sending failed: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'email' => $request->email,
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to send e-ticket. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Generate QR code for ticket verification.
     * TODO: Implement QR code generation in the future
     */
    public function qrCode(Booking $booking)
    {
        // Ensure user can only view their own QR codes
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to QR code.');
        }

        // For now, return a placeholder response
        return response('QR Code generation will be implemented in the future')
            ->header('Content-Type', 'text/plain');
    }
}
