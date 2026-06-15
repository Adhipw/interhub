<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\FeatureFlag;
use App\Models\SystemSetting;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        return Inertia::render('SuperAdmin/Settings/Index', [
            'settings' => SystemSetting::all(),
            'featureFlags' => FeatureFlag::all(),
        ]);
    }

    public function updateSetting(Request $request, SystemSetting $setting)
    {
        $request->validate([
            'value' => 'required',
        ]);

        $oldValue = $setting->is_sensitive ? '[REDACTED]' : $setting->value;
        $newValue = $setting->is_sensitive ? '[REDACTED]' : $request->value;

        $setting->value = $request->value;
        $setting->save();

        AuditService::log('super_admin_setting_update', $setting, "Setting {$setting->key} updated from {$oldValue} to {$newValue}");

        return redirect()->back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }

    public function toggleFeature(Request $request, FeatureFlag $flag)
    {
        $flag->is_enabled = ! $flag->is_enabled;
        $flag->save();

        $status = $flag->is_enabled ? 'Enabled' : 'Disabled';
        AuditService::log('super_admin_feature_toggle', $flag, "Feature flag {$flag->key} changed to {$status}");

        return redirect()->back()->with('success', "Fitur {$flag->name} berhasil diubah statusnya.");
    }
}
