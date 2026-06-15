<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Internship;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyPublicController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::query()
            ->withCount(['internships' => function ($query) {
                $query->published();
            }])
            ->where('is_verified', true)
            ->latest()
            ->paginate(12);

        return Inertia::render('Companies/Index', [
            'companies' => $companies,
        ]);
    }

    public function show(Company $company)
    {
        return Inertia::render('Companies/Show', [
            'company' => $company->loadCount(['internships' => function ($query) {
                $query->published();
            }]),
            'internships' => Internship::published()
                ->where('company_id', $company->id)
                ->latest()
                ->paginate(6),
        ]);
    }
}
