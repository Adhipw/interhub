<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Automatically seed Spatie roles & permissions to prevent missing role exceptions in test environments
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }
}
