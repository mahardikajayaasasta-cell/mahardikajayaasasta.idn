<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'employee_id',
        'department',
        'position',
        'role',
        'is_active',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // =============================================
    // Relationships
    // =============================================

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    // =============================================
    // Scopes
    // =============================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeKaryawan($query)
    {
        return $query->where('role', 'karyawan');
    }

    // =============================================
    // Helpers
    // =============================================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKaryawan(): bool
    {
        return $this->role === 'karyawan';
    }

    public function todayAttendance()
    {
        return $this->attendances()
            ->whereDate('date', today())
            ->first();
    }
}
