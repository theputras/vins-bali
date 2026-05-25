<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'totalCars' => Car::count(),
                'availableCars' => Car::available()->count(),
                'featuredCars' => Car::featured()->count(),
                'unavailableCars' => Car::where('is_available', false)->count(),
                'totalBookings' => Booking::count(),
                'pendingBookings' => Booking::where('status', Booking::STATUS_PENDING)->count(),
                'followUpBookings' => Booking::where('status', Booking::STATUS_FOLLOW_UP)->count(),
                'approvedBookings' => Booking::where('status', Booking::STATUS_APPROVED)->count(),
            ],
            'recentCars' => Car::with('images')
                ->latest()
                ->take(5)
                ->get(),
            'recentBookings' => Booking::with('car.images')
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
