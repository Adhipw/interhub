<?php

namespace App\Services\ExternalIntegration\Providers;

use App\Models\ExternalIntegration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ManualFeedProvider implements IntegrationProviderInterface
{
    public function fetchData(ExternalIntegration $integration): array
    {
        $settings = $integration->settings;
        $feedUrl = $settings['feed_url'] ?? null;

        if (! $feedUrl) {
            Log::error('Manual Feed Integration: Feed URL not configured.');

            return [];
        }

        try {
            $response = Http::get($feedUrl);
            if ($response->successful()) {
                $rawItems = $response->json();

                return array_map(function ($item) {
                    return [
                        'external_id' => $item['id'] ?? $item['external_id'] ?? md5(serialize($item)),
                        'title' => $item['title'] ?? 'Untitled Internship',
                        'company_name' => $item['company_name'] ?? 'Unknown Company',
                        'description' => $item['description'] ?? '',
                        'type' => $item['type'] ?? 'Office',
                        'location' => $item['location'] ?? '',
                        'salary_range' => $item['salary_range'] ?? null,
                        'external_url' => $item['external_url'] ?? null,
                        'requirements' => $item['requirements'] ?? [],
                        'deadline_at' => $item['deadline_at'] ?? null,
                    ];
                }, $rawItems);
            }
        } catch (\Exception $e) {
            Log::error('Manual Feed Integration Error: '.$e->getMessage());
        }

        return [];
    }

    public function validateConfig(array $credentials, array $settings): bool
    {
        return ! empty($settings['feed_url']);
    }
}
