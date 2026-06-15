<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AttendanceIndexRequest;
use App\Http\Resources\Api\AttendanceResource;
use App\Models\Attendance;
use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ApiHrAttendanceController extends ApiBaseController
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Display a listing of attendances for the HR's company.
     *
     * Refactored to use Service and Resource patterns.
     */
    public function index(AttendanceIndexRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $company = $user->companies()->first();

        if (! $company) {
            return $this->sendError(__('error.company_not_found'));
        }

        $attendances = $this->attendanceService->getCompanyAttendances($company, $request->validated());
        $liveLocations = $this->attendanceService->getLiveLocations($attendances);

        return $this->sendResponse([
            'attendances' => AttendanceResource::collection($attendances)->response()->getData(true),
            'liveLocations' => $liveLocations,
        ], __('hr.attendance_retrieved'));
    }

    /**
     * Get attendance stats for the dashboard.
     */
    public function stats(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $company = $user->companies()->first();

        if (! $company) {
            return $this->sendError(__('error.company_not_found'));
        }

        // Logic for stats can also be moved to service if it grows complex
        $today = now()->toDateString();

        $stats = [
            'total_present' => Attendance::whereDate('check_in_at', $today)
                ->whereHas('application.internship', fn ($q) => $q->where('company_id', $company->id))
                ->count(),
            'currently_active' => Attendance::whereDate('check_in_at', $today)
                ->whereNull('check_out_at')
                ->whereHas('application.internship', fn ($q) => $q->where('company_id', $company->id))
                ->count(),
        ];

        return $this->sendResponse($stats, __('hr.stats_retrieved'));
    }
}
