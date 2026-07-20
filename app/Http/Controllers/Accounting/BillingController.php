<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Folio;
use App\Models\PosOrder;
use App\Models\PosSetting;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('manage-accounting-billing');

        $tab = $request->input('tab', 'front_desk');
        $statusFilter = $request->input('status', 'ALL');
        $search = $request->input('search');

        $dateRange = $request->input('date_range', 'all'); // 'specific', 'today', 'weekly', 'monthly', 'yearly', 'all'
        $date = $request->input('date', Carbon::today()->toDateString());

        $startDate = null;
        $endDate = Carbon::now()->endOfDay();

        if ($dateRange === 'today') {
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($dateRange === 'weekly') {
            $startDate = Carbon::today()->startOfWeek();
        } elseif ($dateRange === 'monthly') {
            $startDate = Carbon::today()->startOfMonth();
        } elseif ($dateRange === 'yearly') {
            $startDate = Carbon::today()->startOfYear();
        } elseif ($dateRange === 'specific' && $date) {
            $startDate = Carbon::parse($date)->startOfDay();
            $endDate = Carbon::parse($date)->endOfDay();
        }

        if ($tab === 'pos') {
            $query = PosOrder::with(['folio.guest', 'user']);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('room_number', 'like', "%{$search}%");
                });
            }

            if ($startDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            if ($statusFilter !== 'ALL') {
                $query->where('status', strtolower($statusFilter));
            }

            $posOrders = $query->orderBy('order_id', 'desc')->paginate(10)->withQueryString();

            $txQuery = Transaction::where('department', 'COFFEE_SHOP');
            if ($startDate) {
                $txQuery->whereBetween('timestamp', [$startDate, $endDate]);
            }

            $cashSales = (float) (clone $txQuery)->whereIn('payment_method', ['CASH', 'cash'])->sum('credit_amount');
            $creditSales = (float) (clone $txQuery)->whereIn('payment_method', ['CREDIT_CARD', 'card', 'GCASH', 'gcash'])->sum('credit_amount');
            $totalSales = (float) PosOrder::where('status', 'closed')->sum('total');

            $unpaidBalance = PosOrder::whereIn('status', ['open', 'active'])->sum('total');

            return view('accounting.billing.index', [
                'tab' => $tab,
                'posOrders' => $posOrders,
                'totalSales' => $totalSales,
                'cashSales' => $cashSales,
                'creditSales' => $creditSales,
                'unpaidBalance' => $unpaidBalance,
                'statusFilter' => $statusFilter,
                'search' => $search,
                'dateRange' => $dateRange,
                'date' => $date,
            ]);
        }

        $query = Folio::withBalances()->with(['guest', 'bookings.room']);

        $walkInFolioId = PosSetting::walkInFolioId();
        if ($walkInFolioId) {
            $query->where('folios.folio_id', '!=', $walkInFolioId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('folio_number', 'like', "%{$search}%")
                    ->orWhereHas('guest', function ($gQ) use ($search) {
                        $gQ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($startDate) {
            $query->where(function ($q) use ($startDate, $endDate) {
                $q->whereHas('transactions', function ($tQ) use ($startDate, $endDate) {
                    $tQ->whereBetween('timestamp', [$startDate, $endDate]);
                })->orWhereHas('bookings', function ($bQ) use ($startDate, $endDate) {
                    $bQ->whereDate('arrival_date', '<=', $endDate->toDateString())
                        ->whereDate('departure_date', '>=', $startDate->toDateString());
                });
            });
        }

        if ($statusFilter !== 'ALL') {
            if ($statusFilter === 'CLOSED') {
                $query->where('status', 'CLOSED');
            } elseif ($statusFilter === 'OPEN') {
                $query->where('status', 'OPEN');
            }
        }

        $foliosPaginator = $query->orderBy('folios.folio_id', 'desc')->paginate(10)->withQueryString();

        $folios = $foliosPaginator->getCollection()->map(function ($folio) {
            $totalCharges = $folio->total_charges;
            $totalCredits = $folio->total_credits;
            $balance = $folio->balance;

            if ($folio->status === 'CLOSED') {
                $displayStatus = 'Paid';
            } elseif ($balance <= 0) {
                $displayStatus = 'Paid';
            } else {
                $displayStatus = 'Unpaid';
            }

            return (object) [
                'folio_id' => $folio->folio_id,
                'folio_number' => $folio->folio_number,
                'guest_name' => $folio->guest ? trim($folio->guest->first_name.' '.$folio->guest->last_name) : 'No Guest',
                'room_number' => $folio->bookings->first() && $folio->bookings->first()->room ? $folio->bookings->first()->room->room_number : 'N/A',
                'date' => $folio->bookings->first() ? $folio->bookings->first()->arrival_date->toDateString() : 'N/A',
                'display_status' => $displayStatus,
                'total_amount' => $totalCharges,
                'balance' => $balance,
                'status' => $folio->status,
            ];
        });

        $foliosPaginator->setCollection($folios);

        if ($statusFilter === 'PAID') {
            $filtered = $folios->filter(fn ($f) => $f->display_status === 'Paid');
            $foliosPaginator->setCollection($filtered);
        } elseif ($statusFilter === 'UNPAID') {
            $filtered = $folios->filter(fn ($f) => $f->display_status === 'Unpaid');
            $foliosPaginator->setCollection($filtered);
        }

        $txQuery = Transaction::where('department', 'FRONT_DESK');
        if ($startDate) {
            $txQuery->whereBetween('timestamp', [$startDate, $endDate]);
        }

        $cashSales = (float) (clone $txQuery)->whereIn('payment_method', ['CASH', 'cash'])->sum('credit_amount');
        $creditSales = (float) (clone $txQuery)->whereIn('payment_method', ['CREDIT_CARD', 'card', 'GCASH', 'gcash'])->sum('credit_amount');
        $totalCharges = (float) (clone $txQuery)->sum('charge_amount');
        $totalCredits = (float) (clone $txQuery)->sum('credit_amount');
        $totalSales = max($totalCharges, $totalCredits, $cashSales + $creditSales);

        // Exclude walk-in folio balance if any (should be 0 anyway, but to be strict)
        $unpaidBalanceQuery = Folio::where('status', 'OPEN');
        if ($walkInFolioId) {
            $unpaidBalanceQuery->where('folio_id', '!=', $walkInFolioId);
        }
        $unpaidBalance = $unpaidBalanceQuery->withBalances()->get()->sum('balance');

        return view('accounting.billing.index', [
            'tab' => $tab,
            'folios' => $foliosPaginator,
            'totalSales' => $totalSales,
            'cashSales' => $cashSales,
            'creditSales' => $creditSales,
            'unpaidBalance' => $unpaidBalance,
            'statusFilter' => $statusFilter,
            'search' => $search,
            'dateRange' => $dateRange,
            'date' => $date,
        ]);
    }

    public function show(Folio $folio): View
    {
        Gate::authorize('manage-accounting-billing');

        $folio->load(['guest', 'bookings.room', 'transactions.chargeCode', 'transactions.user']);

        return view('accounting.billing.show', [
            'folio' => $folio,
            'totalCharges' => $folio->total_charges,
            'totalCredits' => $folio->total_credits,
            'balance' => $folio->balance,
        ]);
    }
}
