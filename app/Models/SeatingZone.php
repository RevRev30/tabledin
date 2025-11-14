<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeatingZone extends Model
{
    protected $fillable = [
        'restaurant_id',
        'seating_layout_id',
        'zone_name',
        'description',
        'zone_coordinates',
        'zone_color',
        'max_capacity',
        'amenities',
        'is_active'
    ];

    protected $casts = [
        'zone_coordinates' => 'array',
        'amenities' => 'array',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function seatingLayout(): BelongsTo
    {
        return $this->belongsTo(SeatingLayout::class);
    }

    public function tables(): HasMany
    {
        return $this->hasMany(Table::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
