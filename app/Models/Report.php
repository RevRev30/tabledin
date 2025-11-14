<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = [
        'staff_id',
        'restaurant_id',
        'report_name',
        'report_type',
        'date_from',
        'date_to',
        'data',
        'file_path',
        'format',
        'generated_at'
    ];

    protected $casts = [
        'data' => 'array',
        'date_from' => 'date',
        'date_to' => 'date',
        'generated_at' => 'datetime'
    ];

    // Relationships
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    // Scopes
    public function scopeDaily($query)
    {
        return $query->where('report_type', 'daily');
    }

    public function scopeWeekly($query)
    {
        return $query->where('report_type', 'weekly');
    }

    public function scopeMonthly($query)
    {
        return $query->where('report_type', 'monthly');
    }
}
