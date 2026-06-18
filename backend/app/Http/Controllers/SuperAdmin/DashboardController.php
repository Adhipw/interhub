<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SecurityEvent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $load = function_exists('sys_getloadavg') ? sys_getloadavg()[0] : 0;
        $freeSpace = @disk_free_space('/') ?: 1;
        $totalSpace = @disk_total_space('/') ?: 1;
        $storageUsedPercent = round((($totalSpace - $freeSpace) / $totalSpace) * 100, 1);

        $securityEvents = SecurityEvent::latest()->take(5)->get();
        $auditLogs = AuditLog::with('user.roles')->latest()->take(10)->get();

        $stats = [
            'total_users' => User::count(),
            'total_admins' => User::role('admin')->count(),
            'total_super_admins' => User::role('super_admin')->count(),
            'active_sessions' => DB::table('personal_access_tokens')->where('last_used_at', '>', now()->subHours(24))->count(),
            'server_load' => $load,
            'storage_used' => $storageUsedPercent,
        ];

        $requestStart = defined('LARAVEL_START')
            ? LARAVEL_START
            : ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true));

        return Inertia::render('SuperAdmin/Dashboard', [
            'stats' => $stats,
            'security_events' => $securityEvents,
            'audit_logs' => $auditLogs,
            'system_info' => [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
                'database_driver' => DB::getDriverName(),
                'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2).' MB',
                'os' => PHP_OS,
            ],
            'system_health' => [
                'status' => 'healthy',
                'uptime' => '99.9%',
                'latency' => round((microtime(true) - $requestStart) * 1000, 2).'ms',
                'database' => 'connected',
                'storage' => $storageUsedPercent < 90 ? 'optimal' : 'critical',
            ],
        ]);
    }
}
