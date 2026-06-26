<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosApprovalRequest;
use App\Services\Coffeeshop\PosOrderService;
use App\Services\Coffeeshop\PosTabService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PosApprovalController extends Controller
{
    public function __construct(
        private PosOrderService $orderService,
        private PosTabService $tabService
    ) {}

    public function index(): View
    {
        $pendingRequests = PosApprovalRequest::with(['order', 'tab', 'requestedBy'])
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();

        $resolvedRequests = PosApprovalRequest::with(['order', 'tab', 'requestedBy', 'resolvedBy'])
            ->where('status', '!=', 'pending')
            ->orderByDesc('resolved_at')
            ->paginate(15);

        return view('admin.pos-approvals.index', compact('pendingRequests', 'resolvedRequests'));
    }

    public function approve(int $id): RedirectResponse|JsonResponse
    {
        $approvalRequest = PosApprovalRequest::findOrFail($id);

        if ($approvalRequest->status !== 'pending') {
            if (request()->wantsJson()) {
                return response()->json(['message' => 'This request has already been processed.'], 422);
            }

            return back()->withErrors(['request' => 'This request has already been processed.']);
        }

        try {
            if ($approvalRequest->request_type === 'refund') {
                if (! $approvalRequest->order) {
                    throw new \RuntimeException('Associated order not found.');
                }
                $this->orderService->refundOrder($approvalRequest->order);
            } elseif ($approvalRequest->request_type === 'cancel_tab') {
                if (! $approvalRequest->tab) {
                    throw new \RuntimeException('Associated tab not found.');
                }
                $this->tabService->cancelTab($approvalRequest->tab);
            } elseif ($approvalRequest->request_type === 'cancel_order') {
                if (! $approvalRequest->order) {
                    throw new \RuntimeException('Associated order not found.');
                }
                $this->orderService->cancelOrder($approvalRequest->order);
            }

            $approvalRequest->update([
                'status' => 'approved',
                'resolved_by' => auth()->id() ?? 1,
                'resolved_at' => now(),
            ]);

            Cache::flush();

            if (request()->wantsJson()) {
                return response()->json(['message' => 'POS approval request approved and executed.']);
            }

            return back()->with('success', 'POS approval request approved and executed.');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['message' => 'Failed to approve: '.$e->getMessage()], 422);
            }

            return back()->withErrors(['request' => 'Failed to approve: '.$e->getMessage()]);
        }
    }

    public function reject(int $id): RedirectResponse|JsonResponse
    {
        $approvalRequest = PosApprovalRequest::findOrFail($id);

        if ($approvalRequest->status !== 'pending') {
            if (request()->wantsJson()) {
                return response()->json(['message' => 'This request has already been processed.'], 422);
            }

            return back()->withErrors(['request' => 'This request has already been processed.']);
        }

        $approvalRequest->update([
            'status' => 'rejected',
            'resolved_by' => auth()->id() ?? 1,
            'resolved_at' => now(),
        ]);

        Cache::flush();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'POS approval request rejected.']);
        }

        return back()->with('success', 'POS approval request rejected.');
    }
}
