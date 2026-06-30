<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of system activity logs with filtering and stats.
     */
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user');

        // Apply filters
        $this->applyFilters($query, $request);

        // Paginate logs
        $logs = $query->orderByDesc('timestamp')
            ->orderByDesc('log_id')
            ->paginate(25)
            ->withQueryString();

        // Get filter options
        $users = User::orderBy('full_name')->get(['user_id', 'full_name', 'username']);
        $actionTypes = ActivityLog::select('action_type')->distinct()->pluck('action_type')->toArray();

        // Get overall statistics (unfiltered for consistent overview, or filter-aware if preferred; standard is overall)
        $totalLogs = ActivityLog::count();
        $todayLogs = ActivityLog::whereDate('timestamp', Carbon::today())->count();
        $auditLogsCount = ActivityLog::whereNotIn('action_type', ['LOGIN', 'VIEW'])->count();

        return view('admin.activitylogs.index', [
            'logs' => $logs,
            'users' => $users,
            'actionTypes' => $actionTypes,
            'totalCount' => $totalLogs,
            'todayCount' => $todayLogs,
            'auditCount' => $auditLogsCount,
            'filters' => [
                'search' => $request->query('search'),
                'user_id' => $request->query('user_id'),
                'action_type' => $request->query('action_type'),
                'date_from' => $request->query('date_from'),
                'date_to' => $request->query('date_to'),
            ],
        ]);
    }

    /**
     * Export the filtered activity logs to CSV format.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = ActivityLog::with('user');

        // Apply same filters
        $this->applyFilters($query, $request);

        $logs = $query->orderByDesc('timestamp')
            ->orderByDesc('log_id')
            ->get();

        $fileName = 'activity_logs_'.date('Ymd_His').'.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');

            // Add BOM for Excel compatibility with UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['Log ID', 'User', 'Username', 'Role', 'Action Type', 'Description', 'Timestamp']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->log_id,
                    $log->user?->full_name ?? 'System',
                    $log->user?->username ?? 'N/A',
                    $log->user?->role?->label ?? 'N/A',
                    $log->action_type,
                    $log->description,
                    $log->timestamp ? $log->timestamp->format('Y-m-d H:i:s') : 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Apply request filters to the activity log query.
     */
    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('username', 'like', "%{$search}%")
                            ->orWhere('full_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('user_id') && $request->query('user_id') !== 'all') {
            $query->where('user_id', $request->query('user_id'));
        }

        if ($request->filled('action_type') && $request->query('action_type') !== 'all') {
            $query->where('action_type', $request->query('action_type'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('timestamp', '>=', $request->query('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('timestamp', '<=', $request->query('date_to'));
        }
    }
}
