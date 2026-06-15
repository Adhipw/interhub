<?php

namespace App\Services\ExternalIntegration\Providers;

use App\Models\ExternalIntegration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CsvProvider implements IntegrationProviderInterface
{
    public function fetchData(ExternalIntegration $integration): array
    {
        $settings = $integration->settings;
        $filePath = $settings['file_path'] ?? null;

        if (! $filePath || ! Storage::exists($filePath)) {
            Log::error("CSV Integration: File not found at {$filePath}");

            return [];
        }

        $data = [];
        $stream = Storage::readStream($filePath);
        if ($stream !== false) {
            $header = fgetcsv($stream, 1000, ',');
            while (($row = fgetcsv($stream, 1000, ',')) !== false) {
                if (count($header) !== count($row)) {
                    continue;
                }
                $item = array_combine($header, $row);

                // Standardize fields
                $data[] = [
                    'external_id' => $item['id'] ?? $item['external_id'] ?? md5(serialize($item)),
                    'title' => $item['title'] ?? 'Untitled Internship',
                    'company_name' => $item['company'] ?? $item['company_name'] ?? 'Unknown Company',
                    'description' => $item['description'] ?? '',
                    'type' => $item['type'] ?? 'Office',
                    'location' => $item['location'] ?? '',
                    'salary_range' => $item['salary'] ?? $item['salary_range'] ?? null,
                    'external_url' => $item['url'] ?? $item['external_url'] ?? null,
                    'requirements' => isset($item['requirements']) ? explode(',', $item['requirements']) : [],
                    'deadline_at' => $item['deadline'] ?? $item['deadline_at'] ?? null,
                ];
            }
            fclose($stream);
        }

        return $data;
    }

    public function validateConfig(array $credentials, array $settings): bool
    {
        return ! empty($settings['file_path']);
    }
}
