<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; gap:12px; flex-wrap:wrap;">
        <h3 class="section-title" style="margin-bottom:0;">Taxes</h3>

        @can('module-access', ['reservations', 'create'])
        <a href="{{ route('reservation-taxes.create', $reservation) }}" class="btn btn-primary">+ Apply Tax</a>
        @endcan
    </div>

    @if ($reservation->taxes->count())
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Tax Name</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Tax Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reservation->taxes as $reservationTax)
                <tr>
                    <td>{{ $reservationTax->tax_name }}</td>
                    <td>{{ ucfirst($reservationTax->tax_type) }}</td>
                    <td>
                        @if ($reservationTax->tax_type === 'percentage')
                        {{ number_format($reservationTax->tax_value, 2) }}%
                        @else
                        {{ number_format($reservationTax->tax_value, 2) }}
                        @endif
                    </td>
                    <td>{{ number_format($reservationTax->tax_amount, 2) }}</td>
                    <td>
                        @can('module-access', ['reservations', 'delete'])
                        <form action="{{ route('reservation-taxes.destroy', [$reservation, $reservationTax->id]) }}" method="POST" class="inline-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Remove this tax?')">
                                Remove
                            </button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">
        <p><strong>Room Total:</strong> {{ number_format($reservation->room_total, 2) }}</p>
        <p><strong>Service Total:</strong> {{ number_format($reservation->service_total, 2) }}</p>
        <p><strong>Subtotal:</strong> {{ number_format($reservation->sub_total, 2) }}</p>
        <p><strong>Total Tax:</strong> {{ number_format($reservation->tax_total, 2) }}</p>
        <p><strong>Grand Total:</strong> {{ number_format($reservation->grand_total, 2) }}</p>
    </div>
    @else
    <p>No taxes applied yet.</p>
    <p><strong>Subtotal:</strong> {{ number_format($reservation->sub_total, 2) }}</p>
    <p><strong>Grand Total:</strong> {{ number_format($reservation->grand_total, 2) }}</p>
    @endif
</div>