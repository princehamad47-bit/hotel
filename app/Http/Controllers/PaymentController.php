<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function create(Reservation $reservation)
    {
        $reservation->load('guest');

        return view('payments.create', compact('reservation'));
    }

    public function store(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,card,bank_transfer'],
            'payment_status' => ['required', 'in:pending,paid,refunded'],
            'transaction_ref' => ['nullable', 'string', 'max:255'],
            'paid_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $remainingAmount = $reservation->total_amount - $reservation->paid_amount;

        if ($validated['payment_status'] === 'paid' && $validated['amount'] > $remainingAmount) {
            return back()
                ->withErrors([
                    'amount' => 'Payment amount cannot be greater than remaining amount.'
                ])
                ->withInput();
        }

        Payment::create([
            'reservation_id' => $reservation->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'payment_status' => $validated['payment_status'],
            'transaction_ref' => $validated['transaction_ref'] ?? null,
            'paid_at' => $validated['paid_at'] ?? now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        $this->updateReservationPaidAmount($reservation);

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('success', 'Payment added successfully.');
    }

    private function updateReservationPaidAmount(Reservation $reservation): void
    {
        $paidAmount = $reservation->payments()
            ->where('payment_status', 'paid')
            ->sum('amount');

        $reservation->update([
            'paid_amount' => $paidAmount,
        ]);
    }
}
