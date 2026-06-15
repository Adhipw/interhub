<?php

namespace App\Http\Controllers\Api;

use App\Models\AuditLog;
use App\Models\SecurityEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiAdminAuditController extends ApiBaseController
{
    /**
     * Display a listing of audit logs.
     */
    public function auditLogs(Request $request): JsonResponse
    {
        $logs = AuditLog::with('user')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('action', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($qu) use ($search) {
                            $qu->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return $this->sendResponse($logs, 'Audit logs retrieved successfully');
    }

    /**
     * Display a listing of security events.
     */
    public function securityEvents(Request $request): JsonResponse
    {
        $events = SecurityEvent::with('user')
            ->when($request->severity, function ($query, $severity) {
                $query->where('severity', $severity);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return $this->sendResponse($events, 'Security events retrieved successfully');
    }
}
