<?php

namespace App\Http\Controllers;

use App\Http\Requests\Attendance\CheckInRequest;
use App\Http\Requests\Attendance\CheckOutRequest;
use App\Models\Application;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Services\AttendanceService;
use App\Services\Logging\ActivityLogger;
use App\Services\Logging\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    /**
     * Show the attendance dashboard for the user.
     */
    public function index()
    {
        $userId = Auth::id();
        $activeApplication = Application::where('user_id', $userId)
            ->where('status', 'accepted')
            ->with('internship.company')
            ->first();

        if (! $activeApplication) {
            return Inertia::render('User/Attendance/Index', [
                'error' => 'Anda tidak memiliki magang aktif yang memerlukan absensi.',
            ]);
        }

        $activeSession = Attendance::where('user_id', $userId)
            ->where('application_id', $activeApplication->id)
            ->whereNull('check_out_at')
            ->first();

        $history = Attendance::where('user_id', $userId)
            ->where('application_id', $activeApplication->id)
            ->latest()
            ->paginate(10);

        return Inertia::render('User/Attendance/Index', [
            'activeApplication' => $activeApplication,
            'activeSession' => $activeSession,
            'history' => $history,
        ]);
    }

    /**
     * Handle check-in.
     */
    public function checkIn(CheckInRequest $request)
    {
        $app = Application::with('internship.company')->findOrFail($request->application_id);

        if ($app->user_id !== Auth::id()) {
            abort(403);
        }

        // Check geofence
        $withinGeofence = app(AttendanceService::class)->isWithinGeofence(
            $request->latitude,
            $request->longitude,
            $app->internship
        );

        if (! $withinGeofence) {
            return response()->json([
                'error' => 'Anda berada di luar jangkauan lokasi perusahaan.',
            ], 422);
        }

        $attendance = Attendance::create([
            'user_id' => Auth::id(),
            'application_id' => $app->id,
            'check_in_at' => now(),
            'check_in_location' => [
                'lat' => $request->latitude,
                'lng' => $request->longitude,
            ],
            'status' => 'present',
        ]);

        // Audit sensitive location access
        AuditLogger::log('attendance_check_in', $attendance, null, [
            'lat' => $request->latitude,
            'lng' => $request->longitude,
        ], 'User checked in at location.');

        ActivityLogger::log('check_in', "User checked in for internship {$app->internship->title}");

        return response()->json([
            'message' => 'Check-in berhasil.',
            'attendance' => $attendance,
        ]);
    }

    /**
     * Handle check-out.
     */
    public function checkOut(Attendance $attendance, CheckOutRequest $request)
    {
        if ($attendance->user_id !== Auth::id()) {
            abort(403);
        }

        $attendance->update([
            'check_out_at' => now(),
            'check_out_location' => [
                'lat' => $request->latitude,
                'lng' => $request->longitude,
            ],
        ]);

        // Clear live location cache
        app(AttendanceService::class)->clearLiveLocation(Auth::id());

        AuditLogger::log('attendance_check_out', $attendance, null, [
            'lat' => $request->latitude,
            'lng' => $request->longitude,
        ], 'User checked out at location.');

        return response()->json([
            'message' => 'Check-out berhasil.',
        ]);
    }

    /**
     * Update live location during active session.
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        app(AttendanceService::class)->updateLiveLocation(Auth::id(), $request->latitude, $request->longitude);

        return response()->json(['message' => 'Location updated.']);
    }

    /**
     * Request a correction for attendance.
     */
    public function requestCorrection(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:attendances,id',
            'new_check_in_at' => 'nullable|date',
            'new_check_out_at' => 'nullable|date',
            'reason' => 'required|string|max:500',
        ]);

        $attendance = Attendance::findOrFail($request->attendance_id);
        if ($attendance->user_id !== Auth::id()) {
            abort(403);
        }

        $correction = AttendanceCorrection::create([
            'attendance_id' => $attendance->id,
            'requested_by' => Auth::id(),
            'new_check_in_at' => $request->new_check_in_at,
            'new_check_out_at' => $request->new_check_out_at,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Permintaan koreksi berhasil dikirim.',
            'correction' => $correction,
        ]);
    }
}
