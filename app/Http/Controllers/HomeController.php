<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Display the homepage with hero, featured cars, and advantages.
     */
    public function index(): Response
    {
        $featuredCars = Car::with(['images', 'category', 'services'])
            ->available()
            ->featured()
            ->ordered()
            ->take(6)
            ->get();

        $heroImages = \App\Models\CarImage::where('is_primary', true)
            ->inRandomOrder()
            ->take(5)
            ->get()
            ->map(fn ($img) => \Illuminate\Support\Facades\Storage::url($img->image_path))
            ->toArray();

        return Inertia::render('Home', [
            'featuredCars' => $featuredCars,
            'heroImages' => $heroImages,
        ]);
    }
}
