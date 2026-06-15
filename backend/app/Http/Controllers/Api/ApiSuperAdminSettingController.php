<?php

namespace App\Http\Controllers\Api;

use App\Models\FeatureFlag;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiSuperAdminSettingController extends ApiBaseController
{
    /**
     * Get all settings and feature flags.
     */
    public function index(): JsonResponse
    {
        $settings = SystemSetting::all();
        $featureFlags = FeatureFlag::all();

        return $this->sendResponse([
            'settings' => $settings,
            'feature_flags' => $featureFlags,
        ], 'System configuration retrieved successfully');
    }

    /**
     * Update a system setting.
     */
    public function updateSetting(Request $request, SystemSetting $setting): JsonResponse
    {
        $validated = $request->validate([
            'value' => 'required|string',
        ]);

        $setting->update($validated);

        return $this->sendResponse($setting, 'Setting updated successfully');
    }

    /**
     * Toggle a feature flag.
     */
    public function toggleFeature(FeatureFlag $flag): JsonResponse
    {
        $flag->is_enabled = ! $flag->is_enabled;
        $flag->save();

        return $this->sendResponse($flag, 'Feature flag toggled successfully');
    }
}
