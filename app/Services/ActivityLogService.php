<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Log an admin action.
     */
    public function log(
        string $action,
        string $description,
        string $subjectType = null,
        int    $subjectId = null,
        int    $adminId = null
    ): void {
        ActivityLog::create([
            'admin_id'     => $adminId ?? Auth::id(),
            'action'       => $action,
            'description'  => $description,
            'subject_type' => $subjectType,
            'subject_id'   => $subjectId,
            'ip_address'   => Request::ip(),
        ]);
    }
}
