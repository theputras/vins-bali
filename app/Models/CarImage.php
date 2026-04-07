<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['car_id', 'image_path', 'is_primary', 'sort_order'])]
class CarImage extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────

    /**
     * Get the car that owns this image.
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
