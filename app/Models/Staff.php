<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'address',
        'cnic',
        'designation',
        'department',
        'salary',
        'join_date',
        'status',
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'join_date' => 'date',
    ];

    public function reservationServices(): HasMany
    {
        return $this->hasMany(ReservationService::class, 'assigned_staff_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
