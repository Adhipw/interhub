<?php

namespace App\Http\Controllers\Api;

use App\Models\AuditLog;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApiExportController extends ApiBaseController
{
    /**
     * Export users to CSV.
     */
    public function exportUsers(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users_export_'.now()->format('Y-m-d_His').'.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($handle, ['ID', 'Name', 'Email', 'Role', 'Phone', 'Created At']);

            // Data
            User::with('roles')->chunk(100, function ($users) use ($handle) {
                foreach ($users as $user) {
                    fputcsv($handle, [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->roles->pluck('name')->implode(', '),
                        $user->phone_number,
                        $user->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export audit logs to CSV.
     */
    public function exportAuditLogs(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="audit_logs_'.now()->format('Y-m-d_His').'.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($handle, ['ID', 'User', 'Action', 'Description', 'IP Address', 'Created At']);

            // Data
            AuditLog::with('user')->chunk(100, function ($logs) use ($handle) {
                foreach ($logs as $log) {
                    fputcsv($handle, [
                        $log->id,
                        $log->user->name ?? 'System',
                        $log->action,
                        $log->description,
                        $log->ip_address,
                        $log->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
