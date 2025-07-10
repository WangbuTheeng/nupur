<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Color\Color;

class TicketController extends Controller
{
    /**
     * Generate and download ticket for a booking.
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

        $booking->load(['schedule.route', 'schedule.bus', 'user']);

        // Generate QR code data
        $qrData = $this->generateQrData($booking);
        
        // Create QR code
        $qrCode = QrCode::create($qrData)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(200)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Generate ticket HTML
        $ticketHtml = $this->generateTicketHtml($booking, $result->getDataUri());

        return response($ticketHtml)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="ticket-' . $booking->booking_reference . '.html"');
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

        // Generate QR code data
        $qrData = $this->generateQrData($booking);
        
        // Create QR code
        $qrCode = QrCode::create($qrData)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(200)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return view('tickets.view', compact('booking', 'qrData'))->with('qrCodeDataUri', $result->getDataUri());
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
     * Generate QR code data for a booking.
     */
    private function generateQrData(Booking $booking)
    {
        $data = [
            'booking_reference' => $booking->booking_reference,
            'user_id' => $booking->user_id,
            'schedule_id' => $booking->schedule_id,
            'seats' => $booking->seat_numbers,
            'passenger_count' => $booking->passenger_count,
            'total_amount' => $booking->total_amount,
            'travel_date' => $booking->schedule->travel_date->format('Y-m-d'),
            'departure_time' => $booking->schedule->departure_time->format('H:i:s'),
            'route' => $booking->schedule->route->full_name,
            'bus' => $booking->schedule->bus->bus_number,
            'generated_at' => now()->toISOString(),
            'verification_hash' => hash('sha256', $booking->booking_reference . $booking->user_id . $booking->total_amount)
        ];

        return json_encode($data);
    }

    /**
     * Generate ticket HTML.
     */
    private function generateTicketHtml(Booking $booking, $qrCodeDataUri)
    {
        return view('tickets.template', compact('booking', 'qrCodeDataUri'))->render();
    }
}
