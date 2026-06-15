<?php

namespace App\Http\Controllers\Api;

use App\Models\ExternalIntegration;
use App\Models\IntegrationLog;
use App\Services\ExternalIntegration\SyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiExternalIntegrationController extends ApiBaseController
{
    public function index(): JsonResponse
    {
        $integrations = ExternalIntegration::all();

        return $this->sendResponse($integrations, 'Integrations retrieved successfully');
    }

    /**
     * Store a new integration.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'is_active' => 'boolean',
            'settings' => 'nullable|array',
        ]);

        $integration = ExternalIntegration::create($validated);

        return $this->sendResponse($integration, 'Integration created successfully');
    }

    /**
     * Trigger a manual sync for an integration.
     */
    public function sync(ExternalIntegration $integration, SyncService $syncService): JsonResponse
    {
        $syncService->sync($integration);

        $lastLog = IntegrationLog::where('external_integration_id', $integration->id)
            ->latest()
            ->first();

        return $this->sendResponse($lastLog, 'Sync completed successfully');
    }

    /**
     * Update an integration.
     */
    public function update(Request $request, ExternalIntegration $integration): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
            'settings' => 'nullable|array',
        ]);

        $integration->update($validated);

        return $this->sendResponse($integration, 'Integration updated successfully');
    }

    /**
     * Delete an integration.
     */
    public function destroy(ExternalIntegration $integration): JsonResponse
    {
        $integration->delete();

        return $this->sendResponse(null, 'Integration deleted successfully');
    }
}
