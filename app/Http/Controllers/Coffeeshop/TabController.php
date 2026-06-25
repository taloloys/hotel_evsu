<?php

namespace App\Http\Controllers\Coffeeshop;

use App\Http\Controllers\Controller;
use App\Models\PosTab;
use App\Services\Coffeeshop\PosTabService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TabController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status', 'open');

        $query = PosTab::with(['items.product', 'room', 'guest', 'order', 'openedByUser'])
            ->orderByDesc('opened_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($request->filled('search')) {
            $query->where('tab_name', 'like', '%'.$request->search.'%');
        }

        $tabs = $query->paginate(20)->withQueryString();

        return view('coffeeshop.tabs.index', compact('tabs', 'status'));
    }

    public function reopen(PosTab $tab, PosTabService $tabService): RedirectResponse
    {
        try {
            $tabService->reopenTab($tab);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['tab' => $e->getMessage()]);
        }

        return back()->with('success', 'Tab reopened successfully.');
    }
}
