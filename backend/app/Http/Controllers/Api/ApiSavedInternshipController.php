<?php

namespace App\Http\Controllers\Api;

use App\Models\Internship;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiSavedInternshipController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return $this->sendError('Unauthenticated', [], 401);
        }

        $savedInternships = $user->savedInternships()
            ->with('internship.company')
            ->latest()
            ->get();

        return $this->sendResponse([
            'saved_internships' => $savedInternships,
        ], 'Saved internships retrieved');
    }

    public function toggle(Request $request, Internship $internship): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return $this->sendError('Unauthenticated', [], 401);
        }

        $saved = $user->savedInternships()
            ->where('internship_id', $internship->id)
            ->first();

        if ($saved) {
            $saved->delete();

            return $this->sendResponse([
                'status' => 'unsaved',
                'internship_id' => $internship->id,
            ], 'Internship removed from saved list');
        }

        $saved = $user->savedInternships()->create([
            'internship_id' => $internship->id,
        ]);

        return $this->sendResponse([
            'status' => 'saved',
            'saved_internship' => $saved->load('internship.company'),
        ], 'Internship saved');
    }
}
