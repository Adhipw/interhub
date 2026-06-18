<?php

namespace App\Http\Controllers\Api;

use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
                'database_detail' => $this->checkDatabaseDetail(),
                'storage' => $this->checkStorage(),
                'cache' => $this->checkCache(),
            ],
            'stats' => $this->checkStats(),
        ];

        return $this->sendResponse($health, 'System health check completed');
    }

    /**
     * @return array<string, string|null>
     */
    private function checkDatabaseDetail(): array
    {
        $detail = [
            'connection' => config('database.default'),
            'database' => null,
        ];

        if (! filter_var(config('services.health.expose_database_name'), FILTER_VALIDATE_BOOLEAN)) {
            return $detail;
        }

        try {
            $connection = DB::connection();
            $driver = $connection->getDriverName();

            $detail['database'] = match ($driver) {
                'pgsql' => (string) $connection->selectOne('select current_database() as database')->database,
                'mysql', 'mariadb' => (string) $connection->selectOne('select database() as database')->database,
                default => (string) config('database.connections.'.config('database.default').'.database'),
            };
        } catch (\Throwable $e) {
            $detail['database'] = 'error: '.$e->getMessage();
        }

        return $detail;
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
                'migration_count' => Schema::hasTable('migrations') ? DB::table('migrations')->count() : 0,
                'user_count' => Schema::hasTable('users') ? DB::table('users')->count() : 0,
                'company_count' => Schema::hasTable('companies') ? DB::table('companies')->count() : 0,
                'internship_count' => Schema::hasTable('internships') ? DB::table('internships')->count() : 0,
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
