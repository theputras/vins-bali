<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CarUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('cars', 'slug')->ignore($this->route('car'))],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'transmission' => ['required', 'string', 'in:Automatic,Manual'],
            'year' => ['required', 'integer', 'min:1900', 'max:'.(date('Y') + 2)],
            'seats' => ['required', 'integer', 'min:1', 'max:20'],
            'fuel_type' => ['required', 'string', 'in:Bensin,Diesel,Hybrid,Electric'],
            'color' => ['required', 'string', 'max:100'],
            'is_available' => ['boolean'],
            'is_featured' => ['boolean'],
            'category' => ['nullable', 'string', 'max:50'],
            'horsepower' => ['nullable', 'integer', 'min:0'],
            'engine_capacity' => ['nullable', 'string', 'max:50'],
            'acceleration_0_100' => ['nullable', 'numeric', 'min:0'],
            'services' => ['nullable', 'array'],
            'services.*.id' => ['required', 'exists:services,id'],
            'services.*.discount_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:20480'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_keywords' => ['nullable', 'string', 'max:500'],
            'seo_description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama mobil wajib diisi.',
            'brand.required' => 'Merk mobil wajib diisi.',
            'slug.unique' => 'Slug sudah digunakan oleh mobil lain.',
            'price_per_day.required' => 'Harga sewa per hari wajib diisi.',
            'price_per_day.numeric' => 'Harga sewa harus berupa angka.',
            'transmission.in' => 'Transmisi harus Automatic atau Manual.',
            'year.required' => 'Tahun produksi wajib diisi.',
            'seats.required' => 'Jumlah kursi wajib diisi.',
            'fuel_type.in' => 'Tipe bahan bakar harus Bensin, Diesel, Hybrid, atau Electric.',
            'color.required' => 'Warna mobil wajib diisi.',
            'images.max' => 'Maksimal 10 gambar per mobil.',
            'images.*.image' => 'File harus berupa gambar.',
            'images.*.mimes' => 'Format gambar harus JPG, PNG, atau WebP.',
            'images.*.max' => 'Ukuran gambar maksimal 5MB.',
        ];
    }
}
