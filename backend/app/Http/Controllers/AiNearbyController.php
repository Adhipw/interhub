<?php

namespace App\Http\Controllers;

use App\Http\Requests\Nearby\AiRecommendRequest;
use App\Models\Internship;
use App\Services\AI\AiService;
use App\Services\AI\DTOs\AiMessage;
use App\Services\AI\Enums\AiRole;
use App\Services\AttendanceService;
use Illuminate\Support\Facades\Auth;

class AiNearbyController extends Controller
{
    public function __construct(protected AiService $aiService) {}

    public function recommend(AiRecommendRequest $request)
    {
        $user = Auth::user();
        $lat = $request->lat ?? $user->primaryLocation?->latitude;
        $lng = $request->lng ?? $user->primaryLocation?->longitude;

        if (! $lat || ! $lng) {
            return response()->json(['error' => 'Lokasi diperlukan untuk memberikan rekomendasi terdekat.'], 422);
        }

        // Bounding Box search (reuse logic)
        $radius = $request->radius ?? 15;
        $latDelta = $radius / 111.32;
        $lngDelta = $radius / (111.32 * cos(deg2rad($lat)));

        $nearbyInternships = Internship::with('company')
            ->where('status', 'published')
            ->whereHas('company', function ($q) use ($lat, $lng, $latDelta, $lngDelta) {
                $q->whereBetween('latitude', [$lat - $latDelta, $lat + $latDelta])
                    ->whereBetween('longitude', [$lng - $lngDelta, $lng + $lngDelta]);
            })
            ->get()
            ->map(function ($i) use ($lat, $lng) {
                $i->distance = round(AttendanceService::calculateDistance($lat, $lng, $i->company->latitude, $i->company->longitude) / 1000, 2);

                return $i;
            })->filter(fn ($i) => $i->distance <= $radius)->sortBy('distance')->values();

        $context = "Nearby Internships (Radius {$radius}km):\n";
        foreach ($nearbyInternships as $i) {
            $context .= "- {$i->title} at {$i->company->name} ({$i->distance}km away). Type: {$i->type}. Skills: ".implode(',', $i->requirements ?? [])."\n";
        }

        $messages = [
            new AiMessage(AiRole::SYSTEM, 'You are a Local Internship Assistant. Recommend the best internships for the user from the provided list based on their interests. 
            SAFETY RULES:
            1. DO NOT mention precise coordinates.
            2. Focus on distance, work mode, and skills.
            3. If no internships are close enough, suggest adjusting the radius.'),
            new AiMessage(AiRole::USER, "User Query: {$request->prompt}\n\nContext:\n{$context}"),
        ];

        $response = $this->aiService->chat($messages);

        return response()->json([
            'recommendation' => $response->content,
            'nearby_count' => $nearbyInternships->count(),
        ]);
    }
}
