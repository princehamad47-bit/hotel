<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Tax;
use Illuminate\Http\Request;

class ReservationTaxController extends Controller
{
    public function create(Reservation $reservation)
    {
        $taxes = Tax::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('reservation-taxes.create', compact('reservation', 'taxes'));
    }

    public function store(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'taxes' => ['required', 'array', 'min:1'],
            'taxes.*' => ['required', 'exists:taxes,id'],
        ]);

        $subtotal = $reservation->reservationRooms()->sum('subtotal')
            + $reservation->reservationServices()->sum('total_price');

        foreach ($validated['taxes'] as $taxId) {
            $tax = Tax::findOrFail($taxId);

            $alreadyExists = $reservation->taxes()
                ->where('tax_id', $tax->id)
                ->exists();

            if ($alreadyExists) {
                continue;
            }

            $taxAmount = $tax->type === 'percentage'
                ? ($subtotal * $tax->value) / 100
                : $tax->value;

            $reservation->taxes()->create([
                'tax_id' => $tax->id,
                'tax_name' => $tax->name,
                'tax_type' => $tax->type,
                'tax_value' => $tax->value,
                'tax_amount' => round($taxAmount, 2),
            ]);
        }

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('success', 'Tax applied successfully.');
    }

    public function destroy(Reservation $reservation, $reservationTaxId)
    {
        $reservationTax = $reservation->taxes()->findOrFail($reservationTaxId);

        $reservationTax->delete();

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('success', 'Tax removed successfully.');
    }
}
