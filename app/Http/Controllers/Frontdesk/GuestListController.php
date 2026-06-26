<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestListController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Guest::realGuests()
            ->with([
                'folios.bookings.room',
            ])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    if (config('database.default') === 'sqlite') {
                        $q->where('last_name', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhereRaw("first_name || ' ' || last_name like ?", ["%{$search}%"])
                            ->orWhereRaw("last_name || ', ' || first_name like ?", ["%{$search}%"]);
                    } else {
                        $q->where('last_name', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$search}%"])
                            ->orWhereRaw("CONCAT(last_name, ', ', first_name) like ?", ["%{$search}%"]);
                    }
                });
            })
            ->when($status === 'checked_in', function ($query) {
                $query->whereExists(function ($subQuery) {
                    $subQuery->selectRaw(1)
                        ->from('bookings')
                        ->join('folios', 'bookings.folio_id', '=', 'folios.folio_id')
                        ->whereColumn('folios.guest_id', 'guests.guest_id')
                        ->where('bookings.status', 'CHECKED_IN')
                        ->whereNotExists(function ($nested) {
                            $nested->selectRaw(1)
                                ->from('bookings as b2')
                                ->join('folios as f2', 'b2.folio_id', '=', 'f2.folio_id')
                                ->whereColumn('f2.guest_id', 'guests.guest_id')
                                ->whereColumn('b2.booking_id', '>', 'bookings.booking_id');
                        });
                });
            })
            ->when($status === 'checked_out', function ($query) {
                $query->whereExists(function ($subQuery) {
                    $subQuery->selectRaw(1)
                        ->from('bookings')
                        ->join('folios', 'bookings.folio_id', '=', 'folios.folio_id')
                        ->whereColumn('folios.guest_id', 'guests.guest_id')
                        ->where('bookings.status', 'CHECKED_OUT')
                        ->whereNotExists(function ($nested) {
                            $nested->selectRaw(1)
                                ->from('bookings as b2')
                                ->join('folios as f2', 'b2.folio_id', '=', 'f2.folio_id')
                                ->whereColumn('f2.guest_id', 'guests.guest_id')
                                ->whereColumn('b2.booking_id', '>', 'bookings.booking_id');
                        });
                });
            })
            ->orderBy('last_name')
            ->orderBy('first_name');

        $printGuests = (clone $query)->get();

        $guests = $query->paginate(20)->withQueryString();

        return view('frontdesk.guest-list.index', [
            'guests' => $guests,
            'printGuests' => $printGuests,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function searchJson(Request $request): JsonResponse
    {
        $search = $request->input('q');

        if (empty($search)) {
            return response()->json([]);
        }

        $guests = Guest::realGuests()
            ->with(['folios'])
            ->where(function ($q) use ($search) {
                if (config('database.default') === 'sqlite') {
                    $q->where('last_name', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhereRaw("first_name || ' ' || last_name like ?", ["%{$search}%"])
                        ->orWhereRaw("last_name || ', ' || first_name like ?", ["%{$search}%"]);
                } else {
                    $q->where('last_name', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$search}%"])
                        ->orWhereRaw("CONCAT(last_name, ', ', first_name) like ?", ["%{$search}%"]);
                }
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->limit(10)
            ->get();

        return response()->json($guests);
    }
}
