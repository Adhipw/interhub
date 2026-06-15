<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Internship;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'pending_companies' => Company::where('is_verified', false)->count(),
            'active_internships' => Internship::where('status', 'published')->count(),
            'total_applications' => Application::count(),
        ];

        $recentLogs = AuditLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        $pendingCompanies = Company::where('is_verified', false)
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentLogs' => $recentLogs,
            'pendingCompanies' => $pendingCompanies,
        ]);
    }
}
