<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\NewBookingNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    /**
     * Store a new booking request from the customer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_id' => ['required', 'exists:cars,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'rental_date' => ['required', 'string', 'max:255'],
        ]);

        $booking = Booking::create([
            'car_id' => $validated['car_id'],
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'rental_date' => $validated['rental_date'],
            'status' => Booking::STATUS_PENDING,
        ]);

        // Load the car relation for the notification
        $booking->load('car');

        // Notify all admin users via email + database
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new NewBookingNotification($booking));

        return redirect()->route('booking.thankyou')->with('booking_success', true);
    }

    /**
     * Display the thank you page.
     */
    public function thankyou(Request $request): Response
    {
        return Inertia::render('booking/ThankYou');
    }
}
