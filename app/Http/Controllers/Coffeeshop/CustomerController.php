<?php

namespace App\Http\Controllers\Coffeeshop;

use App\Http\Controllers\Controller;
use App\Services\Coffeeshop\PosAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request, PosAnalyticsService $analytics): View
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $data = $analytics->customerHistory($search, $status);

        return view('coffeeshop.customers.index', [
            'tabs' => $data['tabs'],
            'orders' => $data['orders'],
            'frequentCustomers' => $data['frequent'],
            'search' => $search,
            'status' => $status,
        ]);
    }
}
