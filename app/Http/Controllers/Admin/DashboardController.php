<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PointsTransaction;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $stats = [
            'total_users'      => User::where('role', 'user')->count(),
            'total_orders'     => Order::count(),
            'active_orders'    => Order::where('status', 'active')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'pending_orders'   => Order::where('status', 'pending')->count(),
            'total_points'     => User::sum('points'),
            'total_visits'     => \App\Models\Visit::count(),
            'today_visits'     => \App\Models\Visit::where('visited_date', \Carbon\Carbon::today()->toDateString())->count(),
            'active_users'     => \Illuminate\Support\Facades\DB::table('sessions')
                                    ->where('last_activity', '>=', now()->subMinutes(5)->timestamp)
                                    ->distinct('ip_address')
                                    ->count('ip_address'),
        ];

        $recentOrders = Order::with('user')
            ->latest()
            ->limit(10)
            ->get();

        // Chart data: orders per day over last 30 days
        $ordersChart = Order::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        // User growth: cumulative per day over last 30 days
        $userGrowthChart = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('role', 'user')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        // Points distribution
        $pointsEarned  = PointsTransaction::where('amount', '>', 0)->where('type', 'earn')->sum('amount');
        $pointsSpent   = abs(PointsTransaction::where('type', 'spend')->sum('amount'));
        $pointsCoupon  = PointsTransaction::where('type', 'coupon')->sum('amount');
        $pointsReferral = PointsTransaction::where('type', 'referral')->sum('amount');

        return view('admin.dashboard.index', compact(
            'stats',
            'recentOrders',
            'ordersChart',
            'userGrowthChart',
            'pointsEarned',
            'pointsSpent',
            'pointsCoupon',
            'pointsReferral'
        ));
    }
}
