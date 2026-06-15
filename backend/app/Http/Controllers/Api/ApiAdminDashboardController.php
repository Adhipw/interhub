<?php

namespace App\Http\Controllers\Api;

use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Internship;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class ApiAdminDashboardController extends ApiBaseController
{
    public function index(): JsonResponse
    {
        try {
            // Fetch stats efficiently in a single block
            $stats = [
                'total_users' => User::count(),
                'pending_companies' => Company::where('is_verified', false)->count(),
                'active_internships' => Internship::where('status', 'published')->count(),
                'total_applications' => Application::count(),
            ];

            // Fetch recent activity logs with user relationship
            $recent_logs = [];
            if (\Schema::hasTable('audit_logs')) {
                $recent_logs = AuditLog::with('user:id,name,email')
                    ->latest()
                    ->take(6)
                    ->get();
            }

            // Fetch companies awaiting verification
            $pending_companies = Company::where('is_verified', false)
                ->latest()
                ->take(5)
                ->get();

            return $this->sendResponse([
                'stats' => $stats,
                'recent_logs' => $recent_logs,
                'pending_companies' => $pending_companies,
            ], 'Admin dashboard operational data synchronized.');

        } catch (\Exception $e) {
            \Log::error('Critical failure in Admin Dashboard API', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->sendError('System encountered an issue retrieving management data.', [
                'debug_message' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
