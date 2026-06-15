<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                if ($status === 'verified') {
                    $query->where('is_verified', true);
                }
                if ($status === 'pending') {
                    $query->where('is_verified', false);
                }
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Companies/Index', [
            'companies' => $companies,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function verify(Company $company)
    {
        $company->is_verified = true;
        $company->save();

        AuditService::log('admin_company_verified', $company, "Company verified: {$company->name}");

        return redirect()->back()->with('success', 'Perusahaan berhasil diverifikasi.');
    }

    public function unverify(Company $company)
    {
        $company->is_verified = false;
        $company->save();

        AuditService::log('admin_company_unverified', $company, "Company verification removed: {$company->name}");

        return redirect()->back()->with('success', 'Verifikasi perusahaan dicabut.');
    }
}
