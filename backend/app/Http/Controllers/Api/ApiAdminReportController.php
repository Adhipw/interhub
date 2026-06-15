<?php

namespace App\Http\Controllers\Api;

use App\Models\Application;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ApiAdminReportController extends ApiBaseController
{
    public function index(): JsonResponse
    {
        // 1. Application Stats by Status
        $applicationStats = Application::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        // 2. User Growth (Last 30 days)
        $userGrowth = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // 3. Company Verification Stats
        $companyStats = Company::select('is_verified', DB::raw('count(*) as total'))
            ->groupBy('is_verified')
            ->get();

        return $this->sendResponse([
            'applicationStats' => $applicationStats,
            'userGrowth' => $userGrowth,
            'companyStats' => $companyStats,
        ], 'Report data retrieved successfully');
    }
}
