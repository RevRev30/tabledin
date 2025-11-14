<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Restaurant extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'address',
        'phone',
        'email',
        'website',
        'operating_hours',
        'max_capacity',
        'amenities',
        'logo',
        'images',
        'is_active'
    ];

    protected $casts = [
        'operating_hours' => 'array',
        'amenities' => 'array',
        'images' => 'array',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tables(): HasMany
    {
        return $this->hasMany(Table::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function queue(): HasMany
    {
        return $this->hasMany(Queue::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function seatingLayouts(): HasMany
    {
        return $this->hasMany(SeatingLayout::class);
    }

    public function seatingZones(): HasMany
    {
        return $this->hasMany(SeatingZone::class);
    }
}
