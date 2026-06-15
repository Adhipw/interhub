<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ExternalIntegration;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IntegrationController extends Controller
{
    public function index()
    {
        $integrations = ExternalIntegration::all()->map(function ($integration) {
            // Mask sensitive credentials for security requirement
            $integration->masked_credentials = collect($integration->credentials)->map(function ($value, $key) {
                return '********';
            });

            return $integration;
        });

        return Inertia::render('SuperAdmin/Integrations/Index', [
            'integrations' => $integrations,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'credentials' => 'required|array',
            'settings' => 'nullable|array',
        ]);

        $integration = ExternalIntegration::create($request->all());

        AuditService::log('super_admin_integration_created', $integration, "Integration created: {$integration->name}");

        return redirect()->back()->with('success', 'Integrasi berhasil ditambahkan.');
    }

    public function update(Request $request, ExternalIntegration $integration)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'credentials' => 'nullable|array',
            'settings' => 'nullable|array',
            'is_active' => 'required|boolean',
        ]);

        $data = $request->only(['name', 'settings', 'is_active']);

        // Only update credentials if provided (not masked)
        if ($request->has('credentials') && ! empty($request->credentials)) {
            $data['credentials'] = $request->credentials;
        }

        $integration->update($data);

        AuditService::log('super_admin_integration_updated', $integration, "Integration updated: {$integration->name}");

        return redirect()->back()->with('success', 'Integrasi berhasil diperbarui.');
    }

    public function destroy(ExternalIntegration $integration)
    {
        $name = $integration->name;
        $integration->delete();

        AuditService::log('super_admin_integration_deleted', null, "Integration deleted: {$name}");

        return redirect()->back()->with('success', 'Integrasi berhasil dihapus.');
    }
}
