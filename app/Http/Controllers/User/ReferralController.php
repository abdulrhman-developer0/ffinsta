<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $referrals = Referral::where('referrer_id', $user->id)
            ->with('referee')
            ->latest()
            ->get();

        $totalPointsEarned = $referrals->sum('points_awarded');
        $referralLink      = route('register') . '?ref=' . $user->referral_code;

        return view('user.referral.index', compact('referrals', 'totalPointsEarned', 'referralLink'));
    }
}
