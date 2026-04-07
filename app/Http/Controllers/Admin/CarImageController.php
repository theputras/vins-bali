<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarImageController extends Controller
{
    /**
     * Set an image as the primary image for its car.
     */
    public function setPrimary(CarImage $carImage): RedirectResponse
    {
        // Unset all other primary images for this car
        CarImage::where('car_id', $carImage->car_id)
            ->where('id', '!=', $carImage->id)
            ->update(['is_primary' => false]);

        // Set this one as primary
        $carImage->update(['is_primary' => true]);

        return back()->with('success', 'Foto utama berhasil diubah.');
    }

    /**
     * Update the sort order of images.
     */
    public function reorder(Request $request): RedirectResponse
    {
        $request->validate([
            'images' => ['required', 'array'],
            'images.*.id' => ['required', 'integer', 'exists:car_images,id'],
            'images.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($request->input('images') as $imageData) {
            CarImage::where('id', $imageData['id'])
                ->update(['sort_order' => $imageData['sort_order']]);
        }

        return back()->with('success', 'Urutan foto berhasil diperbarui.');
    }

    /**
     * Remove the specified image from storage.
     */
    public function destroy(CarImage $carImage): RedirectResponse
    {
        $wasPrimary = $carImage->is_primary;
        $carId = $carImage->car_id;

        // Delete file from storage
        Storage::disk('public')->delete($carImage->image_path);

        // Delete database record
        $carImage->delete();

        // If deleted image was primary, set the first remaining image as primary
        if ($wasPrimary) {
            $nextImage = CarImage::where('car_id', $carId)
                ->orderBy('sort_order')
                ->first();

            $nextImage?->update(['is_primary' => true]);
        }

        return back()->with('success', 'Foto berhasil dihapus.');
    }
}
