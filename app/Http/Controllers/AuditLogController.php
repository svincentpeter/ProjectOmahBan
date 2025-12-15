<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:access_audit_log']);
    }

    public function index(Request $request)
    {
        $query = Activity::with('causer:id,name')
            ->latest();

        // Filter by log name
        if ($request->has('log_name') && $request->log_name) {
            $query->where('log_name', $request->log_name);
        }

        // Filter by event
        if ($request->has('event') && $request->event) {
            $query->where('event', $request->event);
        }

        // Filter by date
        if ($request->has('from') && $request->from) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->has('to') && $request->to) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $logs = $query->paginate(50);

        // Get available log names for filter
        $logNames = Activity::distinct('log_name')->pluck('log_name');

        return view('audit-log.index', compact('logs', 'logNames'));
    }
}
