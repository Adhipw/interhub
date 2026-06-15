<?php

namespace App\Http\Controllers;

use App\Http\Requests\Nearby\SearchRequest;
use App\Models\Internship;
use App\Services\AttendanceService; // For Haversine
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class NearbyController extends Controller
{
    /**
     * Public nearby search with privacy protection.
     */
    public function publicSearch(SearchRequest $request)
    {
        $lat = (float) $request->lat;
        $lng = (float) $request->lng;
        $radius = (int) ($request->radius ?? 10);

        // Rate Limiting
        $key = 'nearby_search:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json(['error' => 'Terlalu banyak permintaan. Silakan coba lagi nanti.'], 429);
        }
        RateLimiter::hit($key, 60);

        // Privacy Log (Rounding coordinates to ~1.1km precision)
        $roundedLat = round($lat, 2);
        $roundedLng = round($lng, 2);

        Log::channel('nearby')->info('Public nearby search', [
            'lat_approx' => $roundedLat,
            'lng_approx' => $roundedLng,
            'radius' => $radius,
            'ip' => $request->ip(),
        ]);

        // Optimization: Bounding Box
        $latDelta = $radius / 111.32;
        $lngDelta = $radius / (111.32 * cos(deg2rad($lat)));

        $minLat = $lat - $latDelta;
        $maxLat = $lat + $latDelta;
        $minLng = $lng - $lngDelta;
        $maxLng = $lng + $lngDelta;

        $internships = Internship::with('company')
            ->where('status', 'published')
            ->whereHas('company', function ($q) use ($minLat, $maxLat, $minLng, $maxLng) {
                $q->whereBetween('latitude', [$minLat, $maxLat])
                    ->whereBetween('longitude', [$minLng, $maxLng]);
            })
            ->get();

        // Calculate exact distance and sort
        $results = $internships->map(function ($internship) use ($lat, $lng) {
            $dist = AttendanceService::calculateDistance(
                $lat, $lng,
                $internship->company->latitude,
                $internship->company->longitude
            ) / 1000; // to km

            $internship->distance = round($dist, 2);

            return $internship;
        })->filter(function ($item) use ($radius) {
            return $item->distance <= $radius;
        })->sortBy('distance')->values();

        return response()->json($results);
    }
}
