<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'location_id',
        'date',
        'clock_in',
        'clock_in_latitude',
        'clock_in_longitude',
        'clock_in_photo',
        'clock_in_distance',
        'clock_out',
        'clock_out_latitude',
        'clock_out_longitude',
        'clock_out_photo',
        'clock_out_distance',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'clock_in_latitude' => 'decimal:8',
        'clock_in_longitude' => 'decimal:8',
        'clock_out_latitude' => 'decimal:8',
        'clock_out_longitude' => 'decimal:8',
        'clock_in_distance' => 'decimal:2',
        'clock_out_distance' => 'decimal:2',
    ];

    // =============================================
    // Relationships
    // =============================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    // =============================================
    // Scopes
    // =============================================

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeByMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // =============================================
    // Helpers
    // =============================================

    public function getWorkDurationAttribute(): ?string
    {
        if (!$this->clock_in || !$this->clock_out) {
            return null;
        }
        $diff = $this->clock_in->diff($this->clock_out);
        return sprintf('%02d:%02d', $diff->h, $diff->i);
    }

    public function hasCheckedIn(): bool
    {
        return !is_null($this->clock_in);
    }

    public function hasCheckedOut(): bool
    {
        return !is_null($this->clock_out);
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'Hadir'  => 'green',
            'Telat'  => 'yellow',
            'Mangkir' => 'red',
            default  => 'gray',
        };
    }
}
