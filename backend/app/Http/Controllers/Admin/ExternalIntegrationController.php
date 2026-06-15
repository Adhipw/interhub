<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SyncExternalDataJob;
use App\Models\ExternalIntegration;
use Illuminate\Http\Request;

class ExternalIntegrationController extends Controller
{
    public function index()
    {
        // Admin cannot see secret integrations
        return response()->json(ExternalIntegration::where('is_secret', false)->with('logs')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'provider' => 'required|in:maganghub,csv,manual,webhook',
            'credentials' => 'nullable|array',
            'settings' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $integration = ExternalIntegration::create($validated);

        return response()->json([
            'message' => 'Integration created successfully.',
            'data' => $integration,
        ], 201);
    }

    public function show(ExternalIntegration $externalIntegration)
    {
        if ($externalIntegration->is_secret) {
            abort(403, 'Anda tidak memiliki akses ke integrasi rahasia.');
        }

        return response()->json($externalIntegration->load('logs'));
    }

    public function update(Request $request, ExternalIntegration $externalIntegration)
    {
        if ($externalIntegration->is_secret) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah integrasi rahasia.');
        }

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'provider' => 'sometimes|in:maganghub,csv,manual,webhook',
            'credentials' => 'nullable|array',
            'settings' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $externalIntegration->update($validated);

        return response()->json([
            'message' => 'Integration updated successfully.',
            'data' => $externalIntegration,
        ]);
    }

    public function destroy(ExternalIntegration $externalIntegration)
    {
        if ($externalIntegration->is_secret) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus integrasi rahasia.');
        }

        $externalIntegration->delete();

        return response()->json(['message' => 'Integration deleted successfully.']);
    }

    public function sync(ExternalIntegration $externalIntegration)
    {
        if ($externalIntegration->is_secret) {
            abort(403, 'Anda tidak dapat melakukan sinkronisasi pada integrasi rahasia.');
        }

        SyncExternalDataJob::dispatch($externalIntegration);

        return response()->json(['message' => 'Sync job dispatched.']);
    }
}
