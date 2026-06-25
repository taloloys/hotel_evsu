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

        $guests = Guest::query()
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
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(20)
            ->withQueryString();

        return view('frontdesk.guest-list.index', [
            'guests' => $guests,
            'search' => $search,
        ]);
    }

    public function searchJson(Request $request): JsonResponse
    {
        $search = $request->input('q');

        if (empty($search)) {
            return response()->json([]);
        }

        $guests = Guest::query()
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
