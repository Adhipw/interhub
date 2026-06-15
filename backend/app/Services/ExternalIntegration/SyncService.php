<?php

namespace App\Services\ExternalIntegration;

use App\Models\Company;
use App\Models\ExternalIntegration;
use App\Models\IntegrationLog;
use App\Models\Internship;
use App\Services\ExternalIntegration\Providers\CsvProvider;
use App\Services\ExternalIntegration\Providers\IntegrationProviderInterface;
use App\Services\ExternalIntegration\Providers\MagangHubProvider;
use App\Services\ExternalIntegration\Providers\ManualFeedProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyncService
{
    public function sync(ExternalIntegration $integration)
    {
        $provider = $this->getProvider($integration->provider);
        if (! $provider) {
            $this->log($integration, 'failed', "Provider [{$integration->provider}] not found.");

            return;
        }

        $items = $provider->fetchData($integration);
        $this->processExternalData($integration, $items);

        $integration->update(['last_synced_at' => now()]);
    }

    public function processExternalData(ExternalIntegration $integration, array $items)
    {
        $processed = count($items);
        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($items as $item) {
            try {
                if ($this->isDuplicate($item, $integration->provider)) {
                    $skipped++;

                    continue;
                }

                $this->importItem($item, $integration);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = 'Error importing item ['.($item['external_id'] ?? 'unknown').']: '.$e->getMessage();
            }
        }

        $status = empty($errors) ? 'success' : (count($errors) < $processed ? 'warning' : 'failed');
        $message = "Processed: {$processed}, Imported: {$imported}, Skipped: {$skipped}";

        $this->log($integration, $status, $message, $processed, $imported, $skipped, $errors);
    }

    protected function getProvider(string $providerName): ?IntegrationProviderInterface
    {
        return match ($providerName) {
            'maganghub' => new MagangHubProvider,
            'csv' => new CsvProvider,
            'manual' => new ManualFeedProvider,
            default => null,
        };
    }

    protected function isDuplicate(array $item, string $source): bool
    {
        // 1. Check by external_id and source
        $exists = Internship::where('external_source', $source)
            ->where('external_id', $item['external_id'])
            ->exists();

        if ($exists) {
            return true;
        }

        // 2. Heuristic check: Same title and company name (fuzzy match)
        // We look for a company with same name
        $company = Company::where('name', $item['company_name'])->first();
        if ($company) {
            $exists = Internship::where('company_id', $company->id)
                ->where('title', $item['title'])
                ->exists();
            if ($exists) {
                return true;
            }
        }

        return false;
    }

    protected function importItem(array $item, ExternalIntegration $integration)
    {
        DB::transaction(function () use ($item, $integration) {
            // Find or create external company
            $company = Company::firstOrCreate(
                ['name' => $item['company_name']],
                [
                    'slug' => Str::slug($item['company_name']).'-'.Str::random(5),
                    'is_verified' => false,
                ]
            );

            Internship::create([
                'company_id' => $company->id,
                'title' => $item['title'],
                'slug' => Str::slug($item['title']).'-'.Str::random(8),
                'description' => $item['description'],
                'requirements' => $item['requirements'],
                'type' => $item['type'],
                'location' => $item['location'],
                'is_paid' => ! empty($item['salary_range']),
                'stipend' => $item['salary_range'],
                'deadline_at' => $item['deadline_at'],
                'status' => 'pending_review', // ALL external imports must be pending review
                'is_external' => true,
                'external_source' => $integration->provider,
                'external_id' => $item['external_id'],
                'external_url' => $item['external_url'],
                'external_metadata' => $item,
            ]);
        });
    }

    protected function log(ExternalIntegration $integration, string $status, string $message, int $processed = 0, int $imported = 0, int $skipped = 0, array $errors = [])
    {
        IntegrationLog::create([
            'external_integration_id' => $integration->id,
            'status' => $status,
            'message' => $message,
            'items_processed' => $processed,
            'items_imported' => $imported,
            'items_skipped' => $skipped,
            'error_details' => $errors,
        ]);
    }
}
