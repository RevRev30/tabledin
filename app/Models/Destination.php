<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Destination extends Model
{
    protected $fillable = [
        'name',
        'description',
        'country',
        'city',
        'latitude',
        'longitude',
        'images',
        'climate',
        'best_time_to_visit',
        'attractions',
        'activities',
        'average_cost_per_day',
        'currency',
        'is_popular',
        'is_active'
    ];

    protected $casts = [
        'images' => 'array',
        'attractions' => 'array',
        'activities' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'average_cost_per_day' => 'decimal:2',
        'is_popular' => 'boolean',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function accommodations(): HasMany
    {
        return $this->hasMany(Accommodation::class);
    }

    public function transportationsFrom(): HasMany
    {
        return $this->hasMany(Transportation::class, 'from_destination_id');
    }

    public function transportationsTo(): HasMany
    {
        return $this->hasMany(Transportation::class, 'to_destination_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'destination_categories');
    }
}
