<?php

namespace App\Http\Controllers\Accounting;

// Use App\Models\ActivityLog instead of DB for Eloquent benefits
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $userIdFilter = $request->input('user_id');
        $actionFilter = $request->input('action_type');

        $query = ActivityLog::with('user');

        if ($search) {
            $query->where('description', 'like', "%{$search}%");
        }

        if ($userIdFilter && $userIdFilter !== 'ALL') {
            $query->where('user_id', $userIdFilter);
        }

        if ($actionFilter && $actionFilter !== 'ALL') {
            $query->where('action_type', $actionFilter);
        }

        $logs = $query->orderBy('timestamp', 'desc')->paginate(30);

        // Fetch options for filters
        $users = User::all();

        $actionTypes = [
            'LOGIN',
            'RESERVATION_CREATE',
            'CHECK_IN',
            'ADD_CHARGE',
            'PRINT_FOLIO',
            'CLOSE_SHIFT',
            'ROOM_MODIFIED',
        ];

        return view('accounting.audit.index', [
            'logs' => $logs,
            'users' => $users,
            'actionTypes' => $actionTypes,
            'search' => $search,
            'userIdFilter' => $userIdFilter,
            'actionFilter' => $actionFilter,
        ]);
    }
}
