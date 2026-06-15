<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class ApiAdminCompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::query();

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->status === 'verified') {
            $query->where('is_verified', true);
        } elseif ($request->status === 'unverified') {
            $query->where('is_verified', false);
        }

        $companies = $query->withCount('internships')->latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $companies,
        ]);
    }

    public function verify(Company $company)
    {
        $company->is_verified = true;
        $company->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Company verified',
            'data' => $company,
        ]);
    }

    public function unverify(Company $company)
    {
        $company->is_verified = false;
        $company->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Company verification revoked',
            'data' => $company,
        ]);
    }
}
