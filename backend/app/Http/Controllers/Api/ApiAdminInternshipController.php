<?php

namespace App\Http\Controllers\Api;

use App\Models\Internship;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiAdminInternshipController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $query = Internship::with('company')->latest();

        if ($request->search) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $internships = $query->paginate(10);

        return $this->sendResponse($internships, 'Internships retrieved successfully');
    }

    public function updateStatus(Request $request, Internship $internship): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:published,flagged,archived,pending',
        ]);

        $internship->update(['status' => $request->status]);

        return $this->sendResponse($internship, 'Internship status updated to '.$request->status);
    }
}
