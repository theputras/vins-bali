<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'car_id',
    'customer_name',
    'customer_phone',
    'rental_date',
    'status',
    'notes',
])]
class Booking extends Model
{
    /**
     * Valid statuses for a booking.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_FOLLOW_UP = 'follow_up';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_FOLLOW_UP,
        self::STATUS_APPROVED,
        self::STATUS_CANCELLED,
        self::STATUS_COMPLETED,
    ];

    /**
     * Get the car associated with this booking.
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * Human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Baru Masuk',
            self::STATUS_FOLLOW_UP => 'Follow Up',
            self::STATUS_APPROVED => 'Sewa Berjalan',
            self::STATUS_CANCELLED => 'Batal Sewa',
            self::STATUS_COMPLETED => 'Selesai',
            default => ucfirst($this->status),
        };
    }
}
