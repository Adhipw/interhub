<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationScore;
use App\Models\ScreeningRubric;
use App\Services\AI\AiService;
use App\Services\AI\DTOs\AiMessage;
use App\Services\AI\Enums\AiRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScoringController extends Controller
{
    public function __construct(protected AiService $aiService) {}

    public function setRubric(Request $request)
    {
        $request->validate([
            'internship_id' => 'required|exists:internships,id',
            'criteria' => 'required|array',
            'criteria.*.name' => 'required|string',
            'criteria.*.weight' => 'required|integer|min:1|max:100',
            'criteria.*.description' => 'required|string',
        ]);

        $rubric = ScreeningRubric::updateOrCreate(
            ['internship_id' => $request->internship_id],
            ['criteria' => $request->criteria]
        );

        return response()->json([
            'message' => 'Rubrik penilaian berhasil diperbarui.',
            'rubric' => $rubric,
        ]);
    }

    public function calculateAiScore(Application $application)
    {
        if ($application->internship->company_id !== session('current_company_id')) {
            abort(403);
        }

        $rubric = ScreeningRubric::where('internship_id', $application->internship_id)->first();
        if (! $rubric) {
            return response()->json(['error' => 'Rubrik penilaian belum diatur.'], 422);
        }

        $detail = $application->user->detail;

        $context = "Rubric Criteria:\n";
        foreach ($rubric->criteria as $c) {
            $context .= "- {$c['name']} (Weight: {$c['weight']}%): {$c['description']}\n";
        }

        $context .= "\nCandidate Data:\n";
        $context .= 'Skills: '.implode(', ', $detail?->skills ?? [])."\n";
        $context .= "Bio: {$detail?->bio}\n";
        $context .= "Cover Letter: {$application->cover_letter}\n";

        $messages = [
            new AiMessage(AiRole::SYSTEM, "You are a Fair Recruitment AI. Score this candidate (0-100) strictly based on the provided rubric. 
            RULES:
            1. DO NOT use discriminatory factors (gender, age, race, religion).
            2. Identify 'Factors Used' and 'Factors Ignored' (safety guard).
            3. Provide a justification for the score.
            4. Output format MUST be JSON: { \"score\": 85, \"justification\": \"...\", \"factors_used\": [...], \"factors_ignored\": [...] }"),
            new AiMessage(AiRole::USER, $context),
        ];

        $response = $this->aiService->chat($messages);
        $data = json_decode($response->content, true);

        if (! $data) {
            // Fallback if AI didn't return valid JSON
            return response()->json(['error' => 'Gagal memproses penilaian AI.'], 500);
        }

        $score = ApplicationScore::updateOrCreate(
            ['application_id' => $application->id],
            [
                'score' => $data['score'],
                'factors' => $data,
                'is_ai_suggested' => true,
                'human_reviewed' => false,
            ]
        );

        return response()->json([
            'message' => 'Skor AI berhasil dihasilkan. Perlu review manual.',
            'score' => $score,
        ]);
    }

    public function reviewScore(Request $request, ApplicationScore $score)
    {
        $request->validate([
            'manual_score' => 'required|numeric|min:0|max:100',
        ]);

        if ($score->application->internship->company_id !== session('current_company_id')) {
            abort(403);
        }

        $score->update([
            'score' => $request->manual_score,
            'human_reviewed' => true,
            'reviewer_id' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Skor telah dikonfirmasi oleh HR.',
            'score' => $score,
        ]);
    }
}
