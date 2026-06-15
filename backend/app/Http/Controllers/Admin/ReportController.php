<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index()
    {
        // Simple analytics for the report viewer
        $applicationStats = Application::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        $userGrowth = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $companyStats = Company::select('is_verified', DB::raw('count(*) as total'))
            ->groupBy('is_verified')
            ->get();

        return Inertia::render('Admin/Reports/Index', [
            'applicationStats' => $applicationStats,
            'userGrowth' => $userGrowth,
            'companyStats' => $companyStats,
        ]);
    }
}
