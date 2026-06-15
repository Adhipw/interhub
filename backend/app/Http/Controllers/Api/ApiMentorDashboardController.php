<?php

namespace App\Http\Controllers\Api;

use App\Models\Application;
use App\Models\Attendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ApiMentorDashboardController extends ApiBaseController
{
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $active_mentees = Application::where('status', 'accepted')
            ->where('mentor_user_id', $user->id)
            ->with(['user.detail', 'internship'])
            ->latest()
            ->get();

        $today = now()->toDateString();
        $attendance_summary = Attendance::whereDate('check_in_at', $today)
            ->whereHas('application', function ($q) use ($user) {
                $q->where('mentor_user_id', $user->id);
            })
            ->with('user')
            ->get();

        $stats = [
            'total_mentees' => $active_mentees->count(),
            'present_today' => $attendance_summary->count(),
            'pending_tasks' => 0, // Placeholder
            'completed_evaluations' => 0, // Placeholder
        ];

        return $this->sendResponse([
            'stats' => $stats,
            'active_mentees' => $active_mentees,
            'attendance_today' => $attendance_summary,
            'recent_feedbacks' => [],
        ], 'Mentor dashboard data retrieved');
    }
}
