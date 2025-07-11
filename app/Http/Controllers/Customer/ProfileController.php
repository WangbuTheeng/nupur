<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the customer profile page.
     */
    public function show()
    {
        $user = Auth::user();
        
        // Get user statistics
        $stats = [
            'total_bookings' => $user->bookings()->count(),
            'confirmed_bookings' => $user->bookings()->where('status', 'confirmed')->count(),
            'total_spent' => $user->bookings()->where('status', 'confirmed')->sum('total_amount'),
            'member_since' => $user->created_at->format('M Y'),
        ];

        return view('customer.profile.show', compact('user', 'stats'));
    }

    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('customer.profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'address' => ['nullable', 'string', 'max:500'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($validated);

        return redirect()->route('customer.profile.show')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Show the password change form.
     */
    public function editPassword()
    {
        return view('customer.profile.password');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('customer.profile.show')
            ->with('success', 'Password updated successfully!');
    }

    /**
     * Show the account preferences form.
     */
    public function preferences()
    {
        $user = Auth::user();
        return view('customer.profile.preferences', compact('user'));
    }

    /**
     * Update user preferences.
     */
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'email_notifications' => ['boolean'],
            'sms_notifications' => ['boolean'],
            'marketing_emails' => ['boolean'],
            'booking_reminders' => ['boolean'],
            'payment_notifications' => ['boolean'],
            'preferred_language' => ['in:en,ne'],
            'preferred_currency' => ['in:NPR,USD'],
        ]);

        // Update user preferences (you might want to create a separate preferences table)
        $user->update([
            'preferences' => array_merge($user->preferences ?? [], $validated)
        ]);

        return redirect()->route('customer.profile.preferences')
            ->with('success', 'Preferences updated successfully!');
    }

    /**
     * Show account deletion confirmation.
     */
    public function deleteAccount()
    {
        return view('customer.profile.delete');
    }

    /**
     * Delete the user's account.
     */
    public function destroyAccount(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
            'confirmation' => ['required', 'in:DELETE'],
        ]);

        $user = Auth::user();

        // Check if user has any active bookings
        $activeBookings = $user->bookings()
            ->whereIn('status', ['confirmed', 'pending'])
            ->whereHas('schedule', function($query) {
                $query->where('travel_date', '>=', now());
            })
            ->count();

        if ($activeBookings > 0) {
            return back()->withErrors([
                'active_bookings' => 'You cannot delete your account while you have active bookings. Please cancel or complete your trips first.'
            ]);
        }

        // Anonymize user data instead of hard delete for audit purposes
        $user->update([
            'name' => 'Deleted User',
            'email' => 'deleted_' . $user->id . '@deleted.local',
            'phone' => null,
            'email_verified_at' => null,
            'password' => Hash::make('deleted'),
            'is_active' => false,
            'deleted_at' => now(),
        ]);

        Auth::logout();

        return redirect()->route('welcome')
            ->with('success', 'Your account has been successfully deleted.');
    }

    /**
     * Download user data (GDPR compliance).
     */
    public function downloadData()
    {
        $user = Auth::user();
        
        $userData = [
            'personal_information' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'date_of_birth' => $user->date_of_birth,
                'gender' => $user->gender,
                'address' => $user->address,
                'member_since' => $user->created_at->toDateString(),
            ],
            'bookings' => $user->bookings()->with(['schedule.route', 'schedule.bus'])->get()->map(function($booking) {
                return [
                    'booking_reference' => $booking->booking_reference,
                    'route' => $booking->schedule->route->full_name,
                    'travel_date' => $booking->schedule->travel_date->toDateString(),
                    'departure_time' => $booking->schedule->departure_time->format('H:i'),
                    'seat_numbers' => $booking->seat_numbers,
                    'total_amount' => $booking->total_amount,
                    'status' => $booking->status,
                    'booking_date' => $booking->created_at->toDateString(),
                ];
            }),
            'payments' => $user->payments()->get()->map(function($payment) {
                return [
                    'transaction_id' => $payment->transaction_id,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'status' => $payment->status,
                    'payment_date' => $payment->created_at->toDateString(),
                ];
            }),
        ];

        $fileName = 'bookngo_user_data_' . $user->id . '_' . now()->format('Y-m-d') . '.json';
        
        return response()->json($userData, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Upload profile picture.
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && \Storage::exists($user->avatar)) {
                \Storage::delete($user->avatar);
            }

            // Store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            
            $user->update(['avatar' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Profile picture updated successfully!',
                'avatar_url' => \Storage::url($path),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to upload profile picture.',
        ], 400);
    }

    /**
     * Remove profile picture.
     */
    public function removeAvatar()
    {
        $user = Auth::user();

        if ($user->avatar && \Storage::exists($user->avatar)) {
            \Storage::delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Profile picture removed successfully!',
        ]);
    }
}
