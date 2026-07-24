<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Management System</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f7fb;
            color: #222;
            overflow-x: hidden;
        }

        .navbar {
            background: #1e3a5f;
            color: #fff;
            padding: 14px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            position: relative;
            z-index: 100;
            overflow: visible;
        }

        .navbar h2 {
            font-size: 22px;
            margin: 0;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
            position: relative;
            overflow: visible;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            font-size: 15px;
            line-height: 1;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        .dropdown {
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        .dropdown-toggle {
            background: transparent;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 15px;
            padding: 8px 0;
            font-family: inherit;
            line-height: 1;
        }

        .dropdown-toggle:hover {
            text-decoration: underline;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            margin-top: 6px;
            min-width: 230px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.12);
            padding: 8px 0;
            z-index: 1000;
        }

        .dropdown-menu::before {
            content: "";
            position: absolute;
            top: -8px;
            left: 0;
            right: 0;
            height: 8px;
        }

        .dropdown-menu a {
            display: block;
            padding: 10px 14px;
            color: #1e3a5f !important;
            text-decoration: none;
            font-size: 14px;
            white-space: nowrap;
        }

        .dropdown-menu a:hover {
            background: #f1f5f9;
            text-decoration: none;
        }

        .dropdown:hover .dropdown-menu,
        .dropdown-menu:hover {
            display: block;
        }

        .hotel-user-wrap {
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        .hotel-user-circle {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #fff;
            color: #1e3a5f;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 15px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            user-select: none;
        }

        .hotel-user-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 6px;
            min-width: 220px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.12);
            padding: 12px;
            z-index: 1000;
        }

        .hotel-user-menu::before {
            content: "";
            position: absolute;
            top: -8px;
            left: 0;
            right: 0;
            height: 8px;
        }

        .hotel-user-wrap:hover .hotel-user-menu,
        .hotel-user-menu:hover {
            display: block;
        }

        .hotel-user-name {
            color: #1e3a5f;
            font-weight: bold;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .hotel-user-role {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 12px;
        }

        .logout-btn {
            width: 100%;
            padding: 10px 14px;
            border: none;
            border-radius: 8px;
            background: #dc2626;
            color: #fff;
            cursor: pointer;
            font-size: 14px;
        }

        .logout-btn:hover {
            opacity: 0.92;
        }

        .container {
            width: min(1450px, calc(100% - 40px));
            margin: 30px auto;
        }

        .page-title {
            margin-bottom: 20px;
            font-size: 28px;
            color: #1e3a5f;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
            padding: 20px;
            margin-bottom: 20px;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"],
        input[type="date"],
        input[type="datetime-local"],
        input[type="number"],
        input[type="password"],
        select,
        textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cfd8e3;
            border-radius: 8px;
            font-size: 14px;
            background: #fff;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
            margin-right: 8px;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-success {
            background: #16a34a;
            color: #fff;
        }

        .btn-warning {
            background: #d97706;
            color: #fff;
        }

        .btn-danger {
            background: #dc2626;
            color: #fff;
        }

        .btn-secondary {
            background: #64748b;
            color: #fff;
        }

        .btn:hover {
            opacity: 0.92;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .table-responsive {
            overflow-x: auto;
            width: 100%;
        }

        table {
            width: 100%;
            min-width: 1150px;
            border-collapse: collapse;
            background: #fff;
            margin-top: 12px;
        }

        table th,
        table td {
            padding: 14px 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            font-size: 14px;
            vertical-align: top;
        }

        table th {
            background: #f8fafc;
            color: #1e3a5f;
        }

        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-confirmed {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-checked_in {
            background: #dcfce7;
            color: #166534;
        }

        .badge-checked_out {
            background: #e0e7ff;
            color: #4338ca;
        }

        .badge-cancelled,
        .badge-no_show {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-available {
            background: #dcfce7;
            color: #166534;
        }

        .badge-occupied {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-maintenance {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-cleaning {
            background: #fef3c7;
            color: #92400e;
        }

        .room-box {
            padding: 12px;
            border: 1px solid #dbe3ee;
            border-radius: 8px;
            margin-bottom: 10px;
            background: #f9fbfd;
        }

        .details-list p {
            margin-bottom: 10px;
            font-size: 15px;
        }

        .section-title {
            font-size: 20px;
            color: #1e3a5f;
            margin-bottom: 15px;
        }

        .inline-form {
            display: inline;
        }

        ul.simple-list {
            padding-left: 18px;
        }

        ul.simple-list li {
            margin-bottom: 8px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 22px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        }

        .stat-title {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 30px;
            font-weight: bold;
            color: #1e3a5f;
        }

        .quick-links {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .muted {
            color: #64748b;
            font-size: 14px;
        }

        .room-board-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .room-status-card {
            border-radius: 12px;
            padding: 18px;
            background: #fff;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
            border-left: 6px solid #ddd;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .room-status-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.10);
        }

        .room-status-card h4 {
            color: #1e3a5f;
            font-size: 18px;
        }

        .room-status-card p {
            font-size: 14px;
        }

        .room-available {
            border-left-color: #16a34a;
        }

        .room-occupied {
            border-left-color: #2563eb;
        }

        .room-cleaning {
            border-left-color: #d97706;
        }

        .room-maintenance {
            border-left-color: #dc2626;
        }

        .selectable-room-card {
            cursor: pointer;
        }

        .selected-room-card {
            border-left-color: #2563eb !important;
            background: #eff6ff !important;
            box-shadow: 0 0 0 2px #93c5fd;
        }

        .payment-progress-wrap {
            margin-bottom: 14px;
        }

        .payment-progress-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            gap: 12px;
            flex-wrap: wrap;
        }

        .payment-progress-bar {
            width: 100%;
            height: 14px;
            appearance: none;
            -webkit-appearance: none;
            border: none;
            border-radius: 999px;
            overflow: hidden;
            background: #e5e7eb;
        }

        .payment-progress-bar::-webkit-progress-bar {
            background: #e5e7eb;
            border-radius: 999px;
        }

        .payment-progress-bar.paid-full::-webkit-progress-value {
            background: #16a34a;
            border-radius: 999px;
        }

        .payment-progress-bar.paid-partial::-webkit-progress-value {
            background: #2563eb;
            border-radius: 999px;
        }

        .payment-progress-bar.paid-full::-moz-progress-bar {
            background: #16a34a;
            border-radius: 999px;
        }

        .payment-progress-bar.paid-partial::-moz-progress-bar {
            background: #2563eb;
            border-radius: 999px;
        }

        .action-buttons {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .action-buttons .inline-form {
            margin: 0;
        }

        .action-buttons .btn {
            white-space: nowrap;
        }

        table td:last-child {
            min-width: 180px;
        }

        @media (max-width: 992px) {
            .container {
                width: min(100%, calc(100% - 24px));
            }

            .stats-grid,
            .room-board-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {

            .grid-2,
            .grid-3 {
                grid-template-columns: 1fr;
            }

            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-links {
                gap: 12px;
            }

            table {
                min-width: 900px;
            }
        }

        @media (max-width: 576px) {

            .stats-grid,
            .room-board-grid {
                grid-template-columns: 1fr;
            }
        }

        @media print {

            .navbar,
            .no-print,
            .btn,
            form,
            .pagination {
                display: none !important;
            }

            body {
                background: #fff !important;
            }

            .container {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
            }

            .card,
            .stat-card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                break-inside: avoid;
            }

            .page-title {
                margin-bottom: 10px;
            }

            table {
                page-break-inside: auto;
                min-width: 100% !important;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>
</head>

<body>

    @auth
    @php
    $hotelMark = strtoupper(substr(config('app.hotel_code', 'HOTEL'), 0, 1)) . 'H';
    @endphp

    <div class="navbar">
        <h2>{{ config('app.name', 'Hotel Management') }}</h2>

        <div class="nav-links">
            <a href="{{ route('dashboard') }}">Home</a>

            @can('module-access',['rooms', 'read'])
            <a href="{{ route('rooms.board') }}">Room Board</a>
            @endcan

            @can('module-access',['reservations', 'read'])
            <a href="{{ route('reservations.index') }}">Reservations</a>
            @endcan
            @can('module-access',['restaurant-orders', 'read'])
            <a href="{{ route('restaurant-orders.index') }}">Restaurant Orders</a>
            @endcan
            <div class="dropdown">
                <button type="button" class="dropdown-toggle">Maintenance ▾</button>

                <div class="dropdown-menu">
                    @can ('module-access',['guests', 'read'])
                    <a href="{{ route('guests.index') }}">Guests</a>
                    @endcan
                    @can ('module-access',['reports', 'read'])
                    <a href="{{ route('reports.index') }}">Reports</a>
                    @endcan
                    @can ('module-access',['rooms', 'read'])
                    <a href="{{ route('rooms.index') }}">Rooms</a>
                    @endcan
                    @can ('module-access',['services', 'read'])
                    <a href="{{ route('services.index') }}">Services</a>
                    @endcan
                    @can ('module-access',['reservations', 'read'])
                    <a href="{{ route('reservation-services.index') }}">Reservation Services</a>
                    @endcan
                    @can ('module-access',['room-types', 'read'])
                    <a href="{{ route('room-types.index') }}">Room Types</a>
                    @endcan
                    @can ('module-access',['staff', 'read'])
                    <a href="{{ route('staff.index') }}">Staff</a>
                    @endcan
                    @can ('module-access',['taxes', 'read'])
                    <a href="{{ route('taxes.index') }}">Taxes</a>
                    @endcan
                    @can ('module-access',['menu-categories', 'read'])
                    <a href="{{ route('menu-categories.index') }}">Menu Categories</a>
                    @endcan
                    @can ('module-access',['menu-items', 'read'])
                    <a href="{{ route('menu-items.index') }}">Menu Items</a>
                    @endcan
                </div>
            </div>

            <div class="hotel-user-wrap">
                <div class="hotel-user-circle">{{ $hotelMark }}</div>

                <div class="hotel-user-menu">
                    <div class="hotel-user-name">{{ auth()->user()->name }}</div>
                    <div class="hotel-user-role">{{ ucfirst(auth()->user()->role->name) }}</div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-btn">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endauth

    <div class="container">
        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="padding-left: 18px;">
                @foreach ($errors->all() as $error)
                <li style="margin-bottom: 6px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @yield('content')
    </div>

</body>

</html>