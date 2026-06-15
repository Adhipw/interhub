<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Internship;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $company = app('current_company');

        $stats = [
            'active_internships' => Internship::where('company_id', $company->id)->where('status', 'published')->count(),
            'total_applicants' => Application::whereHas('internship', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })->count(),
            'pending_review' => Application::whereHas('internship', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })->where('status', 'pending')->count(),
            'scheduled_interviews' => Application::whereHas('internship', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })->where('status', 'reviewing')->whereHas('interviewSchedules')->count(),
        ];

        $recent_applications = Application::with(['user', 'internship'])
            ->whereHas('internship', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->latest()
            ->limit(5)
            ->get();

        return Inertia::render('HR/Dashboard', [
            'stats' => $stats,
            'recentApplications' => $recent_applications,
            'company' => $company,
        ]);
    }
}
