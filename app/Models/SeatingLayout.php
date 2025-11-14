<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeatingLayout extends Model
{
    protected $fillable = [
        'restaurant_id',
        'layout_name',
        'description',
        'layout_data',
        'width',
        'height',
        'background_image',
        'is_active',
        'is_default'
    ];

    protected $casts = [
        'layout_data' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean'
    ];

    // Relationships
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function zones(): HasMany
    {
        return $this->hasMany(SeatingZone::class);
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

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
