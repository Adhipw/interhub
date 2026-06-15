<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\Company;
use App\Models\User;
use App\Services\Logging\ActivityLogger;
use App\Services\Logging\AuditLogger;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    /**
     * Get active internship application for a user.
     */
    public function getActiveApplication(User $user): ?Application
    {
        return Application::where('user_id', $user->id)
            ->where('status', 'accepted')
            ->with('internship.company')
            ->first();
    }

    /**
     * Get active attendance session.
     */
    public function getActiveSession(User $user, Application $application): ?Attendance
    {
        return Attendance::where('user_id', $user->id)
            ->where('application_id', $application->id)
            ->whereNull('check_out_at')
            ->first();
    }

    /**
     * Get attendance history for a user.
     */
    public function getUserAttendanceHistory(User $user, Application $application, int $perPage = 10): LengthAwarePaginator
    {
        return Attendance::where('user_id', $user->id)
            ->where('application_id', $application->id)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Perform check-in.
     */
    public function checkIn(Application $application, User $user, array $data): Attendance
    {
        return DB::transaction(function () use ($application, $user, $data) {
            if (! $this->isWithinGeofence($data['latitude'], $data['longitude'], $application->internship)) {
                throw new Exception(__('You are outside the company\'s geofence area.'));
            }

            $attendance = Attendance::create([
                'user_id' => $user->id,
                'application_id' => $application->id,
                'check_in_at' => now(),
                'check_in_location' => [
                    'lat' => $data['latitude'],
                    'lng' => $data['longitude'],
                ],
                'status' => 'present',
            ]);

            AuditLogger::log('attendance_check_in', $attendance, null, [
                'lat' => $data['latitude'],
                'lng' => $data['longitude'],
            ], 'User checked in.');

            ActivityLogger::log('check_in', "Checked in for internship: {$application->internship->title}");

            return $attendance;
        });
    }

    /**
     * Perform check-out.
     */
    public function checkOut(Attendance $attendance, User $user, array $data): Attendance
    {
        return DB::transaction(function () use ($attendance, $user, $data) {
            $attendance->update([
                'check_out_at' => now(),
                'check_out_location' => [
                    'lat' => $data['latitude'],
                    'lng' => $data['longitude'],
                ],
                'notes' => $data['notes'] ?? $attendance->notes, // Save daily logbook/notes
            ]);

            $this->clearLiveLocation($user->id);

            AuditLogger::log('attendance_check_out', $attendance, null, [
                'lat' => $data['latitude'],
                'lng' => $data['longitude'],
                'notes' => $data['notes'] ?? null,
            ], 'User checked out with daily logbook.');

            return $attendance;
        });
    }

    /**
     * Request correction for an attendance record.
     */
    public function requestCorrection(Attendance $attendance, User $user, array $data): AttendanceCorrection
    {
        return AttendanceCorrection::create([
            'attendance_id' => $attendance->id,
            'requested_by' => $user->id,
            'new_check_in_at' => $data['new_check_in_at'] ?? null,
            'new_check_out_at' => $data['new_check_out_at'] ?? null,
            'reason' => $data['reason'],
            'status' => 'pending',
        ]);
    }

    /**
     * Get paginated attendances for a specific company with optional filtering.
     */
    public function getCompanyAttendances(Company $company, array $filters = []): LengthAwarePaginator
    {
        $query = Attendance::whereHas('application.internship', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        })->with(['user.detail', 'application.internship']);

        // Apply Date Filter
        $date = $filters['date'] ?? now()->toDateString();
        $query->whereDate('check_in_at', $date);

        // Apply Search Filter
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get live locations for a collection of attendances.
     */
    public function getLiveLocations($attendances): array
    {
        $liveLocations = [];
        foreach ($attendances as $attendance) {
            if (! $attendance->check_out_at) {
                $loc = Cache::get("user_location:{$attendance->user_id}");
                if ($loc) {
                    $liveLocations[$attendance->user_id] = $loc;
                }
            }
        }

        return $liveLocations;
    }

    /**
     * Calculate distance between two points using Haversine formula (in meters).
     */
    public function calculateDistance($lat1, $lng1, $lat2, $lng2): float
    {
        $earthRadius = 6371000; // meters

        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);
        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);

        $dLat = $lat2 - $lat1;
        $dLng = $lng2 - $lng1;

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Check if a location is within a geofence.
     */
    public function isWithinGeofence($lat, $lng, $internship): bool
    {
        // Bypass for WFH
        if ($internship->type === 'WFH') {
            return true;
        }

        $targetLat = $internship->latitude ?? $internship->company->latitude ?? null;
        $targetLng = $internship->longitude ?? $internship->company->longitude ?? null;

        if (! $targetLat || ! $targetLng) {
            return true; // No geofence coordinates set
        }

        $distance = $this->calculateDistance($lat, $lng, $targetLat, $targetLng);

        // Default 100 meters radius if not specified
        $radius = $internship->company->geofence_radius ?? 100;

        return $distance <= $radius;
    }

    /**
     * Update live location in Redis.
     */
    public function updateLiveLocation($userId, $lat, $lng): void
    {
        $activeSession = Attendance::where('user_id', $userId)
            ->whereNull('check_out_at')
            ->exists();

        if ($activeSession) {
            // Store location with 5-minute TTL
            Cache::put("user_location:{$userId}", [
                'lat' => $lat,
                'lng' => $lng,
                'updated_at' => now()->toIso8601String(),
            ], now()->addMinutes(5));
        }
    }

    /**
     * Clear live location from Redis.
     */
    public function clearLiveLocation($userId): void
    {
        Cache::forget("user_location:{$userId}");
    }
}
