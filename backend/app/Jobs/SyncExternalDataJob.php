<?php

namespace App\Jobs;

use App\Models\ExternalIntegration;
use App\Services\ExternalIntegration\SyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncExternalDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $integration;

    /**
     * Create a new job instance.
     */
    public function __construct(?ExternalIntegration $integration = null)
    {
        $this->integration = $integration;
    }

    /**
     * Execute the job.
     */
    public function handle(SyncService $syncService): void
    {
        if ($this->integration) {
            $syncService->sync($this->integration);
        } else {
            // Sync all active integrations
            ExternalIntegration::where('is_active', true)->get()->each(function ($integration) use ($syncService) {
                $syncService->sync($integration);
            });
        }
    }
}
