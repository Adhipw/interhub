<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Attendance\CheckInRequest;
use App\Http\Requests\Attendance\CheckOutRequest;
use App\Http\Resources\Api\AttendanceResource;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiAttendanceController extends ApiBaseController
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Get attendance status and history for current user.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $activeApplication = $this->attendanceService->getActiveApplication($user);

        if (! $activeApplication) {
            return $this->sendError(__('You do not have an active internship at the moment.'), [], 404);
        }

        $activeSession = $this->attendanceService->getActiveSession($user, $activeApplication);
        $history = $this->attendanceService->getUserAttendanceHistory($user, $activeApplication);

        return $this->sendResponse([
            'active_application' => $activeApplication,
            'active_session' => $activeSession ? new AttendanceResource($activeSession) : null,
            'history' => AttendanceResource::collection($history)->response()->getData(true),
        ], __('Attendance data retrieved successfully.'));
    }

    /**
     * Perform check-in.
     */
    public function checkIn(CheckInRequest $request): JsonResponse
    {
        $user = Auth::user();
        $activeApplication = $this->attendanceService->getActiveApplication($user);

        if (! $activeApplication || $activeApplication->id !== (int) $request->application_id) {
            return $this->sendError(__('Invalid internship application.'), [], 403);
        }

        try {
            $attendance = $this->attendanceService->checkIn($activeApplication, $user, $request->validated());

            return $this->sendResponse(
                new AttendanceResource($attendance),
                __('Check-in successful. Have a great work day!')
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Perform check-out.
     */
    public function checkOut(Attendance $attendance, CheckOutRequest $request): JsonResponse
    {
        if ($attendance->user_id !== Auth::id()) {
            return $this->sendError(__('Unauthorized'), [], 403);
        }

        if ($attendance->check_out_at) {
            return $this->sendError(__('You have already checked out for this session.'), [], 422);
        }

        $attendance = $this->attendanceService->checkOut($attendance, Auth::user(), $request->validated());

        return $this->sendResponse(
            new AttendanceResource($attendance),
            __('Check-out successful. See you tomorrow!')
        );
    }

    /**
     * Update live location during session.
     */
    public function updateLocation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $this->attendanceService->updateLiveLocation(Auth::id(), $validated['latitude'], $validated['longitude']);

        return $this->sendResponse(null, __('Location updated.'));
    }

    /**
     * Submit attendance correction request.
     */
    public function requestCorrection(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'attendance_id' => 'required|exists:attendances,id',
            'new_check_in_at' => 'nullable|date',
            'new_check_out_at' => 'nullable|date',
            'reason' => 'required|string|max:500',
        ]);

        $attendance = Attendance::findOrFail($validated['attendance_id']);

        if ($attendance->user_id !== Auth::id()) {
            return $this->sendError(__('Unauthorized'), [], 403);
        }

        $correction = $this->attendanceService->requestCorrection($attendance, Auth::user(), $validated);

        return $this->sendResponse($correction, __('Correction request submitted successfully.'));
    }
}
