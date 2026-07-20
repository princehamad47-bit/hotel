<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guest extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'address',
        'id_type',
        'id_number',
        'nationality',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
    public function restaurantOrders()
    {
        return $this->hasMany(RestaurantOrder::class);
    }
}
