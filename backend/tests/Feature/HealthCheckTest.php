<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_health_endpoint_does_not_require_authentication(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'ok')
            ->assertJsonStructure([
                'data' => [
                    'services' => [
                        'database',
                        'storage',
                        'cache',
                    ],
                ],
            ]);
    }
}
