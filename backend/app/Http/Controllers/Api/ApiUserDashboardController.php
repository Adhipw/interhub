<?php

namespace App\Http\Controllers\Api;

use App\Models\Internship;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiUserDashboardController extends ApiBaseController
{
    /**
     * Get data for the user (student) dashboard.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (! $user) {
                return $this->sendError('Unauthenticated', [], 401);
            }

            $stats = [
                'total_applications' => $user->applications()->count(),
                'active_applications' => $user->applications()->whereNotIn('status', ['accepted', 'rejected'])->count(),
                'saved_internships' => $user->savedInternships()->count(),
                'profile_completion' => $this->calculateProfileCompletion($user),
            ];

            $recent_applications = $user->applications()
                ->with('internship.company')
                ->latest()
                ->limit(5)
                ->get();

            $saved_internships = $user->savedInternships()
                ->with('internship.company')
                ->latest()
                ->limit(5)
                ->get();

            $recommended_internships = Internship::with('company')
                ->where('status', 'published')
                ->latest()
                ->limit(3)
                ->get();

            $notifications = [];
            if (method_exists($user, 'notifications')) {
                $notifications = $user->notifications()
                    ->latest()
                    ->limit(10)
                    ->get();
            }

            return $this->sendResponse([
                'stats' => $stats,
                'recent_applications' => $recent_applications,
                'saved_internships' => $saved_internships,
                'recommended_internships' => $recommended_internships,
                'notifications' => $notifications,
            ], 'User dashboard data retrieved');
        } catch (\Exception $e) {
            Log::error('Dashboard Error: '.$e->getMessage(), [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->sendError('Internal Server Error', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Calculate profile completion percentage.
     */
    private function calculateProfileCompletion($user): int
    {
        $points = 0;
        $total = 6;

        $detail = $user->detail;

        if ($detail) {
            if (! empty($detail->bio)) {
                $points++;
            }
            if (! empty($detail->phone_number) || ! empty($user->phone_number)) {
                $points++;
            }
            if (! empty($detail->address)) {
                $points++;
            }
            if (! empty($detail->education)) {
                $points++;
            }
            if (! empty($detail->skills)) {
                $points++;
            }
            if (! empty($detail->cv_path)) {
                $points++;
            }
        }

        return (int) round(($points / $total) * 100);
    }
}
