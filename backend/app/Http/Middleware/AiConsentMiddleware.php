<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AiConsentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If user is not logged in, we check if the request is trying to use AI with personal context
        // But usually AI routes are behind auth.
        if (! $user) {
            return $next($request);
        }

        // Load detail if not loaded
        $detail = $user->detail;

        if (! $detail || ! $detail->ai_consent) {
            return response()->json([
                'error' => 'AI_CONSENT_REQUIRED',
                'message' => 'Persetujuan pemrosesan data oleh AI diperlukan untuk fitur ini.',
                'needs_consent' => true,
            ], 403);
        }

        return $next($request);
    }
}
