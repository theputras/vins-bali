<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    /**
     * Display the bookings list.
     */
    public function index(Request $request): Response
    {
        $query = Booking::with(['car.images'])
            ->latest();

        // Search by customer name or phone
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $bookings = $query->paginate(15)->withQueryString();

        return Inertia::render('admin/bookings/Index', [
            'bookings' => $bookings,
            'filters' => $request->only(['search', 'status']),
            'statusCounts' => [
                'all' => Booking::count(),
                'pending' => Booking::where('status', Booking::STATUS_PENDING)->count(),
                'follow_up' => Booking::where('status', Booking::STATUS_FOLLOW_UP)->count(),
                'approved' => Booking::where('status', Booking::STATUS_APPROVED)->count(),
                'completed' => Booking::where('status', Booking::STATUS_COMPLETED)->count(),
                'cancelled' => Booking::where('status', Booking::STATUS_CANCELLED)->count(),
            ],
        ]);
    }

    /**
     * Update the status of a booking.
     */
    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', Booking::STATUSES)],
            'rental_date' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $updateData = ['status' => $validated['status']];
        
        if (isset($validated['rental_date']) && $validated['rental_date']) {
            $updateData['rental_date'] = $validated['rental_date'];
        }

        if (isset($validated['notes'])) {
            $updateData['notes'] = $validated['notes'];
        }

        $booking->update($updateData);

        $labels = [
            Booking::STATUS_FOLLOW_UP => 'follow up',
            Booking::STATUS_APPROVED => 'disetujui (sewa berjalan)',
            Booking::STATUS_COMPLETED => 'selesai',
            Booking::STATUS_CANCELLED => 'dibatalkan',
            Booking::STATUS_PENDING => 'pending',
        ];

        $label = $labels[$validated['status']] ?? $validated['status'];

        return back()->with('success', "Status pesanan berhasil diubah menjadi {$label}.");
    }

    /**
     * Mark notifications as read for the current admin.
     */
    public function markNotificationsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications
            ->where('type', 'App\Notifications\NewBookingNotification')
            ->markAsRead();

        return back();
    }
}
