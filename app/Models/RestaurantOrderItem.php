<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantOrderItem extends Model
{
    protected $fillable = [
        'restaurant_order_id',
        'menu_item_id',
        'item_name',
        'unit_price',
        'quantity',
        'subtotal',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function restaurantOrder(): BelongsTo
    {
        return $this->belongsTo(RestaurantOrder::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }
}
