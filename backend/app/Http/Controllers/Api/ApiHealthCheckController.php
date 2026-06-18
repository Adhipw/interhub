<?php

namespace App\Http\Controllers\Api;

use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ApiHealthCheckController extends ApiBaseController
{
    /**
     * Get system health status.
     */
    public function index(): JsonResponse
    {
        $health = [
            'status' => 'ok',
            'timestamp' => now()->toDateTimeString(),
            'services' => [
                'database' => $this->checkDatabase(),
                'storage' => $this->checkStorage(),
                'cache' => $this->checkCache(),
            ],
            'stats' => $this->checkStats(),
        ];

        return $this->sendResponse($health, 'System health check completed');
    }

    private function checkDatabase(): string
    {
        try {
            DB::connection()->getPdo();

            return 'connected';
        } catch (\Exception $e) {
            return 'error: '.$e->getMessage();
        }
    }

    private function checkStorage(): string
    {
        return Storage::exists('private') || Storage::makeDirectory('private') ? 'writable' : 'error';
    }

    private function checkCache(): string
    {
        try {
            Cache::put('health_check', true, 5);

            return Cache::get('health_check') ? 'connected' : 'error';
        } catch (\Exception $e) {
            return 'error: '.$e->getMessage();
        }
    }

    /**
     * @return array<string, int|string>
     */
    private function checkStats(): array
    {
        try {
            return [
                'log_count' => AuditLog::count(),
                'active_sessions' => DB::table('personal_access_tokens')->count(),
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }
}
