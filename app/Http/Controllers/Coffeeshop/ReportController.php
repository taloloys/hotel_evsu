<?php

namespace App\Http\Controllers\Coffeeshop;

use App\Http\Controllers\Controller;
use App\Models\PosOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $dateFrom = $request->input('date_from', Carbon::today()->subDays(7)->toDateString());
        $dateTo = $request->input('date_to', Carbon::today()->toDateString());
        $paymentMethod = $request->input('payment_method', 'all');

        $query = PosOrder::with('items')
            ->where('status', 'closed')
            ->whereDate('closed_at', '>=', $dateFrom)
            ->whereDate('closed_at', '<=', $dateTo)
            ->orderByDesc('closed_at');

        if ($paymentMethod !== 'all') {
            $query->where('payment_method', $paymentMethod);
        }

        $orders = $query->get();

        $summary = [
            'total_sales' => (float) $orders->sum('total'),
            'order_count' => $orders->count(),
            'cash_total' => (float) $orders->where('payment_method', 'cash')->sum('total'),
            'gcash_total' => (float) $orders->where('payment_method', 'gcash')->sum('total'),
            'card_total' => (float) $orders->where('payment_method', 'card')->sum('total'),
            'room_total' => (float) $orders->where('payment_method', 'room_charge')->sum('total'),
        ];

        return view('coffeeshop.reports.index', compact('orders', 'summary', 'dateFrom', 'dateTo', 'paymentMethod'));
    }

    public function export(Request $request): StreamedResponse
    {
        $dateFrom = $request->input('date_from', Carbon::today()->subDays(7)->toDateString());
        $dateTo = $request->input('date_to', Carbon::today()->toDateString());

        $orders = PosOrder::where('status', 'closed')
            ->whereDate('closed_at', '>=', $dateFrom)
            ->whereDate('closed_at', '<=', $dateTo)
            ->orderByDesc('closed_at')
            ->get();

        $filename = 'pos-report-'.$dateFrom.'-to-'.$dateTo.'.csv';

        return response()->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Order Number', 'Customer', 'Room', 'Payment Method', 'Total', 'Closed At']);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->order_number,
                    $order->customer_name,
                    $order->room_number,
                    $order->payment_method,
                    $order->total,
                    optional($order->closed_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
