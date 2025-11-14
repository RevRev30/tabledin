<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transportation extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'from_destination_id',
        'to_destination_id',
        'departure_time',
        'arrival_time',
        'price',
        'currency',
        'capacity',
        'available_seats',
        'features',
        'operator',
        'is_active'
    ];

    protected $casts = [
        'features' => 'array',
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function fromDestination(): BelongsTo
    {
        return $this->belongsTo(Destination::class, 'from_destination_id');
    }

    public function toDestination(): BelongsTo
    {
        return $this->belongsTo(Destination::class, 'to_destination_id');
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
