<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AuditLog;
use App\Models\SecurityEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class LogController extends Controller
{
    public function __construct()
    {
        // Harden: Ensure only Super Admins can access most logs
        // This is a basic hardening step
    }

    public function activityLogs(Request $request)
    {
        Gate::authorize('viewActivity', ActivityLog::class);

        $logs = ActivityLog::with('user.roles')
            ->when($request->search, function ($query, $search) {
                $query->where('action', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->type, function ($query, $type) {
                $query->where('action', $type);
            })
            ->when($request->date, function ($query, $date) {
                $query->whereDate('created_at', $date);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('SuperAdmin/Audits/ActivityLogs', [
            'logs' => $logs,
            'filters' => $request->only(['search', 'type', 'date']),
        ]);
    }

    public function auditLogs(Request $request)
    {
        Gate::authorize('viewAudit', AuditLog::class);

        $logs = AuditLog::with('user.roles')
            ->when($request->search, function ($query, $search) {
                $query->where('action', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('SuperAdmin/Audits/AuditLogs', [
            'logs' => $logs,
            'filters' => $request->only(['search']),
        ]);
    }

    public function securityEvents(Request $request)
    {
        Gate::authorize('viewSecurity', SecurityEvent::class);

        $events = SecurityEvent::with('user.roles')
            ->when($request->search, function ($query, $search) {
                $query->where('event_type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('SuperAdmin/Audits/SecurityEvents', [
            'events' => $events,
            'filters' => $request->only(['search']),
        ]);
    }
}
