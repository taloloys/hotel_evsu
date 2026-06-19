<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoomController extends Controller
{
    /**
     * Display a listing of the rooms.
     */
    public function index(Request $request): View
    {
        $query = Room::query();

        // Search by room number
        if ($request->filled('search')) {
            $query->where('room_number', 'like', "%{$request->search}%");
        }

        // Filter by room type
        if ($request->filled('room_type') && $request->room_type !== 'all') {
            $query->where('room_type', $request->room_type);
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by active/disabled status
        if ($request->filled('is_active') && $request->is_active !== 'all') {
            $query->where('is_active', $request->is_active === 'active');
        }

        $rooms = $query->orderBy('room_number')->get();

        // Statistics (based on all rooms)
        $allRooms = Room::all();
        $totalCount = $allRooms->count();
        $occupiedCount = $allRooms->where('status', 'OCCUPIED')->count();
        $availableCount = $allRooms->where('status', 'AVAILABLE')->count();
        $maintenanceCount = $allRooms->where('status', 'MAINTENANCE')->count();
        $inactiveCount = $allRooms->where('is_active', false)->count();

        // Get unique room types for filters/modal suggestions
        $roomTypes = Room::query()
            ->select('room_type')
            ->distinct()
            ->orderBy('room_type')
            ->pluck('room_type');

        return view('admin.rooms.index', [
            'rooms' => $rooms,
            'roomTypes' => $roomTypes,
            'totalCount' => $totalCount,
            'occupiedCount' => $occupiedCount,
            'availableCount' => $availableCount,
            'maintenanceCount' => $maintenanceCount,
            'inactiveCount' => $inactiveCount,
            'filters' => [
                'search' => $request->search,
                'room_type' => $request->room_type ?? 'all',
                'status' => $request->status ?? 'all',
                'is_active' => $request->is_active ?? 'all',
            ],
        ]);
    }

    /**
     * Store a newly created room in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'room_number' => ['required', 'string', 'max:10', 'unique:rooms,room_number'],
            'room_type' => ['required', 'string', 'max:50'],
            'base_rate' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:AVAILABLE,OCCUPIED,RESERVED,CLEANING,MAINTENANCE'],
        ]);

        Room::create([
            'room_number' => $validated['room_number'],
            'room_type' => $validated['room_type'],
            'base_rate' => $validated['base_rate'],
            'status' => $validated['status'],
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.rooms')
            ->with('success', 'Room created successfully.');
    }

    /**
     * Update the specified room in storage.
     */
    public function update(Request $request, Room $room): RedirectResponse
    {
        $validated = $request->validate([
            'room_number' => ['required', 'string', 'max:10', Rule::unique('rooms', 'room_number')->ignore($room->room_id, 'room_id')],
            'room_type' => ['required', 'string', 'max:50'],
            'base_rate' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:AVAILABLE,OCCUPIED,RESERVED,CLEANING,MAINTENANCE'],
        ]);

        // If status changes to occupied or reserved, ensure room is active
        if (in_array($validated['status'], ['OCCUPIED', 'RESERVED'], true) && ! $room->is_active) {
            return redirect()
                ->route('admin.rooms')
                ->withErrors(['cannot_activate_status' => 'Cannot set an inactive room to occupied or reserved.']);
        }

        $room->update($validated);

        return redirect()
            ->route('admin.rooms')
            ->with('success', 'Room updated successfully.');
    }

    /**
     * Toggle the active status of a room.
     */
    public function toggleStatus(Room $room): RedirectResponse
    {
        if ($room->is_active) {
            // Check if room is occupied or reserved
            if (in_array($room->status, ['OCCUPIED', 'RESERVED'], true)) {
                return redirect()
                    ->route('admin.rooms')
                    ->withErrors(['cannot_disable_occupied' => 'You cannot disable a room that is currently occupied or reserved.']);
            }
        }

        $room->update([
            'is_active' => ! $room->is_active,
        ]);

        $statusMessage = $room->is_active ? 'enabled' : 'disabled';

        return redirect()
            ->route('admin.rooms')
            ->with('success', "Room has been successfully {$statusMessage}.");
    }
}
