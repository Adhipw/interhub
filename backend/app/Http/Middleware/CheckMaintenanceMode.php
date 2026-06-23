<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\FeatureFlag;
use Illuminate\Support\Facades\Cache;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cache the feature flag for 1 minute to avoid DB hits on every single request
        $isMaintenance = Cache::remember('maintenance_mode_enabled', 60, function () {
            return FeatureFlag::where('key', 'maintenance_mode')->value('is_enabled') ?? false;
        });

        if ($isMaintenance) {
            // Allow super_admin to bypass
            if (auth()->check() && auth()->user()->role === 'super_admin') {
                return $next($request);
            }
            
            // Allow logging out so users aren't stuck if they were logged in
            if ($request->is('logout') || $request->is('api/*/logout')) {
                 return $next($request);
            }

            // Abort with 503 Maintenance
            abort(503, 'Sedang Update Sistem');
        }

        return $next($request);
    }
}
