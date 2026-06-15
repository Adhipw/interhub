<?php

namespace App\Http\Controllers;

use App\Models\AiFileAccessLog;
use App\Models\Company;
use App\Models\Internship;
use App\Models\Location;
use App\Models\User;
use App\Models\UserDetail;
use App\Services\AI\AiService;
use App\Services\AI\DTOs\AiMessage;
use App\Services\AI\Enums\AiRole;
use Illuminate\Http\Request;

class AiAdminController extends Controller
{
    protected $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function moderateContent(Request $request)
    {
        $request->validate([
            'type' => 'required|in:internship,company',
            'id' => 'required|integer',
        ]);

        if ($request->type === 'internship') {
            $content = Internship::findOrFail($request->id);
            $data = "Title: {$content->title}\nDescription: {$content->description}\nRequirements: ".json_encode($content->requirements);
        } else {
            $content = Company::findOrFail($request->id);
            $data = "Name: {$content->name}\nDescription: {$content->description}\nWebsite: {$content->website}";
        }

        $messages = [
            new AiMessage(AiRole::SYSTEM, 'You are a Content Moderation Assistant. Analyze the content for potential violations: hate speech, illegal activities, scams, or inappropriate language. Provide a risk level (Low, Medium, High) and reasoning.'),
            new AiMessage(AiRole::USER, $data),
        ];

        $response = $this->aiService->chat($messages);

        return response()->json([
            'moderation_result' => $response->content,
            'human_review_required' => true,
        ]);
    }

    public function summarizeReport(Request $request)
    {
        $request->validate([
            'report_content' => 'required|string|max:2000',
        ]);

        $messages = [
            new AiMessage(AiRole::SYSTEM, 'Summarize this user report or complaint in 2-3 sentences. Identify the core issue and suggest a potential resolution action for the admin.'),
            new AiMessage(AiRole::USER, $request->report_content),
        ];

        $response = $this->aiService->chat($messages);

        return response()->json([
            'summary' => $response->content,
            'human_review_required' => true,
        ]);
    }

    public function suggestMasterData(Request $request)
    {
        $locations = Location::limit(20)->pluck('name')->join(', ');

        $messages = [
            new AiMessage(AiRole::SYSTEM, 'You are a Data Management Assistant. Based on existing locations, suggest 5 new potential target cities or regions for internship expansion in Indonesia. Focus on industrial or tech hubs.'),
            new AiMessage(AiRole::USER, 'Existing Locations: '.$locations),
        ];

        $response = $this->aiService->chat($messages);

        return response()->json([
            'suggestions' => $response->content,
            'human_review_required' => true,
        ]);
    }

    public function getPrivacyComplianceReport()
    {
        $totalUsers = User::count();
        $usersWithConsent = UserDetail::where('ai_consent', true)->count();
        $fileAccessLogs = AiFileAccessLog::count();

        $context = "Total Users: {$totalUsers}\nUsers with AI Consent: {$usersWithConsent}\nTotal AI File Accesses: {$fileAccessLogs}";

        $messages = [
            new AiMessage(AiRole::SYSTEM, 'You are a Privacy Compliance Auditor. Analyze these statistics and provide a brief report on AI privacy readiness. Highlight the consent percentage and if any areas need improvement.'),
            new AiMessage(AiRole::USER, $context),
        ];

        $response = $this->aiService->chat($messages);

        return response()->json([
            'report' => $response->content,
            'stats' => [
                'total_users' => $totalUsers,
                'consent_count' => $usersWithConsent,
                'consent_percentage' => $totalUsers > 0 ? ($usersWithConsent / $totalUsers) * 100 : 0,
                'file_access_logs' => $fileAccessLogs,
            ],
        ]);
    }
}
