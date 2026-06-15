<?php

namespace App\Jobs;

use App\Models\ExternalIntegration;
use App\Services\ExternalIntegration\SyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncExternalIntegrationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ExternalIntegration $integration
    ) {}

    /**
     * Execute the job.
     */
    public function handle(SyncService $syncService): void
    {
        $syncService->sync($this->integration);
    }
}
