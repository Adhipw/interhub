<?php

namespace Tests\Feature\ExternalIntegration;

use App\Models\ExternalIntegration;
use App\Models\Internship;
use App\Models\User;
use App\Services\ExternalIntegration\SyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ExternalIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake('local');

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    }

    public function test_csv_import_creates_pending_external_listings()
    {
        $csvContent = "id,title,company,description,type,location,salary,url\n".
                     'ext-1,Backend Intern,Tech Corp,Nice job,WFH,Jakarta,2000000,http://tech.com/job1';

        $path = 'temp/import.csv';
        Storage::put($path, $csvContent);

        $integration = ExternalIntegration::create([
            'name' => 'CSV Partner',
            'provider' => 'csv',
            'settings' => ['file_path' => $path],
            'is_active' => true,
        ]);

        $syncService = new SyncService;
        $syncService->sync($integration);

        $this->assertDatabaseHas('internships', [
            'external_id' => 'ext-1',
            'status' => 'pending_review',
            'is_external' => true,
            'external_source' => 'csv',
        ]);

        $internship = Internship::where('external_id', 'ext-1')->first();
        $this->assertEquals('Tech Corp', $internship->company->name);
    }

    public function test_duplicate_detection_works()
    {
        $integration = ExternalIntegration::create([
            'name' => 'Manual Partner',
            'provider' => 'manual',
            'is_active' => true,
        ]);

        $syncService = new SyncService;

        $data = [
            [
                'external_id' => 'dup-1',
                'title' => 'Duplicate Test',
                'company_name' => 'Dup Co',
                'description' => 'Desc',
                'type' => 'Office',
                'location' => 'Jakarta',
                'salary_range' => null,
                'external_url' => null,
                'requirements' => [],
                'deadline_at' => null,
            ],
        ];

        // First sync
        $syncService->processExternalData($integration, $data);
        $this->assertDatabaseCount('internships', 1);

        // Second sync with same data
        $syncService->processExternalData($integration, $data);
        $this->assertDatabaseCount('internships', 1); // Should still be 1
    }

    public function test_credentials_not_exposed_plaintext()
    {
        $credentials = ['api_key' => 'secret-123-key'];

        $integration = ExternalIntegration::create([
            'name' => 'Secret Partner',
            'provider' => 'maganghub',
            'credentials' => $credentials,
            'is_active' => true,
        ]);

        // Check database raw value
        $raw = \DB::table('external_integrations')->where('id', $integration->id)->value('credentials');
        $this->assertStringNotContainsString('secret-123-key', $raw);

        // Model access should be decrypted
        $this->assertEquals('secret-123-key', $integration->credentials['api_key']);
    }

    public function test_admin_approval_publishes_listing()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        $internship = Internship::factory()->create([
            'status' => 'pending_review',
            'is_external' => true,
        ]);

        // Using Admin's update-status route
        $response = $this->actingAs($admin)->patch(route('admin.internships.update-status', $internship->id), [
            'status' => 'published',
        ]);

        $response->assertRedirect();
        $this->assertEquals('published', $internship->fresh()->status);
    }
}
