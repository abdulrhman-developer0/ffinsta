<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\InstagramAccount;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\SettingService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService   $orderService,
        protected SettingService $settingService
    ) {}

    public function index(Request $request)
    {
        $orders = Order::where('user_id', auth()->id())
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->search, fn($q, $s) => $q->where('order_number', 'like', "%$s%")
                ->orWhere('instagram_username', 'like', "%$s%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('user.orders.index', compact('orders'));
    }

    public function create()
    {
        $accounts = InstagramAccount::where('user_id', auth()->id())
            ->where('status', 'active')
            ->get();

        $pointsPerFollow = (int) $this->settingService->get('points_per_follow', 10);
        $userPoints      = auth()->user()->points;

        return view('user.orders.create', compact('accounts', 'pointsPerFollow', 'userPoints'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'instagram_account_id' => ['required', 'exists:instagram_accounts,id'],
            'quantity'             => ['required', 'integer', 'min:1', 'max:10000'],
        ]);

        // Ensure account belongs to user
        $account = InstagramAccount::where('id', $request->instagram_account_id)
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->firstOrFail();

        try {
            $order = $this->orderService->create(
                auth()->id(),
                $account->id,
                $account->username,
                $request->quantity
            );

            \App\Jobs\FetchInstagramProfilePictureJob::dispatch($order);

            return redirect()->route('user.orders.show', $order)
                ->with('success', __('Order #:number created successfully! It will be processed soon.', ['number' => $order->order_number]));
        } catch (\App\Exceptions\InsufficientPointsException $e) {
            return back()->withErrors(['quantity' => __('You do not have enough points for this order.')]);
        } catch (\Exception $e) {
            return back()->withErrors(['quantity' => $e->getMessage()]);
        }
    }

    public function show(Order $order)
    {
        // Ensure order belongs to the authenticated user
        abort_if($order->user_id !== auth()->id(), 403);

        $order->load('instagramAccount', 'followTasks');

        return view('user.orders.show', compact('order'));
    }
}
