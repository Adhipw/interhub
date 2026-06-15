<?php

namespace App\Http\Controllers\ExternalIntegration;

use App\Http\Controllers\Controller;
use App\Models\ExternalIntegration;
use App\Services\ExternalIntegration\SyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request, string $provider)
    {
        $integration = ExternalIntegration::where('provider', $provider)
            ->where('is_active', true)
            ->first();

        if (! $integration) {
            return response()->json(['message' => 'Integration not found or inactive'], 404);
        }

        // Verify webhook secret (if configured)
        $credentials = $integration->credentials;
        $secret = $credentials['webhook_secret'] ?? null;

        if ($secret && $request->header('X-Hub-Signature') !== $secret) {
            Log::warning("Invalid webhook signature for provider: {$provider}");

            return response()->json(['message' => 'Invalid signature'], 401);
        }

        // We assume the payload is a single item or an array of items
        $payload = $request->all();
        $items = isset($payload['items']) ? $payload['items'] : [$payload];

        // We can't use SyncService->sync() directly because it fetches.
        // We'll add a processPayload method to SyncService or handle it here.
        // For now, let's use a specialized method in SyncService.

        $syncService = app(SyncService::class);
        $syncService->processExternalData($integration, $items);

        return response()->json(['message' => 'Webhook received and processed']);
    }
}
