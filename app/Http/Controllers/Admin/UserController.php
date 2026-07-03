<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\PointsService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected PointsService      $pointsService,
        protected ActivityLogService $activityLogService
    ) {}

    public function index(Request $request)
    {
        $users = User::where('role', 'user')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%"))
            ->when($request->status === 'suspended', fn($q) => $q->where('is_suspended', true))
            ->when($request->status === 'active', fn($q) => $q->where('is_suspended', false))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('instagramAccounts', 'orders', 'pointsTransactions', 'referrals');

        return view('admin.users.show', compact('user'));
    }

    public function toggleSuspend(User $user)
    {
        abort_if($user->isAdmin(), 403, 'Cannot suspend admin users.');

        $user->update(['is_suspended' => !$user->is_suspended]);

        $action = $user->is_suspended ? 'suspend_user' : 'unsuspend_user';
        $this->activityLogService->log($action, ($user->is_suspended ? 'Suspended' : 'Unsuspended') . " user: {$user->name} ({$user->email})", 'user', $user->id);

        $msg = $user->is_suspended ? __('User suspended.') : __('User unsuspended.');
        return back()->with('success', $msg);
    }

    public function adjustPoints(Request $request, User $user)
    {
        $request->validate([
            'action'      => ['required', 'in:add,remove'],
            'amount'      => ['required', 'integer', 'min:1'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        $amount = $request->action === 'remove' ? -$request->amount : $request->amount;

        $this->pointsService->adminAdjust($user->id, $amount, $request->description);

        $actionText = $request->action === 'remove' ? 'Removed' : 'Added';
        $this->activityLogService->log(
            'adjust_points',
            "{$actionText} {$request->amount} points for {$user->name}: {$request->description}",
            'user',
            $user->id
        );

        return back()->with('success', __('Points adjusted successfully.'));
    }

    public function destroy(User $user)
    {
        abort_if($user->isAdmin(), 403, 'Cannot delete admin users.');

        $this->activityLogService->log('delete_user', "Deleted user: {$user->name} ({$user->email})", 'user', $user->id);

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', __('User deleted.'));
    }

    public function impersonate(User $user)
    {
        abort_if($user->isAdmin(), 403, 'Cannot impersonate admin users.');

        $this->activityLogService->log('impersonate_user', "Impersonated user: {$user->name} ({$user->email})", 'user', $user->id);

        $adminId = auth()->id();
        auth()->login($user);
        session()->put('impersonated_by', $adminId);

        return redirect()->route('user.dashboard')->with('success', __('You are now logged in as ') . $user->name);
    }

    public function leaveImpersonation()
    {
        if (session()->has('impersonated_by')) {
            $adminId = session()->pull('impersonated_by');
            auth()->loginUsingId($adminId);
            return redirect()->route('admin.dashboard')->with('success', __('Returned to admin panel.'));
        }

        return redirect()->route('user.dashboard');
    }
}
