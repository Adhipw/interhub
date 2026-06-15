<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CompanyScopeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return $next($request);
        }

        $sessionCompanyId = session('current_company_id');

        // If no company in session, pick the first active membership
        if (! $sessionCompanyId) {
            $membership = $user->companyMemberships()->where('is_active', true)->first();
            if ($membership) {
                session(['current_company_id' => $membership->company_id]);
                $sessionCompanyId = $membership->company_id;
            }
        }

        if ($sessionCompanyId) {
            $membership = $user->companyMemberships()
                ->where('company_id', $sessionCompanyId)
                ->where('is_active', true)
                ->with('company')
                ->first();

            if (! $membership) {
                session()->forget('current_company_id');
                // Avoid infinite redirect if already on selection page
                if (! $request->routeIs('hr.companies.select')) {
                    return redirect()->route('hr.companies.select')->withErrors(['company' => 'Akses perusahaan tidak valid atau telah dinonaktifkan.']);
                }
            } else {
                // Bind current company and membership to the app container
                app()->instance('current_company', $membership->company);
                app()->instance('current_membership', $membership);

                // Share with Inertia
                Inertia::share([
                    'current_company' => $membership->company,
                    'current_role' => $membership->role,
                ]);
            }
        } else {
            // If the route requires a company and none is selected, redirect to create/join
            if (($request->is('hr*') || $request->is('mentor*')) &&
                ! $request->routeIs([
                    'hr.companies.create',
                    'hr.companies.store',
                    'hr.companies.select',
                    'hr.companies.switch',
                    'mentor.companies.select', // Assuming mentor might have similar needs
                    'mentor.companies.switch',
                ])) {
                return redirect()->route('hr.companies.create')->with('info', 'Silakan daftarkan atau pilih perusahaan Anda.');
            }
        }

        return $next($request);
    }
}
