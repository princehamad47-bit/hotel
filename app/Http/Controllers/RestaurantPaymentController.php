<?php

namespace App\Http\Controllers;

use App\Models\RestaurantOrder;
use App\Models\RestaurantPayment;
use Illuminate\Http\Request;

class RestaurantPaymentController extends Controller
{
    public function create(RestaurantOrder $restaurantOrder)
    {
        $restaurantOrder->load(['guest', 'reservation.guest']);

        return view('restaurant-payments.create', compact('restaurantOrder'));
    }

    public function store(Request $request, RestaurantOrder $restaurantOrder)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,card,bank_transfer'],
            'payment_status' => ['required', 'in:pending,paid,refunded'],
            'transaction_ref' => ['nullable', 'string', 'max:255'],
            'paid_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $remainingAmount = $restaurantOrder->grand_total - $restaurantOrder->paid_amount;

        if ($validated['payment_status'] === 'paid' && $validated['amount'] > $remainingAmount) {
            return back()
                ->withErrors([
                    'amount' => 'Payment amount cannot be greater than remaining balance.'
                ])
                ->withInput();
        }

        RestaurantPayment::create([
            'restaurant_order_id' => $restaurantOrder->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'payment_status' => $validated['payment_status'],
            'transaction_ref' => $validated['transaction_ref'] ?? null,
            'paid_at' => $validated['paid_at'] ?? now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        $this->updateRestaurantOrderPaidAmount($restaurantOrder);

        return redirect()
            ->route('restaurant-orders.show', $restaurantOrder)
            ->with('success', 'Restaurant payment added successfully.');
    }

    private function updateRestaurantOrderPaidAmount(RestaurantOrder $restaurantOrder): void
    {
        $paidAmount = $restaurantOrder->payments()
            ->where('payment_status', 'paid')
            ->sum('amount');

        $restaurantOrder->update([
            'paid_amount' => $paidAmount,
        ]);
    }
}
