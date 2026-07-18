<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Invoice</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f8fafc;
            color: #222;
            margin: 0;
            padding: 30px;
        }

        .invoice-wrapper {
            max-width: 1000px;
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
            width: 350px;
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
    $roomTotal = $reservation->reservationRooms->sum('subtotal');
    $serviceTotal = $reservation->reservationServices->sum('total_price');
    $paidTotal = $reservation->payments->where('payment_status', 'paid')->sum('amount');
    $remainingTotal = $reservation->total_amount - $paidTotal;
    @endphp

    <div class="invoice-wrapper">
        <div class="topbar">
            <div>
                <div class="title">Reservation Invoice</div>
                <div class="muted">Invoice for reservation {{ $reservation->reservation_code }}</div>
            </div>

            <div class="detail-box">
                <p><strong>Reservation Code:</strong> {{ $reservation->reservation_code }}</p>
                <p><strong>Status:</strong> {{ str_replace('_', ' ', ucfirst($reservation->status)) }}</p>
                <p><strong>Invoice Date:</strong> {{ now()->format('Y-m-d H:i') }}</p>
            </div>
        </div>

        <div class="grid-2">
            <div class="section">
                <h3>Guest Details</h3>
                <div class="detail-box">
                    <p><strong>Name:</strong> {{ $reservation->guest->first_name }} {{ $reservation->guest->last_name }}</p>
                    <p><strong>Phone:</strong> {{ $reservation->guest->phone ?? '-' }}</p>
                    <p><strong>Email:</strong> {{ $reservation->guest->email ?? '-' }}</p>
                    <p><strong>Address:</strong> {{ $reservation->guest->address ?? '-' }}</p>
                </div>
            </div>

            <div class="section">
                <h3>Reservation Details</h3>
                <div class="detail-box">
                    <p><strong>Check In Date:</strong> {{ $reservation->check_in_date->format('Y-m-d') }}</p>
                    <p><strong>Check Out Date:</strong> {{ $reservation->check_out_date->format('Y-m-d') }}</p>
                    <p><strong>Adults:</strong> {{ $reservation->adults }}</p>
                    <p><strong>Children:</strong> {{ $reservation->children }}</p>
                    <p><strong>Booking Source:</strong> {{ $reservation->booking_source ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="section">
            <h3>Room Charges</h3>
            <table>
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Room Type</th>
                        <th>Rate</th>
                        <th>Nights</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reservation->reservationRooms as $item)
                    <tr>
                        <td>{{ $item->room->room_number }}</td>
                        <td>{{ $item->room->roomType->name }}</td>
                        <td>{{ number_format($item->room_rate, 2) }}</td>
                        <td>{{ $item->nights }}</td>
                        <td>{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">No room charges found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="section">
            <h3>Service Charges</h3>
            <table>
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Room</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reservation->reservationServices as $item)
                    <tr>
                        <td>{{ $item->service->name }}</td>
                        <td>{{ $item->room?->room_number ?? '-' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $item->status)) }}</td>
                        <td>{{ number_format($item->total_price, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">No service charges found.</td>
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
                    @forelse ($reservation->payments as $payment)
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
                    <td>Room Total</td>
                    <td>{{ number_format($roomTotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Service Total</td>
                    <td>{{ number_format($serviceTotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Grand Total</td>
                    <td>{{ number_format($reservation->total_amount, 2) }}</td>
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
            <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</body>

</html>