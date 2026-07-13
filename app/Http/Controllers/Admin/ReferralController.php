<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Referral;
use Illuminate\Http\Request;


class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('role', 'user')
            ->withCount('referrals')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%"))
            ->with(['referrals.referee'])
            ->paginate(20);

        return view('admin.referrals.index', compact('users'));
    }
    
    public function show(User $user)
    {
         $referrals = Referral::with('referee')
            ->where('referrer_id', $user->id)
            ->latest()
            ->paginate(20);

        return view(
            'admin.referrals.show',
            compact('user', 'referrals')
        );
    }
}
