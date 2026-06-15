<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Application;
use App\Services\AI\AiService;
use App\Services\AI\DTOs\AiMessage;
use App\Services\AI\Enums\AiRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiMentorController extends Controller
{
    public function __construct(
        protected readonly AiService $aiService
    ) {}

    public function generateTasks(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:applications,id',
            'week_number' => 'nullable|integer',
            'focus_area' => 'nullable|string',
        ]);

        $application = Application::with(['user.detail', 'internship'])->findOrFail($request->application_id);

        // Scope Check: Mentor must be assigned to this mentee
        if ($application->mentor_user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to mentee data.');
        }

        $context = "Internship: {$application->internship->title}\nMentee Skills: ".implode(', ', $application->user->detail?->skills ?? [])."\nWeek: ".($request->week_number ?? 'current')."\nFocus: ".($request->focus_area ?? 'general');

        $messages = [
            new AiMessage(AiRole::SYSTEM, 'You are a Mentor Assistant. Suggest 3-5 relevant and learning-oriented tasks for an intern based on their position and skills. Tasks should be clear and actionable.'),
            new AiMessage(AiRole::USER, $context),
        ];

        $response = $this->aiService->chat($messages);

        return response()->json([
            'tasks' => $response->content,
            'human_review_required' => true,
        ]);
    }

    public function draftFeedback(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:applications,id',
            'task_performance' => 'required|string',
        ]);

        $application = Application::with(['user'])->findOrFail($request->application_id);

        if ($application->mentor_user_id !== Auth::id()) {
            abort(403);
        }

        $context = "Mentee: {$application->user->name}\nPerformance observed: {$request->task_performance}";

        $messages = [
            new AiMessage(AiRole::SYSTEM, 'You are a Mentor Assistant. Draft a constructive and encouraging feedback message for an intern. Focus on growth mindset, specific achievements, and areas for improvement.'),
            new AiMessage(AiRole::USER, $context),
        ];

        $response = $this->aiService->chat($messages);

        return response()->json([
            'feedback_draft' => $response->content,
            'human_review_required' => true,
        ]);
    }

    public function getEvaluationSummary(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:applications,id',
        ]);

        $application = Application::with(['user', 'mentorFeedbacks', 'tasks'])->findOrFail($request->application_id);

        if ($application->mentor_user_id !== Auth::id()) {
            abort(403);
        }

        $feedbacks = $application->mentorFeedbacks->pluck('content')->join("\n- ");
        $tasks_completed = $application->tasks->where('status', 'completed')->count();
        $total_tasks = $application->tasks->count();

        $context = "Mentee: {$application->user->name}\nTasks Completed: {$tasks_completed}/{$total_tasks}\nFeedbacks History:\n- {$feedbacks}";

        $messages = [
            new AiMessage(AiRole::SYSTEM, "You are an Evaluation Assistant. Summarize the intern's progress based on their task completion rate and past feedbacks. Highlight key strengths and overall growth."),
            new AiMessage(AiRole::USER, $context),
        ];

        $response = $this->aiService->chat($messages);

        return response()->json([
            'evaluation_summary' => $response->content,
            'human_review_required' => true,
        ]);
    }
}
