<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Log an audit event.
     *
     * @param  array|string|null  $oldValues  Or description if string
     */
    public static function log(string $action, mixed $model = null, mixed $oldValues = null, ?array $newValues = null)
    {
        $description = null;
        if (is_string($oldValues)) {
            $description = $oldValues;
            $oldValues = null;
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => $model ? get_class($model) : null,
            'auditable_id' => $model ? $model->id : null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'description' => $description,
            // 'region' => LocationService::getRegion(Request::ip()),
        ]);
    }
}
