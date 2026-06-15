<?php

namespace App\Services\ExternalIntegration\Providers;

use App\Models\ExternalIntegration;

interface IntegrationProviderInterface
{
    /**
     * Fetch data from the external source.
     *
     * @return array List of standardized internship data
     */
    public function fetchData(ExternalIntegration $integration): array;

    /**
     * Validate the integration configuration.
     */
    public function validateConfig(array $credentials, array $settings): bool;
}
