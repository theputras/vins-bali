<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CarStoreRequest;
use App\Http\Requests\Admin\CarUpdateRequest;
use App\Models\Car;
use App\Models\CarImage;
use App\Models\CarCategory;
use App\Models\Service;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CarController extends Controller
{
    /**
     * Display a listing of all cars (admin view).
     */
    public function index(Request $request): Response
    {
        $query = Car::with('images')->ordered();

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('color', 'like', "%{$search}%");
            });
        }

        // Availability filter
        if ($request->has('is_available') && $request->input('is_available') !== '') {
            $query->where('is_available', $request->boolean('is_available'));
        }

        $cars = $query->paginate(15)->withQueryString();

        $sortableCars = Car::ordered()->select('id', 'name', 'sort_order', 'brand', 'year')->get();

        return Inertia::render('admin/cars/Index', [
            'cars' => $cars,
            'categories' => CarCategory::withCount('cars')->get(),
            'sortableCars' => $sortableCars,
            'filters' => $request->only(['search', 'is_available']),
        ]);
    }

    /**
     * Show the form for creating a new car.
     */
    public function create(): Response
    {
        return Inertia::render('admin/cars/Form', [
            'car' => null,
            'isEditing' => false,
            'categories' => CarCategory::all(),
            'services' => Service::where('is_active', true)->orderBy('duration_days')->get(),
        ]);
    }

    /**
     * Store a newly created car in storage.
     */
    public function store(CarStoreRequest $request): RedirectResponse
    {
        return DB::transaction(function () use ($request) {
            $validated = $request->validated();
            $images = $validated['images'] ?? [];
            unset($validated['images']);

            // Set defaults
            $validated['is_available'] = $validated['is_available'] ?? true;
            $validated['is_featured'] = $validated['is_featured'] ?? false;
            $validated['sort_order'] = $validated['sort_order'] ?? 0;

            // Handle Services sync
            $servicesData = $validated['services'] ?? [];
            unset($validated['services']);

            $car = Car::create($validated);

            if (!empty($servicesData)) {
                $syncData = [];
                foreach ($servicesData as $sd) {
                    $syncData[$sd['id']] = ['discount_percentage' => $sd['discount_percentage']];
                }
                $car->services()->sync($syncData);
            }

            $this->handleImageUploads($car, $images);

            $this->clearHomeCache();

            return to_route('admin-panel.cars.index')
                ->with('success', "Mobil \"{$car->name}\" berhasil ditambahkan.");
        });
    }

    /**
     * Show the form for editing the specified car.
     */
    public function edit(Car $car): Response
    {
        $car->load(['images', 'services']);

        return Inertia::render('admin/cars/Form', [
            'car' => $car,
            'isEditing' => true,
            'categories' => CarCategory::all(),
            'services' => Service::where('is_active', true)->orderBy('duration_days')->get(),
        ]);
    }

    /**
     * Update the specified car in storage.
     */
    public function update(CarUpdateRequest $request, Car $car): RedirectResponse
    {
        return DB::transaction(function () use ($request, $car) {
            $validated = $request->validated();
            $images = $validated['images'] ?? [];
            unset($validated['images']);

            // Handle Services sync
            $servicesData = $validated['services'] ?? [];
            unset($validated['services']);

            $car->update($validated);

            $syncData = [];
            foreach ($servicesData as $sd) {
                $syncData[$sd['id']] = ['discount_percentage' => $sd['discount_percentage']];
            }
            $car->services()->sync($syncData);

            if (! empty($images)) {
                $this->handleImageUploads($car, $images);
            }

            $this->clearHomeCache();

            return to_route('admin-panel.cars.index')
                ->with('success', "Mobil \"{$car->name}\" berhasil diperbarui.");
        });
    }

    /**
     * Toggle the availability status of the specified car.
     */
    public function toggleAvailability(Car $car): RedirectResponse
    {
        $car->update(['is_available' => ! $car->is_available]);

        $this->clearHomeCache();

        $status = $car->is_available ? 'tersedia' : 'tidak tersedia';

        return back()->with('success', "Status mobil \"{$car->name}\" diubah menjadi {$status}.");
    }

    /**
     * Toggle the rented status of the specified car.
     */
    public function toggleRented(Car $car): RedirectResponse
    {
        $car->update(['is_rented' => ! $car->is_rented]);

        $this->clearHomeCache();

        $status = $car->is_rented ? 'sedang disewa' : 'tersedia';

        return back()->with('success', "Status sewa mobil \"{$car->name}\" diubah menjadi {$status}.");
    }

    /**
     * Update the display order of multiple cars.
     */
    public function updateOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cars' => 'required|array',
            'cars.*.id' => 'required|exists:cars,id',
            'cars.*.sort_order' => 'required|integer',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['cars'] as $carData) {
                Car::where('id', $carData['id'])->update(['sort_order' => $carData['sort_order']]);
            }
        });

        $this->clearHomeCache();

        return back()->with('success', 'Urutan mobil berhasil diperbarui.');
    }

    /**
     * Remove the specified car from storage.
     */
    public function destroy(Car $car): RedirectResponse
    {
        $carName = $car->name;

        DB::transaction(function () use ($car) {
            // Delete all associated image files from storage
            foreach ($car->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }

            // Car images will be cascade deleted via FK
            $car->delete();
        });

        $this->clearHomeCache();

        return to_route('admin-panel.cars.index')
            ->with('success', "Mobil \"{$carName}\" berhasil dihapus.");
    }

    /**
     * Handle uploading images for a car.
     * Images are automatically compressed and converted to WebP.
     *
     * @param  array<int, \Illuminate\Http\UploadedFile>  $images
     */
    private function handleImageUploads(Car $car, array $images): void
    {
        $imageService = new ImageService;
        $currentMaxOrder = $car->images()->max('sort_order') ?? 0;
        $isFirstImage = $car->images()->count() === 0;

        foreach ($images as $index => $imageFile) {
            $path = $imageService->processAndStore($imageFile, "cars/{$car->slug}");

            CarImage::create([
                'car_id' => $car->id,
                'image_path' => $path,
                'is_primary' => $isFirstImage && $index === 0,
                'sort_order' => $currentMaxOrder + $index + 1,
            ]);
        }
    }

    /**
     * Clear home page caches to ensure fresh data.
     */
    private function clearHomeCache(): void
    {
        \Illuminate\Support\Facades\Cache::forget('home_featured_cars');
        \Illuminate\Support\Facades\Cache::forget('home_hero_images');
        \Illuminate\Support\Facades\Cache::forget('home_categories');
    }
}
