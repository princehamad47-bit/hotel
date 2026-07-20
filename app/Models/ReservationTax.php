<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationTax extends Model
{
    protected $fillable = [
        'reservation_id',
        'tax_id',
        'tax_name',
        'tax_type',
        'tax_value',
        'tax_amount',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }
}
