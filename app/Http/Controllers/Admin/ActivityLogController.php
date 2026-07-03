<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::with('admin')
            ->when($request->search, fn($q, $s) => $q->where('description', 'like', "%$s%")
                ->orWhere('action', 'like', "%$s%"))
            ->when($request->action, fn($q, $a) => $q->where('action', $a))
            ->when($request->admin_id, fn($q, $id) => $q->where('admin_id', $id))
            ->when($request->from, fn($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->to, fn($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $actions = ActivityLog::distinct()->pluck('action')->sort()->values();

        return view('admin.logs.index', compact('logs', 'actions'));
    }
}
