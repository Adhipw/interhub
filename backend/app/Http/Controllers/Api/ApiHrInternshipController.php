<?php

namespace App\Http\Controllers\Api;

use App\Models\Internship;
use App\Models\User;
use App\Services\InternshipService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiHrInternshipController extends ApiBaseController
{
    protected InternshipService $internshipService;

    public function __construct(InternshipService $internshipService)
    {
        $this->internshipService = $internshipService;
    }

    /**
     * Display a listing of internships for the HR's company.
     */
    public function index(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $company = $user->companies()->first();

        if (! $company) {
            return $this->sendError(__('messages.company_not_found'), [], 404);
        }

        $internships = $this->internshipService->getCompanyInternships($company);

        return $this->sendResponse($internships, __('messages.retrieved_successfully'));
    }

    /**
     * Store a newly created internship.
     */
    public function store(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $company = $user->companies()->first();

        if (! $company) {
            return $this->sendError(__('messages.unauthorized_company'), [], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|array',
            'benefits' => 'nullable|string',
            'type' => 'required|string|in:WFH,Office,Hybrid',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_paid' => 'required|boolean',
            'stipend' => 'nullable|string|max:255',
            'deadline_at' => 'nullable|date|after:today',
            'status' => 'required|string|in:draft,published',
            'tags' => 'nullable|array',
            'industry_id' => 'nullable|exists:industries,id',
        ]);

        $internship = $this->internshipService->createInternship($company, $validated);

        return $this->sendResponse($internship, __('messages.created_successfully'), 201);
    }

    /**
     * Display the specified internship.
     */
    public function show(Internship $internship): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $company = $user->companies()->first();

        if (! $company || $internship->company_id !== $company->id) {
            return $this->sendError(__('messages.unauthorized'), [], 403);
        }

        return $this->sendResponse($internship, __('messages.retrieved_successfully'));
    }

    /**
     * Update the specified internship.
     */
    public function update(Request $request, Internship $internship): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $company = $user->companies()->first();

        if (! $company || $internship->company_id !== $company->id) {
            return $this->sendError(__('messages.unauthorized'), [], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'requirements' => 'nullable|array',
            'benefits' => 'nullable|string',
            'type' => 'sometimes|required|string|in:WFH,Office,Hybrid',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_paid' => 'sometimes|required|boolean',
            'stipend' => 'nullable|string|max:255',
            'deadline_at' => 'nullable|date|after:today',
            'status' => 'sometimes|required|string|in:draft,published',
            'tags' => 'nullable|array',
            'industry_id' => 'nullable|exists:industries,id',
        ]);

        $internship = $this->internshipService->updateInternship($internship, $validated);

        return $this->sendResponse($internship, __('messages.updated_successfully'));
    }

    /**
     * Remove the specified internship.
     */
    public function destroy(Internship $internship): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $company = $user->companies()->first();

        if (! $company || $internship->company_id !== $company->id) {
            return $this->sendError(__('messages.unauthorized'), [], 403);
        }

        $this->internshipService->deleteInternship($internship);

        return $this->sendResponse(null, __('messages.deleted_successfully'));
    }
}
