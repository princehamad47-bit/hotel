<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\MenuItem;
use App\Models\Reservation;
use App\Models\RestaurantOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestaurantOrderController extends Controller
{
    public function index()
    {
        $restaurantOrders = RestaurantOrder::with(['reservation', 'guest'])
            ->latest()
            ->paginate(15);

        return view('restaurant-orders.index', compact('restaurantOrders'));
    }

    public function create(Request $request)
    {
        $reservations = Reservation::with('guest')
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->orderByDesc('id')
            ->get();

        $guests = Guest::orderBy('first_name')->get();

        $menuItems = MenuItem::with('menuCategory')
            ->where('is_available', true)
            ->orderBy('name')
            ->get();

        $selectedReservationId = $request->reservation_id;

        return view('restaurant-orders.create', compact(
            'reservations',
            'guests',
            'menuItems',
            'selectedReservationId'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_type' => ['required', 'in:in_house,walk_in,room_service,takeaway'],
            'reservation_id' => ['nullable', 'exists:reservations,id'],
            'guest_id' => ['nullable', 'exists:guests,id'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:100'],
            'table_number' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:pending,preparing,served,completed,cancelled'],
            'notes' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_item_id' => ['required', 'exists:menu_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $restaurantOrder = RestaurantOrder::create([
                'order_code' => $this->generateOrderCode(),
                'reservation_id' => $validated['reservation_id'] ?? null,
                'guest_id' => $validated['guest_id'] ?? null,
                'customer_name' => $validated['customer_name'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'order_type' => $validated['order_type'],
                'table_number' => $validated['table_number'] ?? null,
                'status' => $validated['status'],
                'subtotal' => 0,
                'tax_total' => 0,
                'grand_total' => 0,
                'paid_amount' => 0,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $itemData) {
                $menuItem = MenuItem::findOrFail($itemData['menu_item_id']);
                $quantity = (int) $itemData['quantity'];
                $lineSubtotal = $menuItem->price * $quantity;

                $restaurantOrder->items()->create([
                    'menu_item_id' => $menuItem->id,
                    'item_name' => $menuItem->name,
                    'unit_price' => $menuItem->price,
                    'quantity' => $quantity,
                    'subtotal' => $lineSubtotal,
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            $this->refreshOrderTotals($restaurantOrder);
        });

        return redirect()
            ->route('restaurant-orders.index')
            ->with('success', 'Restaurant order created successfully.');
    }

    public function show(RestaurantOrder $restaurantOrder)
    {
        $restaurantOrder->load([
            'reservation.guest',
            'guest',
            'items.menuItem.menuCategory',
            'payments',
            'taxes',
        ]);

        return view('restaurant-orders.show', compact('restaurantOrder'));
    }

    public function invoice(RestaurantOrder $restaurantOrder)
    {
        $restaurantOrder->load([
            'reservation.guest',
            'guest',
            'items.menuItem.menuCategory',
            'payments',
            'taxes',
        ]);

        return view('restaurant-orders.invoice', compact('restaurantOrder'));
    }

    public function edit(RestaurantOrder $restaurantOrder)
    {
        $restaurantOrder->load('items');

        $reservations = Reservation::with('guest')
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->orderByDesc('id')
            ->get();

        $guests = Guest::orderBy('first_name')->get();

        $menuItems = MenuItem::with('menuCategory')
            ->where('is_available', true)
            ->orderBy('name')
            ->get();

        return view('restaurant-orders.edit', compact(
            'restaurantOrder',
            'reservations',
            'guests',
            'menuItems'
        ));
    }

    public function update(Request $request, RestaurantOrder $restaurantOrder)
    {
        $validated = $request->validate([
            'order_type' => ['required', 'in:in_house,walk_in,room_service,takeaway'],
            'reservation_id' => ['nullable', 'exists:reservations,id'],
            'guest_id' => ['nullable', 'exists:guests,id'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:100'],
            'table_number' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:pending,preparing,served,completed,cancelled'],
            'notes' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_item_id' => ['required', 'exists:menu_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated, $restaurantOrder) {
            $restaurantOrder->update([
                'reservation_id' => $validated['reservation_id'] ?? null,
                'guest_id' => $validated['guest_id'] ?? null,
                'customer_name' => $validated['customer_name'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'order_type' => $validated['order_type'],
                'table_number' => $validated['table_number'] ?? null,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $restaurantOrder->items()->delete();

            foreach ($validated['items'] as $itemData) {
                $menuItem = MenuItem::findOrFail($itemData['menu_item_id']);
                $quantity = (int) $itemData['quantity'];
                $lineSubtotal = $menuItem->price * $quantity;

                $restaurantOrder->items()->create([
                    'menu_item_id' => $menuItem->id,
                    'item_name' => $menuItem->name,
                    'unit_price' => $menuItem->price,
                    'quantity' => $quantity,
                    'subtotal' => $lineSubtotal,
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            $this->refreshOrderTotals($restaurantOrder);
        });

        return redirect()
            ->route('restaurant-orders.show', $restaurantOrder)
            ->with('success', 'Restaurant order updated successfully.');
    }

    public function destroy(RestaurantOrder $restaurantOrder)
    {
        $restaurantOrder->delete();

        return redirect()
            ->route('restaurant-orders.index')
            ->with('success', 'Restaurant order deleted successfully.');
    }

    protected function generateOrderCode(): string
    {
        $hotelCode = strtoupper(config('app.hotel_code', 'HOTEL'));
        $datePart = now()->format('Ymd');
        $todayCount = RestaurantOrder::whereDate('created_at', now()->toDateString())->count() + 1;
        $sequence = str_pad($todayCount, 4, '0', STR_PAD_LEFT);

        return "RST-{$hotelCode}-{$datePart}-{$sequence}";
    }

    private function refreshOrderTotals(RestaurantOrder $restaurantOrder): void
    {
        $restaurantOrder->loadMissing('taxes');

        $subtotal = $restaurantOrder->items()->sum('subtotal');
        $taxTotal = 0;

        foreach ($restaurantOrder->taxes as $restaurantTax) {
            $taxAmount = $restaurantTax->tax_type === 'percentage'
                ? ($subtotal * $restaurantTax->tax_value) / 100
                : $restaurantTax->tax_value;

            $taxAmount = round($taxAmount, 2);

            $restaurantTax->update([
                'tax_amount' => $taxAmount,
            ]);

            $taxTotal += $taxAmount;
        }

        $restaurantOrder->update([
            'subtotal' => $subtotal,
            'tax_total' => $taxTotal,
            'grand_total' => $subtotal + $taxTotal,
        ]);
    }
}
