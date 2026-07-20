<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Reservation extends Model
{
    protected $fillable = [
        'guest_id',
        'reservation_code',
        'check_in_date',
        'check_out_date',
        'adults',
        'children',
        'status',
        'total_amount',
        'paid_amount',
        'booking_source',
        'notes',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function reservationRooms(): HasMany
    {
        return $this->hasMany(ReservationRoom::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function reservationServices(): HasMany
    {
        return $this->hasMany(ReservationService::class);
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'reservation_rooms')
            ->withPivot(['room_rate', 'nights', 'subtotal'])
            ->withTimestamps();
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(\App\Models\ReservationTax::class);
    }

    public function restaurantOrders(): HasMany
    {
        return $this->hasMany(\App\Models\RestaurantOrder::class);
    }

    public function getRoomTotalAttribute()
    {
        return $this->reservationRooms->sum('subtotal');
    }

    public function getServiceTotalAttribute()
    {
        return $this->reservationServices->sum('total_price');
    }

    public function getRestaurantTotalAttribute()
    {
        return $this->restaurantOrders()
            ->whereNotIn('status', ['cancelled'])
            ->sum('subtotal');
    }

    public function getSubTotalAttribute()
    {
        return $this->room_total + $this->service_total + $this->restaurant_total;
    }

    public function getTaxTotalAttribute()
    {
        return $this->taxes->sum('tax_amount');
    }

    public function getGrandTotalAttribute()
    {
        return $this->sub_total + $this->tax_total;
    }
}
