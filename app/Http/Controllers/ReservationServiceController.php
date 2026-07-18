<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ReservationService;
use App\Models\Service;
use App\Models\Staff;
use Illuminate\Http\Request;

class ReservationServiceController extends Controller
{
    public function index()
    {
        $reservationServices = ReservationService::with([
            'reservation.guest',
            'room',
            'service',
            'assignedStaff',
        ])->latest()->paginate(15);

        return view('reservation-services.index', compact('reservationServices'));
    }

    public function create(Request $request)
    {
        $reservation = null;
        $rooms = collect();

        if ($request->filled('reservation_id')) {
            $reservation = Reservation::with(['guest', 'reservationRooms.room'])->findOrFail($request->reservation_id);
            $rooms = $reservation->reservationRooms->map->room;
        }

        $reservations = Reservation::with('guest')->orderByDesc('id')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $staff = Staff::where('status', 'active')->orderBy('first_name')->get();

        return view('reservation-services.create', compact(
            'reservation',
            'reservations',
            'rooms',
            'services',
            'staff'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reservation_id' => ['required', 'exists:reservations,id'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'service_id' => ['required', 'exists:services,id'],
            'assigned_staff_id' => ['nullable', 'exists:staff,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'service_date' => ['nullable', 'date'],
            'status' => ['required', 'in:pending,in_progress,completed,cancelled'],
            'notes' => ['nullable', 'string'],
        ]);

        $reservation = Reservation::with('reservationRooms')->findOrFail($validated['reservation_id']);
        $service = Service::findOrFail($validated['service_id']);

        if (!empty($validated['room_id'])) {
            $roomBelongsToReservation = $reservation->reservationRooms
                ->pluck('room_id')
                ->contains((int) $validated['room_id']);

            if (!$roomBelongsToReservation) {
                return back()
                    ->withErrors(['room_id' => 'Selected room does not belong to this reservation.'])
                    ->withInput();
            }
        }

        $totalPrice = $service->price * $validated['quantity'];

        ReservationService::create([
            'reservation_id' => $validated['reservation_id'],
            'room_id' => $validated['room_id'] ?? null,
            'service_id' => $validated['service_id'],
            'assigned_staff_id' => $validated['assigned_staff_id'] ?? null,
            'quantity' => $validated['quantity'],
            'service_date' => $validated['service_date'] ?? now(),
            'status' => $validated['status'],
            'total_price' => $totalPrice,
            'notes' => $validated['notes'] ?? null,
        ]);

        $this->updateReservationTotalAmount($reservation);

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('success', 'Service added successfully.');
    }

    public function show(string $id)
    {
        $reservationService = ReservationService::with([
            'reservation.guest',
            'room',
            'service',
            'assignedStaff',
        ])->findOrFail($id);

        return view('reservation-services.show', compact('reservationService'));
    }

    public function edit(string $id)
    {
        $reservationService = ReservationService::with([
            'reservation.guest',
            'reservation.reservationRooms.room',
            'service',
            'assignedStaff',
        ])->findOrFail($id);

        $reservation = $reservationService->reservation;
        $rooms = $reservation->reservationRooms->map->room;
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $staff = Staff::where('status', 'active')->orderBy('first_name')->get();

        return view('reservation-services.edit', compact(
            'reservationService',
            'reservation',
            'rooms',
            'services',
            'staff'
        ));
    }

    public function update(Request $request, string $id)
    {
        $reservationService = ReservationService::with('reservation.reservationRooms')->findOrFail($id);

        $validated = $request->validate([
            'room_id' => ['nullable', 'exists:rooms,id'],
            'service_id' => ['required', 'exists:services,id'],
            'assigned_staff_id' => ['nullable', 'exists:staff,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'service_date' => ['nullable', 'date'],
            'status' => ['required', 'in:pending,in_progress,completed,cancelled'],
            'notes' => ['nullable', 'string'],
        ]);

        if (!empty($validated['room_id'])) {
            $roomBelongsToReservation = $reservationService->reservation->reservationRooms
                ->pluck('room_id')
                ->contains((int) $validated['room_id']);

            if (!$roomBelongsToReservation) {
                return back()
                    ->withErrors(['room_id' => 'Selected room does not belong to this reservation.'])
                    ->withInput();
            }
        }

        $service = Service::findOrFail($validated['service_id']);
        $totalPrice = $service->price * $validated['quantity'];

        $reservationService->update([
            'room_id' => $validated['room_id'] ?? null,
            'service_id' => $validated['service_id'],
            'assigned_staff_id' => $validated['assigned_staff_id'] ?? null,
            'quantity' => $validated['quantity'],
            'service_date' => $validated['service_date'] ?? now(),
            'status' => $validated['status'],
            'total_price' => $totalPrice,
            'notes' => $validated['notes'] ?? null,
        ]);

        $this->updateReservationTotalAmount($reservationService->reservation);

        return redirect()
            ->route('reservations.show', $reservationService->reservation)
            ->with('success', 'Service updated successfully.');
    }

    public function destroy(string $id)
    {
        $reservationService = ReservationService::with('reservation')->findOrFail($id);
        $reservation = $reservationService->reservation;

        $reservationService->delete();

        $this->updateReservationTotalAmount($reservation);

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('success', 'Service deleted successfully.');
    }

    private function updateReservationTotalAmount(Reservation $reservation): void
    {
        $roomTotal = $reservation->reservationRooms()->sum('subtotal');
        $serviceTotal = $reservation->reservationServices()->sum('total_price');

        $reservation->update([
            'total_amount' => $roomTotal + $serviceTotal,
        ]);
    }
}
