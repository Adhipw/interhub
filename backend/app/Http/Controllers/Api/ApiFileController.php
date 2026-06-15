<?php

namespace App\Http\Controllers\Api;

use App\Models\Application;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApiFileController extends ApiBaseController
{
    /**
     * Serve a private file with authorization check.
     */
    public function showPrivate(Request $request): StreamedResponse|JsonResponse
    {
        $path = $request->get('path');

        if (! $path || ! Storage::exists($path)) {
            return $this->sendError('File not found', [], 404);
        }

        /** @var User $user */
        $user = Auth::user();

        // 1. Admin/Super Admin bypass
        if ($user->hasRole(['admin', 'super_admin'])) {
            return Storage::download($path);
        }

        // 2. Check if it's the user's own file
        if ($user->detail && ($user->detail->cv_path === $path || $user->detail->portfolio_path === $path)) {
            return Storage::download($path);
        }

        // 3. Check if HR/Mentor has access via Application
        $hasAccess = Application::where(function ($query) use ($path) {
            $query->where('cv_snapshot', $path)
                ->orWhere('portfolio_snapshot', $path);
        })->where(function ($query) use ($user) {
            // HR of the company
            $query->whereHas('internship', function ($q) use ($user) {
                $q->whereHas('company', function ($sq) use ($user) {
                    $sq->whereHas('users', function ($ssq) use ($user) {
                        $ssq->where('users.id', $user->id);
                    });
                });
            })
            // OR Assigned Mentor
                ->orWhere('mentor_user_id', $user->id);
        })->exists();

        if ($hasAccess) {
            return Storage::download($path);
        }

        return $this->sendError('Unauthorized access to this file', [], 403);
    }
}
