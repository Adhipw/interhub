<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use App\Services\AI\AiService;
use App\Services\AI\DTOs\AiMessage;
use App\Services\AI\Enums\AiRole;
use App\Services\AI\Logging\AiFileLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiUserController extends Controller
{
    public function __construct(
        protected AiService $aiService,
        protected AiFileLogger $fileLogger
    ) {}

    public function reviewProfile()
    {
        $user = Auth::user();
        $detail = $user->detail;

        $context = "User Name: {$user->name}\n".
                   'Education: '.($detail?->education ?? 'N/A')."\n".
                   'Skills: '.($detail?->skills ? implode(', ', $detail->skills) : 'N/A')."\n".
                   'Bio: '.($detail?->bio ?? 'N/A');

        $messages = [
            new AiMessage(AiRole::SYSTEM, "You are a Professional Career Coach. Review the user's profile and provide 3 constructive tips to make it more attractive to recruiters. Be encouraging but honest. Do not suggest fake experiences."),
            new AiMessage(AiRole::USER, "Here is my profile data:\n".$context),
        ];

        $response = $this->aiService->chat($messages);

        return response()->json([
            'tips' => $response->content,
            'human_review_required' => true,
        ]);
    }

    public function summarizeCv(Request $request)
    {
        $user = Auth::user();
        $detail = $user->detail;

        $request->validate([
            'cv_text' => 'required|string',
        ]);

        // Log file access if user has a CV path
        if ($detail && $detail->cv_path) {
            $this->fileLogger->logAccess(
                $detail->cv_path,
                'cv',
                'summarize-cv',
                'User requested AI summary of their uploaded CV text.'
            );
        }

        $messages = [
            new AiMessage(AiRole::SYSTEM, 'You are a Resume Expert. Summarize this CV into key skills and experience. Focus on technical proficiency and achievements. Do not hallucinate or add skills not present in the text.'),
            new AiMessage(AiRole::USER, $request->cv_text),
        ];

        $response = $this->aiService->chat($messages);

        return response()->json([
            'summary' => $response->content,
            'human_review_required' => true,
        ]);
    }

    public function recommendInternships()
    {
        $user = Auth::user();
        $detail = $user->detail;

        $internships = Internship::published()->with('company')->limit(10)->get();

        $context = 'My Skills: '.($detail?->skills ? implode(', ', $detail->skills) : 'N/A')."\n";
        $context .= "Available Internships:\n";
        foreach ($internships as $i) {
            $context .= "- {$i->title} at {$i->company->name} (Tags: ".implode(',', $i->tags ?? []).")\n";
        }

        $messages = [
            new AiMessage(AiRole::SYSTEM, "You are an Internship Matchmaker. Based on the user's skills and the available list, recommend the top 3 best fits. Explain why each is a good match."),
            new AiMessage(AiRole::USER, $context),
        ];

        $response = $this->aiService->chat($messages);

        return response()->json([
            'recommendations' => $response->content,
            'human_review_required' => true,
        ]);
    }

    public function draftApplicationLetter(Request $request)
    {
        $request->validate([
            'internship_id' => 'required|exists:internships,id',
            'additional_context' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $detail = $user->detail;
        $internship = Internship::findOrFail($request->internship_id);

        $messages = [
            new AiMessage(AiRole::SYSTEM, "You are a professional writer. Draft a cover letter for the following internship. Use the user's background but leave placeholders [BRACKETS] for specific details they must fill in. Warning: Do not invent experiences for the user."),
            new AiMessage(AiRole::USER, "Internship: {$internship->title} at {$internship->company->name}\nMy Background: ".($detail?->bio ?? 'N/A')."\nContext: {$request->additional_context}"),
        ];

        $response = $this->aiService->chat($messages);

        return response()->json([
            'draft' => $response->content,
            'human_review_required' => true,
        ]);
    }

    public function interviewPrep(Request $request)
    {
        $request->validate([
            'internship_id' => 'required|exists:internships,id',
        ]);

        $internship = Internship::findOrFail($request->internship_id);

        $messages = [
            new AiMessage(AiRole::SYSTEM, "You are an Interview Simulator. Provide 5 common interview questions for this specific position and brief tips on how to answer each. Position: {$internship->title} at {$internship->company->name}. Description: {$internship->description}"),
            new AiMessage(AiRole::USER, 'Prepare me for this interview.'),
        ];

        $response = $this->aiService->chat($messages);

        return response()->json([
            'prep_material' => $response->content,
            'human_review_required' => false,
        ]);
    }
}
