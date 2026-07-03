<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PointsTransaction;
use Illuminate\Http\Request;

class PointsController extends Controller
{
    public function index(Request $request)
    {
        $query = PointsTransaction::where('user_id', auth()->id());

        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->from) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->to) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $transactions = $query->latest()->paginate(25)->withQueryString();

        return view('user.points.index', compact('transactions'));
    }
}
