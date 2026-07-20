<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Invoice</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f8fafc;
            color: #222;
            margin: 0;
            padding: 30px;
        }

        .invoice-wrapper {
            max-width: 950px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            gap: 20px;
        }

        .title {
            font-size: 30px;
            font-weight: bold;
            color: #1e3a5f;
            margin-bottom: 8px;
        }

        .muted {
            color: #64748b;
            font-size: 14px;
        }

        .section {
            margin-top: 30px;
        }

        .section h3 {
            margin-bottom: 12px;
            color: #1e3a5f;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 8px;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .detail-box p {
            margin: 8px 0;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        table th,
        table td {
            border: 1px solid #dbe3ee;
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }

        table th {
            background: #f1f5f9;
            color: #1e3a5f;
        }

        .summary {
            margin-top: 20px;
            width: 380px;
            margin-left: auto;
        }

        .summary table td {
            font-weight: bold;
        }

        .actions {
            margin-top: 30px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-secondary {
            background: #64748b;
            color: #fff;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .invoice-wrapper {
                box-shadow: none;
                border-radius: 0;
                padding: 0;
            }

            .actions {
                display: none;
            }
        }
    </style>
</head>

<body>
    @php
    $paidTotal = $restaurantOrder->payments->where('payment_status', 'paid')->sum('amount');
    $remainingTotal = $restaurantOrder->grand_total - $paidTotal;
    @endphp

    <div class="invoice-wrapper">
        <div class="topbar">
            <div>
                <div class="title">Restaurant Invoice</div>
                <div class="muted">Invoice for order {{ $restaurantOrder->order_code }}</div>
            </div>

            <div class="detail-box">
                <p><strong>Order Code:</strong> {{ $restaurantOrder->order_code }}</p>
                <p><strong>Status:</strong> {{ ucfirst($restaurantOrder->status) }}</p>
                <p><strong>Invoice Date:</strong> {{ now()->format('Y-m-d H:i') }}</p>
            </div>
        </div>

        <div class="grid-2">
            <div class="section">
                <h3>Customer Details</h3>
                <div class="detail-box">
                    <p>
                        <strong>Name:</strong>
                        @if ($restaurantOrder->guest)
                        {{ $restaurantOrder->guest->first_name }} {{ $restaurantOrder->guest->last_name }}
                        @elseif ($restaurantOrder->reservation?->guest)
                        {{ $restaurantOrder->reservation->guest->first_name }} {{ $restaurantOrder->reservation->guest->last_name }}
                        @else
                        {{ $restaurantOrder->customer_name ?? '-' }}
                        @endif
                    </p>
                    <p>
                        <strong>Phone:</strong>
                        {{ $restaurantOrder->customer_phone ?? ($restaurantOrder->guest?->phone ?? $restaurantOrder->reservation?->guest?->phone ?? '-') }}
                    </p>
                    <p><strong>Order Type:</strong> {{ ucwords(str_replace('_', ' ', $restaurantOrder->order_type)) }}</p>
                    <p><strong>Table Number:</strong> {{ $restaurantOrder->table_number ?? '-' }}</p>
                </div>
            </div>

            <div class="section">
                <h3>Reference Details</h3>
                <div class="detail-box">
                    <p><strong>Reservation:</strong> {{ $restaurantOrder->reservation?->reservation_code ?? '-' }}</p>
                    <p><strong>Notes:</strong> {{ $restaurantOrder->notes ?? '-' }}</p>
                    <p><strong>Total Items:</strong> {{ $restaurantOrder->items->count() }}</p>
                </div>
            </div>
        </div>

        <div class="section">
            <h3>Order Items</h3>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Category</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($restaurantOrder->items as $item)
                    <tr>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->menuItem?->menuCategory?->name ?? '-' }}</td>
                        <td>{{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">No order items found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="section">
            <h3>Taxes</h3>
            <table>
                <thead>
                    <tr>
                        <th>Tax Name</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Tax Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($restaurantOrder->taxes as $tax)
                    <tr>
                        <td>{{ $tax->tax_name }}</td>
                        <td>{{ ucfirst($tax->tax_type) }}</td>
                        <td>
                            @if ($tax->tax_type === 'percentage')
                            {{ number_format($tax->tax_value, 2) }}%
                            @else
                            {{ number_format($tax->tax_value, 2) }}
                            @endif
                        </td>
                        <td>{{ number_format($tax->tax_amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">No taxes applied.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="section">
            <h3>Payments</h3>
            <table>
                <thead>
                    <tr>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Reference</th>
                        <th>Paid At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($restaurantOrder->payments as $payment)
                    <tr>
                        <td>{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                        <td>{{ ucfirst($payment->payment_status) }}</td>
                        <td>{{ $payment->transaction_ref ?? '-' }}</td>
                        <td>{{ $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i') : '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">No payments found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="summary">
            <table>
                <tr>
                    <td>Subtotal</td>
                    <td>{{ number_format($restaurantOrder->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Tax Total</td>
                    <td>{{ number_format($restaurantOrder->tax_total, 2) }}</td>
                </tr>
                <tr>
                    <td>Grand Total</td>
                    <td>{{ number_format($restaurantOrder->grand_total, 2) }}</td>
                </tr>
                <tr>
                    <td>Paid</td>
                    <td>{{ number_format($paidTotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Remaining</td>
                    <td>{{ number_format($remainingTotal, 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="actions">
            <button onclick="window.print()" class="btn btn-primary">Print Invoice</button>
            <a href="{{ route('restaurant-orders.show', $restaurantOrder) }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</body>

</html>