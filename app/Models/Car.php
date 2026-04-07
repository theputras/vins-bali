<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

#[Fillable([
    'name',
    'brand',
    'slug',
    'short_description',
    'description',
    'price_per_day',
    'transmission',
    'year',
    'seats',
    'fuel_type',
    'color',
    'is_available',
    'is_featured',
    'sort_order',
    'cars_category_id',
    'horsepower',
    'engine_capacity',
    'acceleration_0_100',
])]
class Car extends Model
{
    /**
     * Boot the model.
     * Auto-generate slug from name if not provided.
     */
    protected static function booted(): void
    {
        static::creating(function (Car $car) {
            if (empty($car->slug)) {
                $car->slug = static::generateUniqueSlug($car->name);
            }
        });

        static::updating(function (Car $car) {
            // Only auto-regenerate slug if name changed AND slug wasn't manually edited
            if ($car->isDirty('name') && ! $car->isDirty('slug')) {
                $car->slug = static::generateUniqueSlug($car->name, $car->id);
            }
        });
    }

    /**
     * Generate a unique slug for the car.
     */
    public static function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $counter = 1;

        $query = static::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $original.'-'.$counter;
            $counter++;
            $query = static::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_per_day' => 'decimal:2',
            'acceleration_0_100' => 'decimal:1',
            'year' => 'integer',
            'seats' => 'integer',
            'horsepower' => 'integer',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────

    /**
     * Get all images for this car.
     */
    public function images(): HasMany
    {
        return $this->hasMany(CarImage::class)->orderBy('is_primary', 'desc')->orderBy('sort_order');
    }

    /**
     * Get the category that owns the car.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CarCategory::class, 'cars_category_id');
    }

    /**
     * Get the services/discounts associated with the car.
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'car_service')
            ->withPivot('discount_percentage')
            ->withTimestamps();
    }

    /**
     * Get the primary image for this car.
     */
    public function primaryImage(): HasMany
    {
        return $this->hasMany(CarImage::class)->where('is_primary', true);
    }

    // ─── Scopes ──────────────────────────────────────────────────

    /**
     * Scope to only available cars.
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope to only featured cars.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to order by sort_order.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }

    // ─── Accessors ───────────────────────────────────────────────

    /**
     * Get formatted price in IDR.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp '.number_format((float) $this->price_per_day, 0, ',', '.');
    }
}
