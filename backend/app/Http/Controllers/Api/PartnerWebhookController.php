<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ExternalIntegration;
use App\Models\Internship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PartnerWebhookController extends Controller
{
    /**
     * Handle incoming partner webhooks.
     *
     * @param  string  $uuid  Unique ID for the integration config
     */
    public function handle(Request $request, $uuid)
    {
        // For simplicity, we assume 'uuid' is part of the name or we find by id
        // In production, we'd use a dedicated 'uuid' or 'token' column.
        // Here we use the ID for the demonstration.
        $integration = ExternalIntegration::where('id', $uuid)
            ->where('provider', 'webhook')
            ->where('is_active', true)
            ->first();

        if (! $integration) {
            return response()->json(['error' => 'Integration not found or inactive.'], 404);
        }

        // Validate Secret
        $secret = $integration->credentials['webhook_secret'] ?? null;
        $providedSecret = $request->header('X-Partner-Secret');

        if ($secret && $secret !== $providedSecret) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $items = $request->all();
        if (! is_array($items)) {
            return response()->json(['error' => 'Invalid data format.'], 400);
        }

        // Standardize: if single object, wrap in array
        if (isset($items['title'])) {
            $items = [$items];
        }

        $processed = 0;
        foreach ($items as $item) {
            try {
                $this->importItem($item, $integration);
                $processed++;
            } catch (\Exception $e) {
                Log::error('Webhook Import Error: '.$e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Webhook received.',
            'processed' => $processed,
        ]);
    }

    protected function importItem(array $item, ExternalIntegration $integration)
    {
        // Duplicate detection logic (similar to SyncService)
        $exists = Internship::where('external_source', 'webhook')
            ->where('external_id', $item['external_id'] ?? null)
            ->exists();

        if ($exists) {
            return;
        }

        DB::transaction(function () use ($item) {
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
                'description' => $item['description'] ?? '',
                'requirements' => $item['requirements'] ?? [],
                'type' => $item['type'] ?? 'Office',
                'location' => $item['location'] ?? '',
                'is_paid' => ! empty($item['salary_range']),
                'stipend' => $item['salary_range'] ?? null,
                'deadline_at' => $item['deadline_at'] ?? null,
                'status' => 'pending_review',
                'is_external' => true,
                'external_source' => 'webhook',
                'external_id' => $item['external_id'] ?? null,
                'external_url' => $item['external_url'] ?? null,
                'external_metadata' => $item,
            ]);
        });
    }
}
