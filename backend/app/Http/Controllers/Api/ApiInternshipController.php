<?php

/* [NEW] [ApiInternshipController.php](file:///c:/Users/ASUS/Downloads/rollback_backups/backend/app/Http/Controllers/Api/ApiInternshipController.php) */

namespace App\Http\Controllers\Api;

use App\Models\Application;
use App\Models\Company;
use App\Models\Internship;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiInternshipController extends ApiBaseController
{
    /**
     * Get data for the welcome/landing page.
     */
    public function welcome(): JsonResponse
    {
        $featuredInternships = Internship::published()
            ->with('company')
            ->latest()
            ->limit(6)
            ->get();

        $featuredCompanies = Company::where('is_verified', true)
            ->latest()
            ->limit(8)
            ->get();

        $stats = [
            'total_internships' => Internship::published()->count(),
            'total_companies' => Company::where('is_verified', true)->count(),
            'total_placements' => Application::where('status', 'accepted')->count(),
            'total_students' => User::where('role', 'user')->count(),
        ];

        return $this->sendResponse([
            'featuredInternships' => $featuredInternships,
            'featuredCompanies' => $featuredCompanies,
            'stats' => $stats,
        ], 'Welcome data retrieved successfully');
    }

    /**
     * Get public stats for auth layouts and marketing.
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_internships' => Internship::published()->count(),
            'total_companies' => Company::where('is_verified', true)->count(),
            'total_placements' => Application::where('status', 'accepted')->count(),
            'total_students' => User::where('role', 'user')->count(),
            'applicants_count' => User::where('role', 'user')->count(), // For AuthLayout
            'companies_count' => Company::where('is_verified', true)->count(), // For AuthLayout
        ];

        return $this->sendResponse($stats, 'Public stats retrieved successfully');
    }

    /**
     * Display a listing of the internships.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Internship::with(['company'])->published();

        // Search by title or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by type (WFH, Office, Hybrid)
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by paid status
        if ($request->has('is_paid')) {
            $query->where('is_paid', filter_var($request->is_paid, FILTER_VALIDATE_BOOLEAN));
        }

        // Nearby Search (Haversine Formula)
        if ($request->has('lat') && $request->has('lng')) {
            $lat = $request->lat;
            $lng = $request->lng;
            $radius = $request->get('radius', 50); // Default 50km

            $query->selectRaw('*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance', [$lat, $lng, $lat])
                ->having('distance', '<=', $radius)
                ->orderBy('distance', 'asc');
        } else {
            $query->latest();
        }

        $internships = $query->paginate(12);

        return $this->sendResponse($internships, 'Internships retrieved successfully');
    }

    /**
     * Display the specified internship.
     */
    public function show(Internship $internship): JsonResponse
    {
        $internship->load(['company']);

        return $this->sendResponse($internship, 'Internship details retrieved successfully');
    }
}
