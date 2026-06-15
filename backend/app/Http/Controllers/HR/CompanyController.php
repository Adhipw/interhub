<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\HR\StoreCompanyRequest;
use App\Models\Company;
use App\Models\CompanyMember;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function select()
    {
        $companies = Auth::user()->companies()
            ->wherePivot('is_active', true)
            ->get();

        return Inertia::render('HR/Companies/Select', [
            'companies' => $companies,
        ]);
    }

    public function switch(Company $company)
    {
        $membership = Auth::user()->companyMemberships()
            ->where('company_id', $company->id)
            ->where('is_active', true)
            ->firstOrFail();

        session(['current_company_id' => $company->id]);

        return redirect()->route('hr.dashboard')->with('status', "Berhasil beralih ke {$company->name}");
    }

    public function create()
    {
        return Inertia::render('HR/Companies/Create');
    }

    public function store(StoreCompanyRequest $request)
    {
        $company = Company::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name).'-'.Str::random(5),
            'website' => $request->website,
            'location' => $request->location,
            'description' => $request->description,
        ]);

        CompanyMember::create([
            'company_id' => $company->id,
            'user_id' => Auth::id(),
            'role' => 'owner',
            'is_active' => true,
        ]);

        session(['current_company_id' => $company->id]);

        // Auto-assign HR role to the creator
        Auth::user()->assignRole('hr');

        AuditService::log('company_registered', $company);

        return redirect()->route('hr.dashboard')->with('status', 'Perusahaan berhasil didaftarkan.');
    }

    public function edit()
    {
        $company = app('current_company');

        return Inertia::render('HR/Companies/Edit', [
            'company' => $company,
        ]);
    }

    public function update(Request $request)
    {
        $company = app('current_company');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'logo_url' => 'nullable|url|max:255',
        ]);

        $company->update($validated);

        AuditService::log('company_updated', $company, null, $validated);

        return back()->with('status', 'Profil perusahaan berhasil diperbarui.');
    }
}
