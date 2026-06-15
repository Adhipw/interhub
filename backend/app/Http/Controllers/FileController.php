<?php

namespace App\Http\Controllers;

use App\Models\UserDetail;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Serve private files (CV, Portfolio).
     * Authorization is enforced via UserDetailPolicy.
     */
    public function show(Request $request, string $type, string $filename)
    {
        $path = "private/{$type}/{$filename}";

        if (! Storage::exists($path)) {
            abort(404);
        }

        // Resolve the UserDetail that owns this file
        $userDetail = UserDetail::where(function ($query) use ($type, $path) {
            if ($type === 'cvs') {
                $query->where('cv_path', $path);
            } elseif ($type === 'portfolios') {
                $query->where('portfolio_path', $path);
            }
        })->first();

        if (! $userDetail) {
            abort(404); // File exists in storage but has no owner record
        }

        // Policy check: UserDetailPolicy@view enforces ownership
        $this->authorize('view', $userDetail);

        AuditService::log('document_accessed', $userDetail, null, ['path' => $path]);

        return Storage::response($path);
    }
}
