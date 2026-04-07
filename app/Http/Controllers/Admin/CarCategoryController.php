<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CarCategoryController extends Controller
{
    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:cars_category,name',
        ]);

        CarCategory::create($validated);

        return back()->with('success', 'Kategori mobil berhasil ditambahkan.');
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, CarCategory $carCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:cars_category,name,' . $carCategory->id,
        ]);

        $carCategory->update($validated);

        return back()->with('success', 'Kategori mobil berhasil diperbarui.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(CarCategory $carCategory): RedirectResponse
    {
        if ($carCategory->cars()->count() > 0) {
            return back()->with('error', 'Gagal menghapus! Kategori ini masih digunakan oleh beberapa mobil. Harap pindahkan atau hapus mobil tersebut terlebih dahulu.');
        }

        $carCategory->delete();

        return back()->with('success', 'Kategori mobil berhasil dihapus.');
    }
}
