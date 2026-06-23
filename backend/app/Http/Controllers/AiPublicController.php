<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use App\Services\AI\AiService;
use App\Services\AI\DTOs\AiMessage;
use App\Services\AI\Enums\AiRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class AiPublicController extends Controller
{
    public function __construct(protected AiService $aiService) {}

    public function internshipFinder(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:500',
        ]);

        // Rate limiting — applied globally regardless of AI provider
        $rateLimitKey = 'public-finder:' . $request->ip();
        $maxRequests  = 20;
        $executed = RateLimiter::attempt($rateLimitKey, $maxRequests, fn() => true, 3600);
        if (! $executed) {
            return response()->json([
                'error'   => 'RATE_LIMIT_EXCEEDED',
                'message' => 'Batas penggunaan AI Finder tercapai. Silakan coba lagi dalam 1 jam.',
            ], 429);
        }

        // Run safety check on input prompt
        try {
            app(\App\Services\AI\Safety\SafetyGuard::class)->validateInput($request->prompt);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'SAFETY_VIOLATION',
                'message' => 'Input mengandung kata kunci sensitif/terlarang.'
            ], 422);
        }

        // 1. Fetch published active internships
        $internships = Internship::published()->with('company')->get();

        if ($internships->isEmpty()) {
            return response()->json(['matches' => [], 'message' => 'Belum ada lowongan magang aktif saat ini.']);
        }

        // 2. Try Gemini AI first (only if provider is NOT 'fake')
        $aiProvider = config('ai.default', 'fake');
        if ($aiProvider !== 'fake') {
            try {
                $internshipsList = $internships->map(fn($item) => [
                    'id'           => $item->id,
                    'title'        => $item->title,
                    'slug'         => $item->slug,
                    'company'      => $item->company->name ?? 'Unknown Company',
                    'location'     => $item->location,
                    'type'         => $item->type,
                    'requirements' => implode(', ', (array) ($item->requirements ?? [])),
                ])->values()->toArray();

                $internshipsJson = json_encode($internshipsList, JSON_PRETTY_PRINT);

                $systemPrompt = "You are InternHub's AI Internship Matcher. Below is the list of active internships in our database:\n".
                    $internshipsJson."\n\n".
                    "Analyze the student's input (major, interests, skills) and recommend the top 3 matching internships.\n".
                    "For each match, calculate a 'match_score' (integer between 0 and 100) and write an encouraging, brief 'explanation' (1-2 sentences in Indonesian) why it's a great match.\n\n".
                    "You MUST respond in raw JSON format matching this exact schema:\n".
                    "{\n".
                    "  \"matches\": [\n".
                    "    {\n".
                    "      \"id\": 1,\n".
                    "      \"title\": \"Vacancy Title\",\n".
                    "      \"slug\": \"slug-url\",\n".
                    "      \"company\": \"Company Name\",\n".
                    "      \"location\": \"Location\",\n".
                    "      \"type\": \"Type\",\n".
                    "      \"match_score\": 90,\n".
                    "      \"explanation\": \"...\"\n".
                    "    }\n".
                    "  ]\n".
                    "}\n\n".
                    'If no suitable matches are found, still recommend the 3 best available internships with a score of 70+. Do not include markdown formatting (like ```json). Respond only with raw JSON.';

                $messages = [
                    new AiMessage(AiRole::SYSTEM, $systemPrompt),
                    new AiMessage(AiRole::USER, $request->prompt),
                ];

                $response = $this->aiService->chat($messages, [
                    'skip_auth'      => true,
                    'rate_limit_key' => 'public-finder-gemini:'.$request->ip(),
                    'max_requests'   => 1000, // Rate limiting already enforced above at controller level
                ]);

                $content = trim($response->content);
                // Clean code blocks if present
                $content = preg_replace('/^```(?:json)?\s+|\s+```$/s', '', $content);

                $data = json_decode($content, true);
                if ($data && isset($data['matches']) && ! empty($data['matches'])) {
                    return response()->json(['matches' => $data['matches']]);
                }
            } catch (\Exception $e) {
                if ($e->getMessage() === 'AI rate limit exceeded. Please try again later.') {
                    return response()->json(['error' => 'RATE_LIMIT_EXCEEDED', 'message' => $e->getMessage()], 429);
                }
                Log::warning('AiPublicController: Gemini failed, using keyword fallback. Error: '.$e->getMessage());
            }
        }

        // === Smart Local Keyword Matching Fallback Engine ===
        // Triggered when: AI provider is 'fake', Gemini quota exhausted, or response cannot be parsed
        return response()->json([
            'matches' => $this->runKeywordEngine($internships, $request->prompt),
        ]);
    }

    /**
     * Keyword-based smart matching engine.
     * Tokenizes the prompt, matches against title/requirements/description.
     * Always returns at least 1 result (falls back to most recently added internships).
     */
    private function runKeywordEngine($internships, string $prompt): array
    {
        $promptLower = strtolower($prompt);

        // Tokenize prompt into individual words (3+ chars)
        $promptTokens = array_values(array_filter(
            preg_split('/[\s,;]+/', $promptLower),
            fn($w) => strlen($w) >= 3
        ));

        // Keyword synonym map for Indonesian students
        $synonymMap = [
            'frontend'      => ['frontend', 'front end', 'react', 'vue', 'tailwind', 'css', 'html', 'javascript', 'js', 'web design', 'web developer', 'pemrograman web'],
            'backend'       => ['backend', 'back end', 'php', 'laravel', 'golang', 'node', 'nodejs', 'python', 'api', 'database', 'programmer', 'software engineer'],
            'software'      => ['software', 'coder', 'developer', 'programmer', 'coding', 'pemrograman', 'algoritma'],
            'ui_ux'         => ['ui', 'ux', 'design', 'figma', 'prototype', 'graphic', 'adobe', 'illustrator', 'photoshop', 'visual', 'creative', 'desainer'],
            'video'         => ['video', 'editing', 'videografi', 'videography', 'premiere', 'after effect', 'davinci', 'konten', 'content creator', 'youtube', 'tiktok', 'film'],
            'marketing'     => ['marketing', 'pemasaran', 'sales', 'social media', 'copywriter', 'seo', 'ads', 'branding', 'promosi', 'digital marketing', 'campaign'],
            'data'          => ['data', 'analyst', 'analytics', 'machine learning', 'tableau', 'excel', 'statistik', 'business intelligence', 'data science', 'ai', 'ml'],
            'finance'       => ['finance', 'akuntansi', 'accounting', 'keuangan', 'audit', 'pajak', 'tax', 'laporan keuangan', 'finansial'],
            'hrd'           => ['hrd', 'hr', 'human resource', 'rekrutmen', 'recruitment', 'sdm', 'administrasi', 'admin', 'sumber daya manusia'],
            'writing'       => ['writing', 'penulis', 'journalist', 'jurnalis', 'copywriting', 'artikel', 'blog', 'content writing', 'redaksi'],
            'business'      => ['bisnis', 'business', 'manajemen', 'management', 'strategy', 'konsultan', 'konsultansi', 'operasional', 'entrepreneur'],
            'legal'         => ['hukum', 'legal', 'law', 'litigation', 'advokat', 'notaris', 'perdata', 'pidana'],
            'communication' => ['komunikasi', 'communication', 'public relation', 'pr', 'media relation', 'jurnalistik', 'broadcasting'],
        ];

        $scoredMatches = [];

        foreach ($internships as $item) {
            $titleLower  = strtolower($item->title);
            $reqsRaw     = is_array($item->requirements) ? $item->requirements : (json_decode($item->requirements ?? '[]', true) ?? []);
            $reqsLower   = strtolower(strip_tags(implode(' ', $reqsRaw)));
            $descLower   = strtolower(strip_tags($item->description ?? ''));
            $locLower    = strtolower($item->location ?? '');

            $combined = "$titleLower $reqsLower $descLower";
            $score = 0;
            $matchingLabels = [];

            // A. Synonym group matching (bidirectional)
            foreach ($synonymMap as $group => $synonyms) {
                $groupMatchInPrompt      = false;
                $groupMatchInInternship  = false;

                foreach ($synonyms as $syn) {
                    if (str_contains($promptLower, $syn)) {
                        $groupMatchInPrompt = true;
                    }
                    if (str_contains($combined, $syn)) {
                        $groupMatchInInternship = true;
                    }
                }

                if ($groupMatchInPrompt && $groupMatchInInternship) {
                    $score += 30;
                    $matchingLabels[] = str_replace('_', '/', $group);
                } elseif ($groupMatchInPrompt || $groupMatchInInternship) {
                    $score += 5;
                }
            }

            // B. Direct token matching
            foreach ($promptTokens as $token) {
                if (str_contains($titleLower, $token)) {
                    $score += 20;
                    $matchingLabels[] = $token;
                }
                if (str_contains($reqsLower, $token)) {
                    $score += 10;
                }
                if (str_contains($descLower, $token)) {
                    $score += 5;
                }
                if (str_contains($locLower, $token)) {
                    $score += 10;
                }
            }

            if ($score > 0) {
                $finalScore = min(98, 60 + $score);
                $labels = array_unique($matchingLabels);
                $explanationKeywords = array_slice($labels, 0, 3);
                $explanation = empty($explanationKeywords)
                    ? 'Lowongan ini sangat cocok berdasarkan profil dan minat yang Anda sampaikan.'
                    : 'Keahlian Anda di bidang '.implode(', ', $explanationKeywords).' sangat relevan dengan kualifikasi yang dicari oleh '.($item->company->name ?? 'perusahaan').' untuk posisi '.$item->title.'.';

                $scoredMatches[] = [
                    'id'          => $item->id,
                    'title'       => $item->title,
                    'slug'        => $item->slug,
                    'company'     => $item->company->name ?? 'Unknown Company',
                    'location'    => $item->location,
                    'type'        => $item->type,
                    'match_score' => $finalScore,
                    'explanation' => $explanation,
                    '_score'      => $score,
                ];
            }
        }

        // Sort by internal score descending
        usort($scoredMatches, fn($a, $b) => $b['_score'] <=> $a['_score']);
        $scoredMatches = array_slice($scoredMatches, 0, 3);

        // Remove internal score key
        $scoredMatches = array_map(function ($m) {
            unset($m['_score']);
            return $m;
        }, $scoredMatches);

        // If still no matches, return top 3 popular internships
        if (empty($scoredMatches)) {
            foreach ($internships->take(3) as $item) {
                $scoredMatches[] = [
                    'id'          => $item->id,
                    'title'       => $item->title,
                    'slug'        => $item->slug,
                    'company'     => $item->company->name ?? 'Unknown Company',
                    'location'    => $item->location,
                    'type'        => $item->type,
                    'match_score' => 80,
                    'explanation' => 'Posisi '.$item->title.' di '.($item->company->name ?? 'perusahaan').' adalah lowongan magang populer. Coba deskripsikan keahlian atau jurusan Anda lebih spesifik untuk hasil yang lebih personal!',
                ];
            }
        }

        return $scoredMatches;
    }

    public function faq(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:500',
        ]);

        $messages = [
            new AiMessage(AiRole::SYSTEM, "You are InternHub's FAQ Assistant. You answer questions about how InternHub works, application processes, and requirements. If you don't know the answer, refer the user to the support email. Do not invent policies."),
            new AiMessage(AiRole::USER, $request->question),
        ];

        try {
            $response = $this->aiService->chat($messages, [
                'skip_auth'      => true,
                'rate_limit_key' => 'public-faq:'.$request->ip(),
                'max_requests'   => 15,
            ]);

            return response()->json([
                'answer'               => $response->content,
                'human_review_required' => false,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 429);
        }
    }
}
