<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('roomType')->latest()->paginate(15);

        return view('rooms.index', compact('rooms'));
    }

    public function create()
    {
        $roomTypes = RoomType::orderBy('name')->get();

        return view('rooms.create', compact('roomTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_type_id' => ['required', 'exists:room_types,id'],
            'room_number' => ['required', 'string', 'max:50', 'unique:rooms,room_number'],
            'floor_number' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:available,occupied,maintenance,cleaning'],
            'notes' => ['nullable', 'string'],
        ]);

        Room::create($validated);

        return redirect()
            ->route('rooms.index')
            ->with('success', 'Room created successfully.');
    }

    public function show(Room $room)
    {
        $room->load(['roomType', 'reservationRooms.reservation']);

        return view('rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        $roomTypes = RoomType::orderBy('name')->get();

        return view('rooms.edit', compact('room', 'roomTypes'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_type_id' => ['required', 'exists:room_types,id'],
            'room_number' => ['required', 'string', 'max:50', 'unique:rooms,room_number,' . $room->id],
            'floor_number' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:available,occupied,maintenance,cleaning'],
            'notes' => ['nullable', 'string'],
        ]);

        $room->update($validated);

        return redirect()
            ->route('rooms.index')
            ->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room)
    {
        $room->delete();

        return redirect()
            ->route('rooms.index')
            ->with('success', 'Room deleted successfully.');
    }

    public function markAvailable(Room $room)
    {
        if (!in_array($room->status, ['cleaning', 'maintenance'])) {
            return back()->withErrors([
                'status' => 'Only cleaning or maintenance rooms can be marked available.',
            ]);
        }

        $room->update([
            'status' => 'available',
        ]);

        return redirect()
            ->route('rooms.show', $room)
            ->with('success', 'Room marked as available successfully.');
    }

    public function markCleaning(Room $room)
    {
        if ($room->status === 'maintenance') {
            return back()->withErrors([
                'status' => 'Room in maintenance cannot be marked as cleaning.',
            ]);
        }

        $room->update([
            'status' => 'cleaning',
        ]);

        return redirect()
            ->route('rooms.show', $room)
            ->with('success', 'Room marked as cleaning successfully.');
    }

    public function markMaintenance(Room $room)
    {
        if ($room->status === 'occupied') {
            return back()->withErrors([
                'status' => 'Occupied room cannot be moved to maintenance.',
            ]);
        }

        $room->update([
            'status' => 'maintenance',
        ]);

        return redirect()
            ->route('rooms.show', $room)
            ->with('success', 'Room marked as maintenance successfully.');
    }
    public function board()
    {
        $roomsByFloor = Room::with([
            'roomType',
            'reservationRooms.reservation.guest',
        ])
            ->orderBy('floor_number')
            ->orderBy('room_number')
            ->get()
            ->groupBy('floor_number');

        return view('rooms.board', compact('roomsByFloor'));
    }
}
