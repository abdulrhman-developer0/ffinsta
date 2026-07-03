<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = auth()->user();

        $stats = [
            'points'              => $user->points,
            'active_orders'       => Order::where('user_id', $user->id)->where('status', 'active')->count(),
            'completed_orders'    => Order::where('user_id', $user->id)->where('status', 'completed')->count(),
            'total_requested'     => Order::where('user_id', $user->id)->sum('requested_qty'),
            'total_delivered'     => Order::where('user_id', $user->id)->sum('delivered_qty'),
        ];

        $recentTransactions = $user->pointsTransactions()
            ->latest()
            ->limit(10)
            ->get();

        $activeOrders = Order::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'active'])
            ->latest()
            ->limit(5)
            ->get();

        return view('user.dashboard.index', compact('stats', 'recentTransactions', 'activeOrders'));
    }
}
