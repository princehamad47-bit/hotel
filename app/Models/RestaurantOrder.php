<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantOrder extends Model
{
    protected $fillable = [
        'order_code',
        'reservation_id',
        'guest_id',
        'customer_name',
        'customer_phone',
        'order_type',
        'table_number',
        'status',
        'subtotal',
        'tax_total',
        'grand_total',
        'paid_amount',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(RestaurantOrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(RestaurantPayment::class);
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(RestaurantOrderTax::class);
    }

    public function getRemainingAmountAttribute()
    {
        return max(0, $this->grand_total - $this->paid_amount);
    }

    public function getIsFullyPaidAttribute()
    {
        return $this->paid_amount >= $this->grand_total;
    }
}
