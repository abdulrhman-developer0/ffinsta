<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstagramAccount;
use App\Models\Order;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService       $orderService,
        protected ActivityLogService $activityLogService
    ) {}

    public function index(Request $request)
    {
        $orders = Order::with('user', 'instagramAccount')
            ->when($request->search, fn($q, $s) => $q->where('order_number', 'like', "%$s%")
                ->orWhere('instagram_username', 'like', "%$s%")
                ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%$s%")))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->priority, fn($q, $p) => $q->where('priority', $p))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function completed(Request $request)
    {
        $orders = Order::with('user')
            ->where('status', 'completed')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.completed', compact('orders'));
    }

    public function create()
    {
        $users = User::where('role', 'user')->with('instagramAccounts')->orderBy('name')->get();
        return view('admin.orders.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'              => ['required', 'exists:users,id'],
            'instagram_account_id' => ['nullable', 'exists:instagram_accounts,id'],
            'custom_username'      => ['nullable', 'string', 'max:255'],
            'requested_qty'        => ['required', 'integer', 'min:1'],
            'priority'             => ['required', 'in:normal,high'],
            'deduct_points'        => ['required', 'boolean'],
            'admin_notes'          => ['nullable', 'string'],
        ]);

        if (empty($request->instagram_account_id) && empty($request->custom_username)) {
            return back()->withErrors(['instagram_account_id' => __('Please select an account or enter a custom username.')])->withInput();
        }

        $accountId = null;
        $username = null;

        if ($request->filled('instagram_account_id')) {
            $account = InstagramAccount::where('id', $request->instagram_account_id)
                ->where('user_id', $request->user_id)
                ->firstOrFail();
            $accountId = $account->id;
            $username = $account->username;
        } else {
            $username = str_replace('@', '', $request->custom_username);
        }

        if ($request->boolean('deduct_points')) {
            try {
                $order = $this->orderService->create(
                    $request->user_id,
                    $accountId,
                    $username,
                    $request->requested_qty
                );
                
                // Admin created order, if priority is high or active we can set it.
                // We should make it active immediately since an admin created it.
                $order->update(['status' => 'active', 'priority' => $request->priority, 'admin_notes' => $request->admin_notes]);
            } catch (\Exception $e) {
                return back()->withErrors(['order' => $e->getMessage()])->withInput();
            }
        } else {
            $order = $this->orderService->createManual(
                $request->user_id,
                $accountId,
                $username,
                $request->requested_qty,
                'active',
                $request->priority,
                $request->admin_notes
            );
        }

        if ($request->filled('profile_picture_url')) {
            $order->update(['profile_picture_url' => $request->profile_picture_url]);
        }

        $this->activityLogService->log('create_order', "Created order #{$order->order_number} for user ID {$request->user_id}", 'order', $order->id);

        \App\Jobs\FetchInstagramProfilePictureJob::dispatch($order);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', __('Order created.'));
    }

    public function show(Order $order)
    {
        $order->load('user', 'instagramAccount', 'followTasks');
        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status'       => ['sometimes', 'in:pending,active,completed,cancelled'],
            'priority'     => ['sometimes', 'in:normal,high'],
            'admin_notes'  => ['nullable', 'string'],
            'requested_qty' => ['sometimes', 'integer', 'min:1'],
        ]);

        $oldStatus = $order->status;
        $order->update($request->only('status', 'priority', 'admin_notes', 'requested_qty'));

        // If status changed to active, generate tasks
        if ($oldStatus !== 'active' && $order->status === 'active') {
            $this->orderService->activate($order->fresh());
        }

        $this->activityLogService->log('update_order', "Updated order #{$order->order_number}", 'order', $order->id);

        return back()->with('success', __('Order updated.'));
    }

    public function activate(Order $order)
    {
        try {
            $this->orderService->activate($order);
            $this->activityLogService->log('activate_order', "Activated order #{$order->order_number}", 'order', $order->id);
            return back()->with('success', __('Order activated and follow tasks generated.'));
        } catch (\Exception $e) {
            return back()->withErrors(['order' => $e->getMessage()]);
        }
    }

    public function cancel(Request $request, Order $order)
    {
        try {
            $refund = $request->boolean('refund', true);
            $this->orderService->cancel($order, $refund);
            $this->activityLogService->log('cancel_order', "Cancelled order #{$order->order_number}", 'order', $order->id);
            return back()->with('success', __('Order cancelled.'));
        } catch (\Exception $e) {
            return back()->withErrors(['order' => $e->getMessage()]);
        }
    }

    public function destroy(Order $order)
    {
        $this->activityLogService->log('delete_order', "Deleted order #{$order->order_number}", 'order', $order->id);
        $order->followTasks()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', __('Order deleted.'));
    }

    public function togglePriority(Order $order)
    {
        $order->update([
            'priority' => $order->priority === 'high' ? 'normal' : 'high'
        ]);
        
        $this->activityLogService->log('update_order', "Toggled priority to {$order->priority} for order #{$order->order_number}", 'order', $order->id);

        return back()->with('success', __('Order priority updated.'));
    }
}
