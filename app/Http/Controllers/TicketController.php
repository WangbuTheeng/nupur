<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;


class TicketController extends Controller
{
    /**
     * Show ticket verification form.
     */
    public function showVerifyForm(Request $request)
    {
        $bookingReference = $request->get('ref');
        return view('tickets.verify', compact('bookingReference'));
    }

    /**
     * Generate and download compact ticket for a booking.
     */
    public function download(Booking $booking)
    {
        // Check if user owns this booking or is admin
        if ($booking->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        // Check if booking is confirmed
        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Ticket can only be downloaded for confirmed bookings.');
        }

        $booking->load([
            'schedule.route.sourceCity',
            'schedule.route.destinationCity',
            'schedule.bus.busType',
            'schedule.operator'
        ]);

        // Generate compact ticket PDF
        $pdf = Pdf::loadView('tickets.compact-pdf', compact('booking'));
        $pdf->setPaper([0, 0, 288, 432], 'portrait'); // 4x6 inches in points for compact size

        $filename = 'BookNGO-Compact-Ticket-' . $booking->booking_reference . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * View ticket online.
     */
    public function view(Booking $booking)
    {
        // Check if user owns this booking or is admin
        if ($booking->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        // Check if booking is confirmed
        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Ticket can only be viewed for confirmed bookings.');
        }

        $booking->load(['schedule.route', 'schedule.bus', 'user']);

        return view('tickets.view', compact('booking'));
    }

    /**
     * Verify ticket using QR code data.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        try {
            $data = json_decode(base64_decode($request->qr_data), true);
            
            if (!$data || !isset($data['booking_reference'])) {
                return response()->json(['valid' => false, 'message' => 'Invalid QR code']);
            }

            $booking = Booking::where('booking_reference', $data['booking_reference'])
                ->with(['schedule.route', 'schedule.bus', 'user'])
                ->first();

            if (!$booking) {
                return response()->json(['valid' => false, 'message' => 'Booking not found']);
            }

            if ($booking->status !== 'confirmed') {
                return response()->json(['valid' => false, 'message' => 'Booking not confirmed']);
            }

            // Verify QR code integrity
            $expectedData = $this->generateQrData($booking);
            if ($request->qr_data !== base64_encode($expectedData)) {
                return response()->json(['valid' => false, 'message' => 'QR code verification failed']);
            }

            return response()->json([
                'valid' => true,
                'booking' => [
                    'reference' => $booking->booking_reference,
                    'passenger_name' => $booking->user->name,
                    'route' => $booking->schedule->route->full_name,
                    'bus' => $booking->schedule->bus->display_name,
                    'travel_date' => $booking->schedule->travel_date->format('M d, Y'),
                    'departure_time' => $booking->schedule->departure_time->format('h:i A'),
                    'seats' => $booking->seat_numbers_string,
                    'passenger_count' => $booking->passenger_count,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['valid' => false, 'message' => 'QR code verification failed']);
        }
    }

    /**
     * Verify ticket using booking reference (manual entry).
     */
    public function verifyManual(Request $request)
    {
        $request->validate([
            'booking_reference' => 'required|string',
        ]);

        try {
            $booking = Booking::where('booking_reference', $request->booking_reference)
                ->with(['schedule.route', 'schedule.bus', 'user'])
                ->first();

            if (!$booking) {
                return response()->json(['valid' => false, 'message' => 'Booking not found']);
            }

            if ($booking->status !== 'confirmed') {
                return response()->json(['valid' => false, 'message' => 'Booking not confirmed']);
            }

            return response()->json([
                'valid' => true,
                'booking' => [
                    'reference' => $booking->booking_reference,
                    'passenger_name' => $booking->user->name,
                    'route' => $booking->schedule->route->full_name,
                    'bus' => $booking->schedule->bus->display_name,
                    'travel_date' => $booking->schedule->travel_date->format('M d, Y'),
                    'departure_time' => $booking->schedule->departure_time->format('h:i A'),
                    'seats' => $booking->seat_numbers_string,
                    'passenger_count' => $booking->passenger_count,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['valid' => false, 'message' => 'Verification failed']);
        }
    }



    /**
     * Generate ticket HTML.
     */
    private function generateTicketHtml(Booking $booking)
    {
        return view('tickets.template', compact('booking'))->render();
    }
}
