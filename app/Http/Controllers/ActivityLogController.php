<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with([
            'performer:id,first_name,last_name',
            'user:id,first_name,last_name',
        ])->latest()->get();

        return view('admin.activity-logs', [
            'logs' => $logs,
            'user' => auth()->user(),
            'view' => 'activity-logs',
        ]);
    }
}
