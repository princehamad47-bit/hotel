@extends('layouts.app')

@section('content')
<h1 class="page-title">Edit Restaurant Order</h1>

<div class="card">
    @can('module-access', ['restaurant-orders', 'update'])
    <form method="POST" action="{{ route('restaurant-orders.update', $restaurantOrder) }}">
        @csrf
        @method('PUT')

        <div class="grid-2">
            <div class="form-group">
                <label>Order Type</label>
                <select name="order_type" id="order_type" required>
                    <option value="walk_in" @selected(old('order_type', $restaurantOrder->order_type) == 'walk_in')>Walk In</option>
                    <option value="in_house" @selected(old('order_type', $restaurantOrder->order_type) == 'in_house')>In House</option>
                    <option value="room_service" @selected(old('order_type', $restaurantOrder->order_type) == 'room_service')>Room Service</option>
                    <option value="takeaway" @selected(old('order_type', $restaurantOrder->order_type) == 'takeaway')>Takeaway</option>
                </select>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="pending" @selected(old('status', $restaurantOrder->status) == 'pending')>Pending</option>
                    <option value="preparing" @selected(old('status', $restaurantOrder->status) == 'preparing')>Preparing</option>
                    <option value="served" @selected(old('status', $restaurantOrder->status) == 'served')>Served</option>
                    <option value="completed" @selected(old('status', $restaurantOrder->status) == 'completed')>Completed</option>
                    <option value="cancelled" @selected(old('status', $restaurantOrder->status) == 'cancelled')>Cancelled</option>
                </select>
            </div>
        </div>

        <div id="reservation-box" style="{{ in_array(old('order_type', $restaurantOrder->order_type), ['in_house', 'room_service']) ? '' : 'display:none;' }}">
            <div class="grid-2">
                <div class="form-group">
                    <label>Reservation</label>
                    <select name="reservation_id" id="reservation_id">
                        <option value="">Select Reservation</option>
                        @foreach ($reservations as $reservation)
                        <option value="{{ $reservation->id }}"
                            data-guest-id="{{ $reservation->guest?->id }}"
                            @selected(old('reservation_id', $restaurantOrder->reservation_id) == $reservation->id)>
                            {{ $reservation->reservation_code }} -
                            {{ $reservation->guest?->first_name }} {{ $reservation->guest?->last_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Guest</label>
                    <select name="guest_id" id="guest_id">
                        <option value="">Select Guest</option>
                        @foreach ($guests as $guest)
                        <option value="{{ $guest->id }}" @selected(old('guest_id', $restaurantOrder->guest_id) == $guest->id)>
                            {{ $guest->first_name }} {{ $guest->last_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div id="walkin-box" style="{{ old('order_type', $restaurantOrder->order_type) === 'walk_in' ? '' : 'display:none;' }}">
            <div class="grid-2">
                <div class="form-group">
                    <label>Customer Name</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name', $restaurantOrder->customer_name) }}">
                </div>

                <div class="form-group">
                    <label>Customer Phone</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone', $restaurantOrder->customer_phone) }}">
                </div>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Table Number</label>
                <input type="text" name="table_number" value="{{ old('table_number', $restaurantOrder->table_number) }}">
            </div>

            <div class="form-group">
                <label>Notes</label>
                <input type="text" name="notes" value="{{ old('notes', $restaurantOrder->notes) }}">
            </div>
        </div>

        <div class="card" style="margin-top: 10px;">
            <h3 class="section-title">Order Items</h3>

            <div id="order-items-wrapper">
                @php
                $oldItems = old('items', $restaurantOrder->items->map(function ($item) {
                return [
                'menu_item_id' => $item->menu_item_id,
                'quantity' => $item->quantity,
                'notes' => $item->notes,
                ];
                })->toArray());
                @endphp

                @foreach ($oldItems as $index => $oldItem)
                <div class="order-item-row" style="border:1px solid #dbe3ee; border-radius:8px; padding:15px; margin-bottom:12px;">
                    <div class="grid-3">
                        <div class="form-group">
                            <label>Menu Item</label>
                            <select name="items[{{ $index }}][menu_item_id]" required>
                                <option value="">Select Item</option>
                                @foreach ($menuItems as $menuItem)
                                <option value="{{ $menuItem->id }}" @selected(($oldItem['menu_item_id'] ?? '' )==$menuItem->id)>
                                    {{ $menuItem->name }} ({{ $menuItem->menuCategory->name }}) - {{ number_format($menuItem->price, 2) }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" min="1" name="items[{{ $index }}][quantity]" value="{{ $oldItem['quantity'] ?? 1 }}" required>
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <input type="text" name="items[{{ $index }}][notes]" value="{{ $oldItem['notes'] ?? '' }}">
                        </div>
                    </div>

                    <button type="button" class="btn btn-danger remove-item-btn">Remove</button>
                </div>
                @endforeach
            </div>

            <button type="button" id="add-item-btn" class="btn btn-secondary">+ Add Another Item</button>
        </div>

        <button type="submit" class="btn btn-success">Update Restaurant Order</button>
        <a href="{{ route('restaurant-orders.index') }}" class="btn btn-secondary">Back</a>
    </form>
    @else
    <p class="muted">You do not have permission to edit restaurant orders.</p>

    @can('module-access', ['restaurant-orders', 'read'])
    <a href="{{ route('restaurant-orders.show', $restaurantOrder) }}" class="btn btn-secondary">Back</a>
    @endcan
    @endcan
</div>

@can('module-access', ['restaurant-orders', 'update'])
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderType = document.getElementById('order_type');
        const reservationBox = document.getElementById('reservation-box');
        const walkinBox = document.getElementById('walkin-box');
        const reservationSelect = document.getElementById('reservation_id');
        const guestSelect = document.getElementById('guest_id');
        const wrapper = document.getElementById('order-items-wrapper');
        const addBtn = document.getElementById('add-item-btn');

        function toggleCustomerSections() {
            const value = orderType.value;

            if (value === 'in_house' || value === 'room_service') {
                reservationBox.style.display = '';
                walkinBox.style.display = 'none';
            } else if (value === 'walk_in') {
                reservationBox.style.display = 'none';
                walkinBox.style.display = '';
            } else {
                reservationBox.style.display = 'none';
                walkinBox.style.display = 'none';
            }
        }

        orderType.addEventListener('change', toggleCustomerSections);
        toggleCustomerSections();

        if (reservationSelect) {
            reservationSelect.addEventListener('change', function() {
                const selected = reservationSelect.options[reservationSelect.selectedIndex];
                const guestId = selected.getAttribute('data-guest-id');

                if (guestId && guestSelect) {
                    guestSelect.value = guestId;
                }
            });
        }

        function bindRemoveButtons() {
            document.querySelectorAll('.remove-item-btn').forEach(function(btn) {
                btn.onclick = function() {
                    const rows = wrapper.querySelectorAll('.order-item-row');
                    if (rows.length > 1) {
                        btn.closest('.order-item-row').remove();
                    }
                };
            });
        }

        addBtn.addEventListener('click', function() {
            const index = wrapper.querySelectorAll('.order-item-row').length;

            const row = document.createElement('div');
            row.className = 'order-item-row';
            row.style.border = '1px solid #dbe3ee';
            row.style.borderRadius = '8px';
            row.style.padding = '15px';
            row.style.marginBottom = '12px';

            row.innerHTML = `
                <div class="grid-3">
                    <div class="form-group">
                        <label>Menu Item</label>
                        <select name="items[${index}][menu_item_id]" required>
                            <option value="">Select Item</option>
                            @foreach ($menuItems as $menuItem)
                                <option value="{{ $menuItem->id }}">
                                    {{ $menuItem->name }} ({{ $menuItem->menuCategory->name }}) - {{ number_format($menuItem->price, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" min="1" name="items[${index}][quantity]" value="1" required>
                    </div>

                    <div class="form-group">
                        <label>Notes</label>
                        <input type="text" name="items[${index}][notes]" value="">
                    </div>
                </div>

                <button type="button" class="btn btn-danger remove-item-btn">Remove</button>
            `;

            wrapper.appendChild(row);
            bindRemoveButtons();
        });

        bindRemoveButtons();
    });
</script>
@endcan
@endsection