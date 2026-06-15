<?php

namespace App\Http\Controllers\Api;

use App\Models\Application;
use App\Models\Internship;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiHrDashboardController extends ApiBaseController
{
    /**
     * Get data for the HR dashboard.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Deriving company from membership
        $membership = $user->companyMemberships()->where('is_active', true)->first();

        if (! $membership) {
            return $this->sendError('You are not associated with any active company.', [], 403);
        }

        $companyId = $membership->company_id;

        $stats = [
            'active_internships' => Internship::where('company_id', $companyId)->where('status', 'published')->count(),
            'total_applicants' => Application::whereHas('internship', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })->count(),
            'pending_review' => Application::whereHas('internship', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })->where('status', 'pending')->count(),
            'scheduled_interviews' => Application::whereHas('internship', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })->where('status', 'reviewing')->count(), // Simplified
        ];

        $recent_applications = Application::with(['user', 'internship'])
            ->whereHas('internship', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->latest()
            ->limit(10)
            ->get();

        $active_internships = Internship::where('company_id', $companyId)
            ->withCount('applications')
            ->latest()
            ->limit(5)
            ->get();

        return $this->sendResponse([
            'stats' => $stats,
            'recent_applications' => $recent_applications,
            'active_internships' => $active_internships,
            'company' => $user->companies()->find($companyId),
        ], 'HR dashboard data retrieved');
    }
}
