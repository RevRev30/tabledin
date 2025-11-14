<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = [
        'customer_id',
        'restaurant_id',
        'table_id',
        'reservation_reference',
        'reservation_date',
        'reservation_time',
        'number_of_guests',
        'status',
        'special_requests',
        'customer_phone',
        'customer_email',
        'confirmed_at',
        'seated_at',
        'completed_at'
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'reservation_time' => 'datetime:H:i',
        'confirmed_at' => 'datetime',
        'seated_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('reservation_date', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('reservation_date', '>=', today())
                    ->where('status', '!=', 'cancelled');
    }

    // Authorization methods
    public function canView($user)
    {
        return $this->customer_id === $user->id;
    }

    public function canUpdate($user)
    {
        return $this->customer_id === $user->id && in_array($this->status, ['pending', 'confirmed']);
    }

    public function canDelete($user)
    {
        return $this->customer_id === $user->id && in_array($this->status, ['pending', 'confirmed']);
    }

    /**
     * Boot model and ensure a reservation_reference is generated when creating.
     */
    protected static function booted()
    {
        static::creating(function ($reservation) {
            if (empty($reservation->reservation_reference)) {
                // Example format: R-20251017-8CHAR  (R-YYYYMMDD-<6-8 random alnum>)
                $reservation->reservation_reference = strtoupper('R-' . date('Ymd') . '-' . Str::upper(Str::random(6)));
            }
        });
    }
}
