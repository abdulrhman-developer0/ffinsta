<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstagramAccount;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class InstagramAccountController extends Controller
{
    public function __construct(protected ActivityLogService $activityLogService) {}

    public function index(Request $request)
    {
        $accounts = InstagramAccount::with('user')
            ->when($request->search, fn($q, $s) => $q->where('username', 'like', "%$s%")
                ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%$s%")))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.instagram.index', compact('accounts'));
    }

    public function show(InstagramAccount $account)
    {
        $account->load('user', 'orders');
        return view('admin.instagram.show', compact('account'));
    }

    public function updateStatus(Request $request, InstagramAccount $account)
    {
        $request->validate([
            'status' => ['required', 'in:active,inactive,banned'],
        ]);

        $account->update(['status' => $request->status]);
        $this->activityLogService->log('update_instagram_status', "Changed status of @{$account->username} to {$request->status}", 'instagram_account', $account->id);

        return back()->with('success', __('Account status updated.'));
    }
}
