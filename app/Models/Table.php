<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    protected $fillable = [
        'restaurant_id',
        'seating_layout_id',
        'seating_zone_id',
        'table_name',
        'capacity',
        'status',
        'location',
        'position',
        'table_coordinates',
        'table_rotation',
        'table_shape',
        'table_dimensions',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'position' => 'array',
        'table_coordinates' => 'array',
        'table_dimensions' => 'array',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function seatingLayout(): BelongsTo
    {
        return $this->belongsTo(SeatingLayout::class);
    }

    public function seatingZone(): BelongsTo
    {
        return $this->belongsTo(SeatingZone::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    public function scopeReserved($query)
    {
        return $query->where('status', 'reserved');
    }
}
