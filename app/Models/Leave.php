<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'date',
        'reason',
        'attachment',
        'status',
        'verified_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // =============================================
    // Relationships
    // =============================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // =============================================
    // Helpers
    // =============================================

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'approved' => 'green',
            'rejected' => 'red',
            'pending'  => 'yellow',
            default    => 'gray',
        };
    }
}
