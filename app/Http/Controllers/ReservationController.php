<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['guest', 'reservationRooms.room']);

        if ($request->filled('reservation_code')) {
            $query->where('reservation_code', 'like', '%' . $request->reservation_code . '%');
        }

        if ($request->filled('guest_name')) {
            $guestName = $request->guest_name;

            $query->whereHas('guest', function ($q) use ($guestName) {
                $q->where('first_name', 'like', '%' . $guestName . '%')
                    ->orWhere('last_name', 'like', '%' . $guestName . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('check_in_date')) {
            $query->whereDate('check_in_date', $request->check_in_date);
        }

        if ($request->filled('check_out_date')) {
            $query->whereDate('check_out_date', $request->check_out_date);
        }

        $reservations = $query->latest()->paginate(15)->withQueryString();

        return view('reservations.index', compact('reservations'));
    }

    public function create(Request $request)
    {
        $guests = Guest::orderBy('first_name')->get();
        $availableRooms = collect();
        $selectedRoomId = $request->room_id;

        if ($request->filled('check_in_date') && $request->filled('check_out_date')) {
            $availableRooms = $this->getAvailableRooms(
                $request->check_in_date,
                $request->check_out_date
            );
        } elseif ($selectedRoomId) {
            $availableRooms = Room::with('roomType')
                ->where('id', $selectedRoomId)
                ->get();
        }

        return view('reservations.create', compact('guests', 'availableRooms', 'selectedRoomId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'guest_mode' => ['required', 'in:existing,new'],
            'guest_id' => ['nullable', 'exists:guests,id'],

            'guest.first_name' => ['nullable', 'string', 'max:255'],
            'guest.last_name' => ['nullable', 'string', 'max:255'],
            'guest.phone' => ['nullable', 'string', 'max:50'],
            'guest.email' => ['nullable', 'email', 'max:255'],
            'guest.id_type' => ['nullable', 'string', 'max:100'],
            'guest.id_number' => ['nullable', 'string', 'max:100'],
            'guest.nationality' => ['nullable', 'string', 'max:100'],
            'guest.address' => ['nullable', 'string', 'max:255'],

            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'adults' => ['required', 'integer', 'min:1'],
            'children' => ['nullable', 'integer', 'min:0'],
            'booking_source' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'rooms' => ['required', 'array', 'min:1'],
            'rooms.*' => ['required', 'exists:rooms,id'],
        ]);

        if ($validated['guest_mode'] === 'existing' && empty($validated['guest_id'])) {
            return back()
                ->withErrors(['guest_id' => 'Please select an existing guest.'])
                ->withInput();
        }

        if ($validated['guest_mode'] === 'new') {
            if (empty($validated['guest']['first_name']) || empty($validated['guest']['last_name'])) {
                return back()
                    ->withErrors(['guest.first_name' => 'First name and last name are required for a new guest.'])
                    ->withInput();
            }
        }

        $availableRoomIds = $this->getAvailableRooms(
            $validated['check_in_date'],
            $validated['check_out_date']
        )->pluck('id')->toArray();

        foreach ($validated['rooms'] as $roomId) {
            if (!in_array($roomId, $availableRoomIds)) {
                return back()
                    ->withErrors(['rooms' => 'One or more selected rooms are not available for those dates.'])
                    ->withInput();
            }
        }

        $nights = Carbon::parse($validated['check_in_date'])
            ->diffInDays(Carbon::parse($validated['check_out_date']));

        if ($nights < 1) {
            $nights = 1;
        }

        DB::transaction(function () use ($validated, $nights) {
            if ($validated['guest_mode'] === 'existing') {
                $guest = Guest::findOrFail($validated['guest_id']);
            } else {
                $guest = Guest::create([
                    'first_name' => $validated['guest']['first_name'],
                    'last_name' => $validated['guest']['last_name'],
                    'phone' => $validated['guest']['phone'] ?? null,
                    'email' => $validated['guest']['email'] ?? null,
                    'id_type' => $validated['guest']['id_type'] ?? null,
                    'id_number' => $validated['guest']['id_number'] ?? null,
                    'nationality' => $validated['guest']['nationality'] ?? null,
                    'address' => $validated['guest']['address'] ?? null,
                ]);
            }

            $reservation = Reservation::create([
                'guest_id' => $guest->id,
                'reservation_code' => $this->generateReservationCode(),
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'adults' => $validated['adults'],
                'children' => $validated['children'] ?? 0,
                'status' => 'confirmed',
                'total_amount' => 0,
                'paid_amount' => 0,
                'booking_source' => $validated['booking_source'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['rooms'] as $roomId) {
                $room = Room::with('roomType')->findOrFail($roomId);

                $roomRate = $room->roomType->base_price;
                $subtotal = $roomRate * $nights;

                $reservation->reservationRooms()->create([
                    'room_id' => $room->id,
                    'room_rate' => $roomRate,
                    'nights' => $nights,
                    'subtotal' => $subtotal,
                ]);
            }

            $reservation->update([
                'total_amount' => $this->calculateReservationTotal($reservation),
            ]);
        });

        return redirect()
            ->route('reservations.index')
            ->with('success', 'Reservation created successfully.');
    }

    public function show(Reservation $reservation)
    {
        $reservation->load([
            'guest',
            'reservationRooms.room.roomType',
            'payments',
            'reservationServices.service',
        ]);

        return view('reservations.show', compact('reservation'));
    }

    public function invoice(Reservation $reservation)
    {
        $reservation->load([
            'guest',
            'reservationRooms.room.roomType',
            'reservationServices.service',
            'payments',
        ]);

        return view('reservations.invoice', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        $guests = Guest::orderBy('first_name')->get();

        $availableRooms = $this->getAvailableRoomsForUpdate(
            $reservation->check_in_date->format('Y-m-d'),
            $reservation->check_out_date->format('Y-m-d'),
            $reservation->id
        );

        return view('reservations.edit', compact('reservation', 'guests', 'availableRooms'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'guest_id' => ['required', 'exists:guests,id'],
            'check_in_date' => ['required', 'date'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'adults' => ['required', 'integer', 'min:1'],
            'children' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:pending,confirmed,cancelled,no_show'],
            'booking_source' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'rooms' => ['required', 'array', 'min:1'],
            'rooms.*' => ['required', 'exists:rooms,id'],
        ]);

        $availableRoomIds = $this->getAvailableRoomsForUpdate(
            $validated['check_in_date'],
            $validated['check_out_date'],
            $reservation->id
        )->pluck('id')->toArray();

        foreach ($validated['rooms'] as $roomId) {
            if (!in_array($roomId, $availableRoomIds)) {
                return back()
                    ->withErrors(['rooms' => 'One or more selected rooms are not available for those dates.'])
                    ->withInput();
            }
        }

        $nights = Carbon::parse($validated['check_in_date'])
            ->diffInDays(Carbon::parse($validated['check_out_date']));

        if ($nights < 1) {
            $nights = 1;
        }

        DB::transaction(function () use ($validated, $reservation, $nights) {
            $reservation->update([
                'guest_id' => $validated['guest_id'],
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'adults' => $validated['adults'],
                'children' => $validated['children'] ?? 0,
                'status' => $validated['status'],
                'booking_source' => $validated['booking_source'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            $reservation->reservationRooms()->delete();

            foreach ($validated['rooms'] as $roomId) {
                $room = Room::with('roomType')->findOrFail($roomId);

                $roomRate = $room->roomType->base_price;
                $subtotal = $roomRate * $nights;

                $reservation->reservationRooms()->create([
                    'room_id' => $room->id,
                    'room_rate' => $roomRate,
                    'nights' => $nights,
                    'subtotal' => $subtotal,
                ]);
            }

            $reservation->update([
                'total_amount' => $this->calculateReservationTotal($reservation),
            ]);
        });

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('success', 'Reservation updated successfully.');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return redirect()
            ->route('reservations.index')
            ->with('success', 'Reservation deleted successfully.');
    }

    public function checkIn(Reservation $reservation)
    {
        if ($reservation->status !== 'confirmed') {
            return back()->withErrors([
                'status' => 'Only confirmed reservations can be checked in.',
            ]);
        }

        if (now()->toDateString() < $reservation->check_in_date->toDateString()) {
            return back()->withErrors([
                'status' => 'Guest arrived before the reserved date. Update the reservation check-in date first, then check in.',
            ]);
        }

        DB::transaction(function () use ($reservation) {
            $reservation->load('reservationRooms.room');

            $reservation->update([
                'status' => 'checked_in',
                'checked_in_at' => now(),
            ]);

            foreach ($reservation->reservationRooms as $reservationRoom) {
                $reservationRoom->room->update([
                    'status' => 'occupied',
                ]);
            }
        });

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('success', 'Guest checked in successfully.');
    }

    public function checkOut(Reservation $reservation)
    {
        if ($reservation->status !== 'checked_in') {
            return back()->withErrors([
                'status' => 'Only checked-in reservations can be checked out.',
            ]);
        }

        DB::transaction(function () use ($reservation) {
            $reservation->load([
                'reservationRooms.room.roomType',
                'reservationServices',
            ]);

            $actualCheckOutDate = now()->toDateString();
            $roomIds = $reservation->reservationRooms->pluck('room_id')->toArray();

            if ($actualCheckOutDate !== $reservation->check_out_date->toDateString()) {
                $reservation->update([
                    'check_out_date' => $actualCheckOutDate,
                ]);

                $nights = Carbon::parse($reservation->check_in_date)
                    ->diffInDays(Carbon::parse($reservation->check_out_date));

                if ($nights < 1) {
                    $nights = 1;
                }

                $reservation->reservationRooms()->delete();

                foreach ($roomIds as $roomId) {
                    $room = Room::with('roomType')->findOrFail($roomId);

                    $roomRate = $room->roomType->base_price;
                    $subtotal = $roomRate * $nights;

                    $reservation->reservationRooms()->create([
                        'room_id' => $room->id,
                        'room_rate' => $roomRate,
                        'nights' => $nights,
                        'subtotal' => $subtotal,
                    ]);
                }

                $reservation->update([
                    'total_amount' => $this->calculateReservationTotal($reservation),
                ]);
            }

            $reservation->load('reservationRooms.room');

            $reservation->update([
                'status' => 'checked_out',
                'checked_out_at' => now(),
            ]);

            foreach ($reservation->reservationRooms as $reservationRoom) {
                $reservationRoom->room->update([
                    'status' => 'cleaning',
                ]);
            }
        });

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('success', 'Guest checked out successfully.');
    }

    private function getAvailableRooms(string $checkInDate, string $checkOutDate)
    {
        return Room::with('roomType')
            ->whereNotIn('status', ['maintenance'])
            ->whereNotIn('id', function ($query) use ($checkInDate, $checkOutDate) {
                $query->select('reservation_rooms.room_id')
                    ->from('reservation_rooms')
                    ->join('reservations', 'reservations.id', '=', 'reservation_rooms.reservation_id')
                    ->whereIn('reservations.status', ['pending', 'confirmed', 'checked_in'])
                    ->where('reservations.check_in_date', '<', $checkOutDate)
                    ->where('reservations.check_out_date', '>', $checkInDate);
            })
            ->orderBy('room_number')
            ->get();
    }

    private function getAvailableRoomsForUpdate(string $checkInDate, string $checkOutDate, int $reservationId)
    {
        return Room::with('roomType')
            ->whereNotIn('status', ['maintenance'])
            ->whereNotIn('id', function ($query) use ($checkInDate, $checkOutDate, $reservationId) {
                $query->select('reservation_rooms.room_id')
                    ->from('reservation_rooms')
                    ->join('reservations', 'reservations.id', '=', 'reservation_rooms.reservation_id')
                    ->where('reservations.id', '!=', $reservationId)
                    ->whereIn('reservations.status', ['pending', 'confirmed', 'checked_in'])
                    ->where('reservations.check_in_date', '<', $checkOutDate)
                    ->where('reservations.check_out_date', '>', $checkInDate);
            })
            ->orderBy('room_number')
            ->get();
    }

    private function calculateReservationTotal(Reservation $reservation): float
    {
        $roomTotal = $reservation->reservationRooms()->sum('subtotal');
        $serviceTotal = $reservation->reservationServices()->sum('total_price');

        return (float) ($roomTotal + $serviceTotal);
    }

    public function guestSearch(Request $request)
    {
        $query = trim($request->get('q', ''));

        if ($query === '' || strlen($query) < 2) {
            return response()->json([]);
        }

        $guests = Guest::query()
            ->where('first_name', 'like', "%{$query}%")
            ->orWhere('last_name', 'like', "%{$query}%")
            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$query}%"])
            ->orWhere('phone', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(8)
            ->get(['id', 'first_name', 'last_name', 'phone', 'email']);

        return response()->json($guests);
    }

    protected function generateReservationCode(): string
    {
        $hotelCode = strtoupper(config('app.hotel_code', 'HOTEL'));
        $datePart = now()->format('Ymd');

        $todayCount = \App\Models\Reservation::whereDate('created_at', now()->toDateString())->count() + 1;

        $sequence = str_pad($todayCount, 4, '0', STR_PAD_LEFT);

        return "{$hotelCode}-{$datePart}-{$sequence}";
    }
}
