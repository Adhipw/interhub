<?php
// Test with different prompts including edge cases
$baseUrl = 'http://localhost:8082';

$testCases = [
    'Saya mahasiswa Ilmu Komunikasi, suka marketing digital',
    'frontend developer vue react',
    'magang hukum atau legal',
    'saya suka desain grafis',
    'data science python',
    'magang', // general keyword - should still return popular ones
    'asdfghjkl', // completely unrelated - should return popular
];

foreach ($testCases as $prompt) {
    $ch = curl_init($baseUrl . '/api/v1/ai/public/finder');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['prompt' => $prompt]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        echo "Prompt: '$prompt' => CURL ERROR: $error\n";
        continue;
    }

    $data = json_decode($response, true);
    $matchCount = count($data['matches'] ?? []);
    $firstTitle = isset($data['matches'][0]) ? $data['matches'][0]['title'] : 'N/A';
    $firstScore = isset($data['matches'][0]) ? $data['matches'][0]['match_score'] : 'N/A';

    echo "Prompt: '$prompt'\n";
    echo "  => HTTP $httpCode, matches: $matchCount";
    if ($matchCount > 0) echo ", top: '$firstTitle' ($firstScore%)";
    echo "\n";
}
