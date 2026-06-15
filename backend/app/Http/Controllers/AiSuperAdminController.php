<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\IntegrationLog;
use App\Models\SecurityEvent;
use App\Models\User;
use App\Services\AI\AiService;
use App\Services\AI\DTOs\AiMessage;
use App\Services\AI\Enums\AiRole;
use App\Services\AI\Safety\SafetyGuard;
use Illuminate\Http\Request;

class AiSuperAdminController extends Controller
{
    protected $aiService;

    protected $safetyGuard;

    public function __construct(AiService $aiService, SafetyGuard $safetyGuard)
    {
        $this->aiService = $aiService;
        $this->safetyGuard = $safetyGuard;
    }

    public function getAuditInsight(Request $request)
    {
        $logs = AuditLog::latest()->limit(50)->get(['event_type', 'description', 'created_at']);

        $data = $logs->map(fn ($l) => "[{$l->created_at}] {$l->event_type}: {$l->description}")->join("\n");

        $messages = [
            new AiMessage(AiRole::SYSTEM, 'You are a System Auditor. Analyze the recent audit logs and provide a summary of significant activity. Identify any unusual patterns or bulk changes that might need manual verification.'),
            new AiMessage(AiRole::USER, "Recent Audit Logs:\n".$data),
        ];

        $response = $this->aiService->chat($messages);

        return response()->json([
            'audit_insight' => $response->content,
            'human_review_required' => true,
        ]);
    }

    public function getSecurityRiskSummary(Request $request)
    {
        $events = SecurityEvent::latest()->limit(50)->get(['event_type', 'description', 'created_at', 'ip_address']);

        $data = $events->map(fn ($e) => "[{$e->created_at}] ({$e->ip_address}) {$e->event_type}: {$e->description}")->join("\n");

        $messages = [
            new AiMessage(AiRole::SYSTEM, 'You are a Cyber Security Analyst. Review these security events and provide a risk summary. Highlight frequent login failures, safety violations, or suspicious IP activity.'),
            new AiMessage(AiRole::USER, "Recent Security Events:\n".$data),
        ];

        $response = $this->aiService->chat($messages);

        return response()->json([
            'security_risk_summary' => $response->content,
            'human_review_required' => true,
        ]);
    }

    public function diagnoseIntegration(Request $request)
    {
        $request->validate([
            'integration_id' => 'required|exists:external_integrations,id',
        ]);

        $logs = IntegrationLog::where('external_integration_id', $request->integration_id)
            ->latest()
            ->limit(10)
            ->get(['status', 'message', 'error_details', 'created_at']);

        // REDACTION: Ensure error details don't leak secrets
        $data = $logs->map(function ($l) {
            $err = json_encode($l->error_details);
            $redactedErr = $this->safetyGuard->redactSecrets($err);

            return "[{$l->created_at}] Status: {$l->status}, Message: {$l->message}, Error: {$redactedErr}";
        })->join("\n");

        $messages = [
            new AiMessage(AiRole::SYSTEM, 'You are an Integration Diagnostics Specialist. Analyze the provided logs and suggest why the integration might be failing. Provide troubleshooting steps.'),
            new AiMessage(AiRole::USER, "Integration Logs:\n".$data),
        ];

        $response = $this->aiService->chat($messages);

        return response()->json([
            'diagnostics' => $response->content,
            'human_review_required' => true,
        ]);
    }

    public function getSystemHealth(Request $request)
    {
        // Dummy health stats for demo
        $stats = [
            'database' => 'connected',
            'redis' => 'connected',
            'storage_usage' => '45%',
            'pending_applications' => Internship::published()->count(),
            'total_users' => User::count(),
        ];

        $messages = [
            new AiMessage(AiRole::SYSTEM, 'You are a System Health Assistant. Provide a brief health report based on these stats. If everything looks normal, keep it concise.'),
            new AiMessage(AiRole::USER, 'System Stats: '.json_encode($stats)),
        ];

        $response = $this->aiService->chat($messages);

        return response()->json([
            'system_health' => $response->content,
            'human_review_required' => true,
        ]);
    }
}
