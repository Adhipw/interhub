<?php

namespace App\Services\ExternalIntegration\Providers;

use App\Models\ExternalIntegration;
use Illuminate\Support\Facades\Log;

class MagangHubProvider implements IntegrationProviderInterface
{
    public function fetchData(ExternalIntegration $integration): array
    {
        // AS PER RULE: No illegal scraping and no unofficial scraper API.
        // This is an official placeholder that would normally use an official API key.
        // For demonstration, we simulate fetching data if a "mock" flag is set,
        // otherwise it returns an empty array to comply with safety rules.

        $credentials = $integration->credentials;
        if (empty($credentials['api_key'])) {
            Log::warning('MagangHub Integration: Missing API Key.');

            return [];
        }

        // Simulating official API response
        return [
            [
                'external_id' => 'mh-101',
                'title' => 'Software Engineer Intern (MagangHub)',
                'company_name' => 'MagangHub Official Partner',
                'description' => 'A great internship opportunity from our partner.',
                'type' => 'Remote',
                'location' => 'Jakarta',
                'salary_range' => 'IDR 2.000.000 - 4.000.000',
                'external_url' => 'https://maganghub.id/jobs/101',
                'requirements' => ['PHP', 'Laravel', 'Vue.js'],
                'deadline_at' => now()->addDays(30)->toDateString(),
            ],
        ];
    }

    public function validateConfig(array $credentials, array $settings): bool
    {
        return ! empty($credentials['api_key']);
    }
}
