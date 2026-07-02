<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Expense;
use App\Models\PosApprovalRequest;
use App\Models\PosProduct;
use App\Models\Room;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;

class LayoutDataController extends Controller
{
    /**
     * Retrieve notifications and stock alert count for layout.
     * Caches the queries per user for 30 seconds to optimize performance.
     */
    public function getLayoutData(): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json([
                'notifications' => [],
                'lowStockCount' => 0,
            ]);
        }

        $userId = $user->user_id;

        $notifications = [];
        $today = Carbon::today();

        // 1. Manage Reservations notifications
        if ($user->hasPermission('manage-reservations')) {
            $pendingCheckins = Booking::where('status', 'RESERVED')
                ->whereDate('arrival_date', '<=', $today)
                ->with(['folio.guest', 'room'])
                ->get();

            foreach ($pendingCheckins as $booking) {
                $guestName = $booking->folio && $booking->folio->guest
                    ? ($booking->folio->guest->first_name.' '.$booking->folio->guest->last_name)
                    : 'Unknown Guest';
                $roomNumber = $booking->room ? $booking->room->room_number : 'Unassigned';
                $notifications[] = [
                    'id' => 'booking-checkin-'.$booking->booking_id,
                    'type' => 'reservation',
                    'icon' => 'fa-calendar-check text-success',
                    'message' => "Guest {$guestName} is pending check-in (Room {$roomNumber}).",
                    'link' => route('frontdesk.checkin'),
                    'time' => $booking->arrival_date->format('M d, Y'),
                ];
            }

            // Dirty Rooms
            $dirtyRooms = Room::where('status', 'CLEANING')
                ->where('is_active', true)
                ->get();

            foreach ($dirtyRooms as $room) {
                $notifications[] = [
                    'id' => 'room-dirty-'.$room->room_id,
                    'type' => 'housekeeping',
                    'icon' => 'fa-broom text-warning',
                    'message' => "Room {$room->room_number} requires cleaning.",
                    'link' => route('frontdesk.dashboard'),
                    'time' => 'Action required',
                ];
            }

            // Rooms under Maintenance
            $maintenanceRooms = Room::where('status', 'MAINTENANCE')
                ->where('is_active', true)
                ->get();

            foreach ($maintenanceRooms as $room) {
                $notifications[] = [
                    'id' => 'room-maintenance-'.$room->room_id,
                    'type' => 'maintenance',
                    'icon' => 'fa-wrench text-danger',
                    'message' => "Room {$room->room_number} is under maintenance.",
                    'link' => route('frontdesk.dashboard'),
                    'time' => 'In progress',
                ];
            }
        }

        // 1b. POS Approval notifications (Admin only)
        if ($user->hasPermission('manage-users')) {
            $pendingApprovals = PosApprovalRequest::where('status', 'pending')
                ->with(['requestedBy', 'order', 'tab'])
                ->get();

            foreach ($pendingApprovals as $req) {
                $typeLabel = match ($req->request_type) {
                    'refund' => 'Refund',
                    'cancel_tab' => 'Cancel Tab',
                    'cancel_order' => 'Cancel Order',
                    default => 'Approval',
                };
                $detail = '';
                if ($req->order) {
                    $detail = "Order {$req->order->order_number} (₱".number_format($req->order->total, 2).')';
                } elseif ($req->tab) {
                    $detail = "Tab '{$req->tab->tab_name}' (₱".number_format($req->tab->total, 2).')';
                } else {
                    $detail = "Request #{$req->request_id}";
                }

                $notifications[] = [
                    'id' => 'pos-approval-'.$req->request_id,
                    'type' => 'pos_approval',
                    'icon' => 'fa-check-double text-danger',
                    'message' => "POS {$typeLabel}: {$detail} requires authorization.",
                    'link' => route('admin.pos-approvals'),
                    'time' => $req->created_at->diffForHumans(),
                ];
            }
        }

        // 2. View Guest Folio / Process Checkout notifications
        if ($user->hasPermission('view-guest-folio') || $user->hasPermission('process-checkout')) {
            $pendingCheckouts = Booking::where('status', 'CHECKED_IN')
                ->whereDate('departure_date', '<=', $today)
                ->with(['folio.guest', 'room'])
                ->get();

            foreach ($pendingCheckouts as $booking) {
                $guestName = $booking->folio && $booking->folio->guest
                    ? ($booking->folio->guest->first_name.' '.$booking->folio->guest->last_name)
                    : 'Unknown Guest';
                $roomNumber = $booking->room ? $booking->room->room_number : 'Unassigned';
                $notifications[] = [
                    'id' => 'booking-checkout-'.$booking->booking_id,
                    'type' => 'checkout',
                    'icon' => 'fa-door-open text-primary',
                    'message' => "Guest {$guestName} (Room {$roomNumber}) is due for checkout today.",
                    'link' => route('frontdesk.guest-folio'),
                    'time' => $booking->departure_date->format('M d, Y'),
                ];
            }

            // Pending Expenses
            $pendingExpenses = Expense::where('status', 'PENDING')->get();
            foreach ($pendingExpenses as $expense) {
                $notifications[] = [
                    'id' => 'expense-pending-'.$expense->expense_id,
                    'type' => 'billing',
                    'icon' => 'fa-file-invoice-dollar text-info',
                    'message' => "Expense: {$expense->description} (₱".number_format($expense->amount, 2).') needs approval.',
                    'link' => route('accounting.expenses'),
                    'time' => $expense->expense_date->format('M d, Y'),
                ];
            }
        }

        // 3. Manage Inventory notifications
        $hasProductsTable = Schema::hasTable('pos_products');
        if ($user->hasPermission('manage-inventory') && $hasProductsTable) {
            $lowStockProducts = PosProduct::with('category')->lowStock()->orderBy('stock_quantity')->limit(10)->get();

            foreach ($lowStockProducts as $product) {
                $threshold = $product->effectiveLowStockThreshold();
                $notifications[] = [
                    'id' => 'inventory-low-'.$product->product_id,
                    'type' => 'inventory',
                    'icon' => 'fa-box-open text-danger',
                    'message' => "Low Stock: '{$product->name}' is below {$threshold} (Current: {$product->stock_quantity}).",
                    'link' => route('coffeeshop.inventory', ['filter' => 'low_stock']),
                    'time' => 'Low Stock',
                ];
            }
        }

        // 4. Manage Shifts notifications
        if ($user->hasPermission('manage-shifts') || $user->hasPermission('manage-reservations')) {
            $activeShift = Shift::where('user_id', $user->user_id)
                ->whereNull('end_time')
                ->first();

            if (! $activeShift) {
                $notifications[] = [
                    'id' => 'shift-none-open',
                    'type' => 'shift',
                    'icon' => 'fa-clock-rotate-left text-warning',
                    'message' => 'No active shift open. Please start a shift.',
                    'link' => route('frontdesk.dashboard'),
                    'time' => 'Attention',
                ];
            }
        }

        // 5. Sidebar low stock count
        $lowStockCount = 0;
        if ($hasProductsTable) {
            $lowStockCount = PosProduct::lowStock()->count();
        }

        $data = [
            'notifications' => $notifications,
            'lowStockCount' => $lowStockCount,
        ];

        return response()->json($data);
    }
}
