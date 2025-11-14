<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Accommodation extends Model
{
    protected $fillable = [
        'name',
        'description',
        'destination_id',
        'type',
        'address',
        'latitude',
        'longitude',
        'images',
        'amenities',
        'star_rating',
        'price_per_night',
        'currency',
        'max_guests',
        'rooms_available',
        'contact_info',
        'is_active'
    ];

    protected $casts = [
        'images' => 'array',
        'amenities' => 'array',
        'contact_info' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'price_per_night' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
