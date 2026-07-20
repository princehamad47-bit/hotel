<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = [
        'name',
        'type',
        'value',
        'is_active',
        'description',
    ];

    public function reservationTaxes()
    {
        return $this->hasMany(ReservationTax::class);
    }
}
