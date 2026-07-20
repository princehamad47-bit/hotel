<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantOrderTax extends Model
{
    protected $fillable = [
        'restaurant_order_id',
        'tax_id',
        'tax_name',
        'tax_type',
        'tax_value',
        'tax_amount',
    ];

    protected $casts = [
        'tax_value' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];

    public function restaurantOrder(): BelongsTo
    {
        return $this->belongsTo(RestaurantOrder::class);
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }
}
