<?php

namespace App\Http\Controllers;

use App\Models\RestaurantOrder;
use App\Models\Tax;
use Illuminate\Http\Request;

class RestaurantOrderTaxController extends Controller
{
    public function create(RestaurantOrder $restaurantOrder)
    {
        $taxes = Tax::where('is_active', true)->orderBy('name')->get();

        return view('restaurant-order-taxes.create', compact('restaurantOrder', 'taxes'));
    }

    public function store(Request $request, RestaurantOrder $restaurantOrder)
    {
        $validated = $request->validate([
            'taxes' => ['required', 'array', 'min:1'],
            'taxes.*' => ['required', 'exists:taxes,id'],
        ]);

        $subtotal = $restaurantOrder->items()->sum('subtotal');

        foreach ($validated['taxes'] as $taxId) {
            $tax = Tax::findOrFail($taxId);

            $alreadyExists = $restaurantOrder->taxes()
                ->where('tax_id', $tax->id)
                ->exists();

            if ($alreadyExists) {
                continue;
            }

            $taxAmount = $tax->type === 'percentage'
                ? ($subtotal * $tax->value) / 100
                : $tax->value;

            $restaurantOrder->taxes()->create([
                'tax_id' => $tax->id,
                'tax_name' => $tax->name,
                'tax_type' => $tax->type,
                'tax_value' => $tax->value,
                'tax_amount' => round($taxAmount, 2),
            ]);
        }

        $this->refreshRestaurantOrderTotals($restaurantOrder);

        return redirect()
            ->route('restaurant-orders.show', $restaurantOrder)
            ->with('success', 'Restaurant tax applied successfully.');
    }

    public function destroy(RestaurantOrder $restaurantOrder, $restaurantOrderTaxId)
    {
        $restaurantOrderTax = $restaurantOrder->taxes()->findOrFail($restaurantOrderTaxId);
        $restaurantOrderTax->delete();

        $this->refreshRestaurantOrderTotals($restaurantOrder);

        return redirect()
            ->route('restaurant-orders.show', $restaurantOrder)
            ->with('success', 'Restaurant tax removed successfully.');
    }

    private function refreshRestaurantOrderTotals(RestaurantOrder $restaurantOrder): void
    {
        $subtotal = $restaurantOrder->items()->sum('subtotal');
        $taxTotal = $restaurantOrder->taxes()->sum('tax_amount');

        $restaurantOrder->update([
            'subtotal' => $subtotal,
            'tax_total' => $taxTotal,
            'grand_total' => $subtotal + $taxTotal,
        ]);
    }
}
