<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Queue extends Model
{
    protected $table = 'queue';
    protected $fillable = [
        'restaurant_id',
        'customer_name',
        'customer_phone',
        'party_size',
        'token_number',
        'status',
        'estimated_wait_time',
        'joined_at',
        'called_at',
        'seated_at',
        'notes'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'called_at' => 'datetime',
        'seated_at' => 'datetime'
    ];

    // Relationships
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    // Scopes
    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    public function scopeCalled($query)
    {
        return $query->where('status', 'called');
    }

    public function scopeSeated($query)
    {
        return $query->where('status', 'seated');
    }
}
