<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantPayment extends Model
{
    protected $fillable = [
        'restaurant_order_id',
        'amount',
        'payment_method',
        'payment_status',
        'transaction_ref',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function restaurantOrder(): BelongsTo
    {
        return $this->belongsTo(RestaurantOrder::class);
    }
}
