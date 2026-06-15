<?php

namespace App\Services\AI\Safety;

use App\Models\SecurityEvent;
use Illuminate\Support\Facades\Auth;

class SafetyGuard
{
    public function validateInput(string $input): void
    {
        $blocked = config('ai.safety.blocked_keywords', [
            'password', 'secret_key', 'api_key', 'private_key',
        ]);

        foreach ($blocked as $keyword) {
            if (stripos($input, $keyword) !== false) {
                $this->logViolation("Blocked keyword '{$keyword}' detected in AI input.");
                throw new \Exception('Input contains blocked sensitive keyword.');
            }
        }
    }

    public function sanitizeOutput(string $output): string
    {
        $output = $this->redactSecrets($output);
        $output = $this->anonymizePII($output);

        $blocked = config('ai.safety.blocked_keywords', []);
        foreach ($blocked as $keyword) {
            $output = str_ireplace($keyword, '[REDACTED]', $output);
        }

        return $output;
    }

    public function redactSecrets(string $text): string
    {
        if (empty($text)) {
            return $text;
        }

        $patterns = [
            '/(api[_-]?key[:=]\s*[\'"]?)([a-zA-Z0-9_\-]{16,})([\'"]?)/i',
            '/(secret[:=]\s*[\'"]?)([a-zA-Z0-9_\-]{16,})([\'"]?)/i',
            '/(bearer\s+)([a-zA-Z0-9_\-\.]{20,})/i',
            '/(password[:=]\s*[\'"]?)([a-zA-Z0-9_\-]{8,})([\'"]?)/i',
        ];

        return preg_replace_callback($patterns, function ($matches) {
            // matches[2] is the secret part
            return str_replace($matches[2], '[REDACTED]', $matches[0]);
        }, $text);
    }

    public function anonymizePII(string $text): string
    {
        if (empty($text)) {
            return $text;
        }

        // Anonymize Emails
        $text = preg_replace('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', '[EMAIL_REDACTED]', $text);

        // Anonymize Phones (Indonesia format)
        $text = preg_replace('/(\+?62|0)[0-9]{9,13}/', '[PHONE_REDACTED]', $text);

        return $text;
    }

    protected function logViolation(string $description): void
    {
        SecurityEvent::create([
            'user_id' => Auth::id(),
            'event_type' => 'AI_SAFETY_VIOLATION',
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * ELITE STANDARDS: Fairness Guard
     * Ensures AI evaluation does not use discriminatory factors.
     */
    public function validateFairness(string $text): void
    {
        $discriminatoryFactors = [
            'gender', 'race', 'religion', 'age', 'disability', 'sexual orientation',
            'jenis kelamin', 'ras', 'agama', 'usia', 'disabilitas', 'orientasi seksual',
        ];

        foreach ($discriminatoryFactors as $factor) {
            if (stripos($text, $factor) !== false) {
                // If it's used in a context that suggests bias
                if (preg_match("/(because of|based on|due to|karena|berdasarkan)\s+{$factor}/i", $text)) {
                    $this->logViolation("Potential discriminatory factor detected in AI evaluation: {$factor}");
                    throw new \Exception('AI evaluation contains potential discriminatory factors. Please review manually.');
                }
            }
        }
    }
}
