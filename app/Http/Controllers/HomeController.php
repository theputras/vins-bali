<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarCategory;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Display the homepage with hero, featured cars, and advantages.
     */
    public function index(): Response
    {
        $featuredCars = \Illuminate\Support\Facades\Cache::remember('home_featured_cars', 86400, function () {
            return Car::with(['images', 'category', 'services'])
                ->available()
                ->featured()
                ->ordered()
                ->take(6)
                ->get();
        });

        $heroImages = \Illuminate\Support\Facades\Cache::remember('home_hero_images', 86400, function () {
            return \App\Models\CarImage::where('is_primary', true)
                ->inRandomOrder()
                ->take(5)
                ->get()
                ->map(fn ($img) => \Illuminate\Support\Facades\Storage::url($img->image_path))
                ->toArray();
        });

        $categories = \Illuminate\Support\Facades\Cache::remember('home_categories', 86400, function () {
            return CarCategory::orderBy('name')->pluck('name')->toArray();
        });

        return Inertia::render('Home', [
            'featuredCars' => $featuredCars,
            'heroImages' => $heroImages,
            'categories' => $categories,
        ]);
    }
}
