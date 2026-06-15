<?php

namespace App\Http\Controllers\Api;

use App\Models\Application;
use App\Models\MentorEvaluation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApiMentorEvaluationController extends ApiBaseController
{
    /**
     * Display a listing of evaluations created by the mentor.
     */
    public function index(): JsonResponse
    {
        $evaluations = MentorEvaluation::where('mentor_user_id', Auth::id())
            ->with(['application.user', 'application.internship'])
            ->latest()
            ->paginate(15);

        return $this->sendResponse($evaluations, 'Mentor evaluations retrieved successfully');
    }

    /**
     * Store a new evaluation.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'application_id' => 'required|exists:applications,id',
            'title' => 'required|string|max:255',
            'summary' => 'required|string|max:2000',
            'metrics' => 'required|array',
            'metrics.technical_skill' => 'required|integer|min:1|max:5',
            'metrics.communication' => 'required|integer|min:1|max:5',
            'metrics.attitude' => 'required|integer|min:1|max:5',
            'metrics.reliability' => 'required|integer|min:1|max:5',
            'recommendation' => 'nullable|string|max:1000',
            'final_status' => 'required|string|in:completed,failed',
        ]);

        $application = Application::findOrFail($validated['application_id']);

        // Ensure the mentor is authorized to evaluate this application
        if ($application->mentor_user_id !== Auth::id()) {
            return $this->sendError('Unauthorized', [], 403);
        }

        $evaluation = DB::transaction(function () use ($validated, $application) {
            $eval = MentorEvaluation::create([
                'application_id' => $validated['application_id'],
                'mentor_user_id' => Auth::id(),
                'title' => $validated['title'],
                'summary' => $validated['summary'],
                'metrics' => $validated['metrics'],
                'recommendation' => $validated['recommendation'],
                'final_status' => $validated['final_status'],
            ]);

            // Update application status to reflect completion
            $application->update([
                'status' => 'completed',
                'timeline' => array_merge($application->timeline ?? [], [[
                    'status' => 'completed',
                    'label' => 'Magang Selesai',
                    'description' => 'Selamat! Anda telah menyelesaikan program magang ini. Evaluasi akhir telah diterbitkan.',
                    'date' => now()->toDateTimeString(),
                ]]),
            ]);

            return $eval;
        });

        return $this->sendResponse($evaluation, 'Evaluation submitted successfully', 201);
    }

    /**
     * Display the specified evaluation.
     */
    public function show(MentorEvaluation $mentorEvaluation): JsonResponse
    {
        if ($mentorEvaluation->mentor_user_id !== Auth::id()) {
            // Also allow the student to see their own evaluation
            $application = $mentorEvaluation->application;
            if ($application->user_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }
        }

        return $this->sendResponse(
            $mentorEvaluation->load(['application.user', 'application.internship']),
            'Evaluation details retrieved'
        );
    }
}
