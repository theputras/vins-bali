<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    /**
     * Display the car catalog with search and filter.
     */
    public function index(Request $request): Response
    {
        $query = Car::with(['images', 'category', 'services'])
            ->available()
            ->ordered();

        // Search by name or brand
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // Filter by brand
        if ($brand = $request->input('brand')) {
            $query->where('brand', $brand);
        }

        // Filter by category
        if ($category = $request->input('category')) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('name', $category);
            });
        }

        // Filter by transmission
        if ($transmission = $request->input('transmission')) {
            $query->where('transmission', $transmission);
        }

        // Filter by seats (min)
        if ($seats = $request->input('seats')) {
            $query->where('seats', '>=', (int) $seats);
        }

        // Sort options
        $sort = $request->input('sort', 'default');
        $query = match ($sort) {
            'price_asc' => $query->reorder()->orderBy('price_per_day', 'asc'),
            'price_desc' => $query->reorder()->orderBy('price_per_day', 'desc'),
            'newest' => $query->reorder()->latest(),
            default => $query,
        };

        $cars = $query->paginate(12)->withQueryString();

        // Get available brands for filter dropdown
        $brands = Car::available()
            ->select('brand')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand');

        // Get categories for filter dropdown
        $categories = \App\Models\CarCategory::orderBy('name')->pluck('name');

        return Inertia::render('catalog/Index', [
            'cars' => $cars,
            'brands' => $brands,
            'categories' => $categories,
            'filters' => $request->only(['search', 'brand', 'category', 'transmission', 'seats', 'sort']),
        ]);
    }

    /**
     * Display the car detail page with specifications and gallery.
     */
    public function show(string $slug): Response
    {
        $car = Car::with(['images', 'category', 'services'])
            ->where('slug', $slug)
            ->available()
            ->firstOrFail();

        // Get other available cars for "You might also like" section
        $relatedCars = Car::with(['images', 'category', 'services'])
            ->available()
            ->where('id', '!=', $car->id)
            ->where('brand', $car->brand)
            ->take(3)
            ->get();

        // If not enough same-brand cars, fill with other cars
        if ($relatedCars->count() < 3) {
            $remaining = 3 - $relatedCars->count();
            $otherCars = Car::with(['images', 'category', 'services'])
                ->available()
                ->where('id', '!=', $car->id)
                ->whereNotIn('id', $relatedCars->pluck('id'))
                ->inRandomOrder()
                ->take($remaining)
                ->get();

            $relatedCars = $relatedCars->concat($otherCars);
        }

        return Inertia::render('catalog/Show', [
            'car' => $car,
            'relatedCars' => $relatedCars,
        ]);
    }
}
