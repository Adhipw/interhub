<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->banned_at) {
            $user = Auth::user();
            $message = 'Akun Anda telah diblokir. Alasan: '.$user->banned_reason;

            if ($request->expectsJson()) {
                Auth::logout();

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'errors' => ['email' => [$message]],
                    'request_id' => $request->header('X-Request-ID'),
                ], 403);
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => $message,
            ]);
        }

        return $next($request);
    }
}
