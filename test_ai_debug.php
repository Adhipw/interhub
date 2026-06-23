<?php
// Test with different prompts to reproduce the production issue
$prompts = ['frontend', 'marketing', 'data analyst', 'magang'];

foreach ($prompts as $prompt) {
    $ch = curl_init('https://internhub.my.id/api/v1/ai/public/finder');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['prompt' => $prompt]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($response, true);
    $matchCount = count($data['matches'] ?? []);
    echo "Prompt: '$prompt' => HTTP $httpCode, matches: $matchCount";
    if (isset($data['message'])) echo ", msg: {$data['message']}";
    if (isset($data['error'])) echo ", error: {$data['error']}";
    echo "\n";
}
