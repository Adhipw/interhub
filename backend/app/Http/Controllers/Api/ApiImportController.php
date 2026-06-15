<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Services\InternshipImportService;
use App\Services\UserImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApiImportController extends ApiBaseController
{
    protected $userImportService;

    protected $internshipImportService;

    public function __construct(
        UserImportService $userImportService,
        InternshipImportService $internshipImportService
    ) {
        $this->userImportService = $userImportService;
        $this->internshipImportService = $internshipImportService;
    }

    /**
     * Import users (Super Admin only).
     */
    public function importUsers(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $result = $this->userImportService->importFromCsv($file->getRealPath());

        if (! $result['success']) {
            return $this->sendError($result['message']);
        }

        return $this->sendResponse($result['summary'], 'Users imported successfully');
    }

    /**
     * Import internships (HR/Company Admin).
     */
    public function importInternships(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $company = $user ? $user->companies()->first() : null;

        if (! $company) {
            return $this->sendError('You are not associated with any company.', [], 403);
        }

        $file = $request->file('file');
        $result = $this->internshipImportService->importFromCsv($file->getRealPath(), $company->id);

        if (! $result['success']) {
            return $this->sendError($result['message']);
        }

        return $this->sendResponse($result['summary'], 'Internships imported successfully');
    }

    /**
     * Download CSV template for imports.
     */
    public function downloadTemplate(string $type): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"template_{$type}.csv\"",
        ];

        $columns = $type === 'users'
            ? ['name', 'email', 'role', 'phone_number']
            : ['title', 'description', 'type', 'location', 'deadline_at', 'requirements', 'is_paid'];

        return response()->stream(function () use ($columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);
            fclose($handle);
        }, 200, $headers);
    }
}
