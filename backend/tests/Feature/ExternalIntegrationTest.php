<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\ExternalIntegration;
use App\Models\Internship;
use App\Models\User;
use App\Services\ExternalIntegration\SyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExternalIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_credentials_are_encrypted_in_database()
    {
        $credentials = ['api_key' => 'secret-123'];
        $integration = ExternalIntegration::create([
            'name' => 'Test Integration',
            'provider' => 'maganghub',
            'credentials' => $credentials,
        ]);

        $raw = \DB::table('external_integrations')->where('id', $integration->id)->value('credentials');

        $this->assertNotContains('secret-123', [$raw]);
        $this->assertEquals('secret-123', $integration->credentials['api_key']);
    }

    public function test_csv_import_creates_pending_listings()
    {
        Storage::fake('local');
        $csvContent = "id,title,company,description,type,location,salary,url,requirements,deadline\n";
        $csvContent .= 'ext-1,External Intern,Acme Corp,Description,Remote,Jakarta,5M,http://test.com,"PHP,Laravel",2026-12-31';

        Storage::put('imports/test.csv', $csvContent);

        $integration = ExternalIntegration::create([
            'name' => 'CSV Import',
            'provider' => 'csv',
            'settings' => ['file_path' => 'imports/test.csv'],
        ]);

        $service = new SyncService;
        $service->sync($integration);

        $this->assertDatabaseHas('internships', [
            'title' => 'External Intern',
            'status' => 'pending_review',
            'is_external' => true,
            'external_source' => 'csv',
            'external_id' => 'ext-1',
        ]);

        $this->assertDatabaseHas('integration_logs', [
            'external_integration_id' => $integration->id,
            'status' => 'success',
            'items_imported' => 1,
        ]);
    }

    public function test_duplicate_detection_prevents_reimporting()
    {
        Storage::fake('local');
        $csvContent = "id,title,company,description,type,location,salary,url,requirements,deadline\n";
        $csvContent .= 'ext-1,External Intern,Acme Corp,Description,Remote,Jakarta,5M,http://test.com,"PHP,Laravel",2026-12-31';

        Storage::put('imports/test.csv', $csvContent);

        $integration = ExternalIntegration::create([
            'name' => 'CSV Import',
            'provider' => 'csv',
            'settings' => ['file_path' => 'imports/test.csv'],
        ]);

        $service = new SyncService;

        // First sync
        $service->sync($integration);
        $this->assertEquals(1, Internship::count());

        // Second sync (should skip duplicate)
        $service->sync($integration);
        $this->assertEquals(1, Internship::count());

        $this->assertDatabaseHas('integration_logs', [
            'external_integration_id' => $integration->id,
            'items_skipped' => 1,
        ]);
    }

    public function test_admin_approval_publishes_listing()
    {
        $listing = Internship::create([
            'company_id' => Company::factory()->create()->id,
            'title' => 'Pending External',
            'slug' => 'pending-external',
            'description' => 'Test',
            'type' => 'Office',
            'status' => 'pending_review',
            'is_external' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->post("/admin/integrations/review/{$listing->id}/approve");

        $response->assertRedirect();
        $this->assertEquals('published', $listing->fresh()->status);
    }
}
