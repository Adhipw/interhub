<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Internship;
use App\Services\AI\AiService;
use App\Services\AI\DTOs\AiMessage;
use App\Services\AI\Enums\AiRole;
use Illuminate\Http\Request;

class AiHrController extends Controller
{
    public function __construct(
        protected readonly AiService $aiService
    ) {}

    public function generateJobDescription(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'requirements' => 'nullable|string',
            'additional_context' => 'nullable|string',
        ]);

        $messages = [
            new AiMessage(AiRole::SYSTEM, 'You are a professional HR recruiter. Help write a compelling job description for an internship. Focus on learning opportunities, requirements, and culture. No hallucinations.'),
            new AiMessage(AiRole::USER, "Title: {$request->title}\nInitial Requirements: ".($request->requirements ?? 'None')."\nContext: ".($request->additional_context ?? 'None')),
        ];

        $response = $this->aiService->chat($messages);

        return response()->json([
            'job_description' => $response->content,
            'human_review_required' => true,
        ]);
    }

    public function summarizeCandidate(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:applications,id',
        ]);

        $application = Application::with(['user.detail', 'internship'])->findOrFail($request->application_id);

        // Consent Check
        if (! $application->user->detail?->ai_consent) {
            return response()->json([
                'error' => 'CANDIDATE_CONSENT_REQUIRED',
                'message' => 'Kandidat belum memberikan persetujuan untuk pemrosesan data oleh AI.',
            ], 403);
        }

        // Scope Check
        if ($application->internship->company_id !== session('current_company_id')) {
            abort(403);
        }

        $user = $application->user;
        $context = "Candidate: {$user->name}\nSkills: ".implode(', ', $user->detail?->skills ?? [])."\nBio: {$user->detail?->bio}\nApplied for: {$application->internship->title}";

        $messages = [
            new AiMessage(AiRole::SYSTEM, "You are an HR Assistant. Summarize this candidate's application in 3-4 bullet points focusing on fit for the role."),
            new AiMessage(AiRole::USER, $context),
        ];

        $response = $this->aiService->chat($messages);

        return response()->json([
            'summary' => $response->content,
            'human_review_required' => true,
        ]);
    }

    public function screenCandidate(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:applications,id',
        ]);

        $application = Application::with(['user.detail', 'internship'])->findOrFail($request->application_id);

        // Consent Check
        if (! $application->user->detail?->ai_consent) {
            abort(403, 'Candidate consent required for AI processing.');
        }

        if ($application->internship->company_id !== session('current_company_id')) {
            abort(403);
        }

        $internship = $application->internship;
        $user = $application->user;

        $context = "Job Title: {$internship->title}\nJob Requirements: ".json_encode($internship->requirements)."\n\n";
        $context .= 'Candidate Skills: '.implode(', ', $user->detail?->skills ?? [])."\n";
        $context .= "Candidate Bio: {$user->detail?->bio}";

        $messages = [
            new AiMessage(AiRole::SYSTEM, 'You are a Screening AI. Evaluate how well the candidate matches the job requirements. Provide a score (1-10) and reasoning. WARNING: Do NOT make a final decision. Decisions must be made by humans. Do not use discriminatory factors (gender, age, race).'),
            new AiMessage(AiRole::USER, $context),
        ];

        $response = $this->aiService->chat($messages);

        return response()->json([
            'screening_result' => $response->content,
            'human_review_required' => true,
        ]);
    }

    public function generateInterviewQuestions(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:applications,id',
        ]);

        $application = Application::with(['user.detail', 'internship'])->findOrFail($request->application_id);

        // Consent Check
        if (! $application->user->detail?->ai_consent) {
            abort(403, 'Candidate consent required.');
        }

        if ($application->internship->company_id !== session('current_company_id')) {
            abort(403);
        }

        $context = "Position: {$application->internship->title}\nCandidate: {$application->user->name}\nSkills: ".implode(', ', $application->user->detail?->skills ?? []);

        $messages = [
            new AiMessage(AiRole::SYSTEM, "Generate 5 customized interview questions for this candidate based on their skills and the internship position. Include 'look-for' tips for the interviewer."),
            new AiMessage(AiRole::USER, $context),
        ];

        $response = $this->aiService->chat($messages);

        return response()->json([
            'questions' => $response->content,
            'human_review_required' => true,
        ]);
    }

    public function getPipelineInsight(Request $request)
    {
        $request->validate([
            'internship_id' => 'required|exists:internships,id',
        ]);

        $internship = Internship::withCount(['applications'])->findOrFail($request->internship_id);

        if ($internship->company_id !== session('current_company_id')) {
            abort(403);
        }

        $stats = Application::where('internship_id', $internship->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get();

        $context = "Internship: {$internship->title}\nStats: ".$stats->toJson();

        $messages = [
            new AiMessage(AiRole::SYSTEM, 'Analyze this recruitment pipeline. Provide a brief insight into current progress and if any bottlenecks exist based on the status distribution.'),
            new AiMessage(AiRole::USER, $context),
        ];

        $response = $this->aiService->chat($messages);

        return response()->json([
            'insight' => $response->content,
            'human_review_required' => true,
        ]);
    }
}
