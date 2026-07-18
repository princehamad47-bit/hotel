<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationRoom extends Model
{
    protected $fillable = [
        'reservation_id',
        'room_id',
        'room_rate',
        'nights',
        'subtotal',
    ];

    protected $casts = [
        'room_rate' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
