<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\ExternalIntegration;
use App\Models\Internship;
use App\Models\User;
use App\Services\ExternalIntegration\SyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ExternalIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->admin->assignRole('admin');
    }

    #[Test]
    public function csv_import_creates_pending_listings()
    {
        Storage::fake('local');
        $csvContent = "id,title,company,description,type,location\n";
        $csvContent .= 'ext-1,Test Job,Test Corp,A test description,Remote,Jakarta';
        Storage::put('imports/test.csv', $csvContent);

        $integration = ExternalIntegration::create([
            'name' => 'CSV Import Test',
            'provider' => 'csv',
            'settings' => ['file_path' => 'imports/test.csv'],
            'is_active' => true,
        ]);

        $syncService = new SyncService;
        $syncService->sync($integration);

        $this->assertDatabaseHas('internships', [
            'external_id' => 'ext-1',
            'status' => 'pending_review',
            'is_external' => true,
        ]);

        $this->assertDatabaseHas('companies', [
            'name' => 'Test Corp',
        ]);
    }

    #[Test]
    public function duplicate_detection_works()
    {
        $integration = ExternalIntegration::create([
            'name' => 'Duplicate Test',
            'provider' => 'csv',
            'is_active' => true,
        ]);

        $company = Company::factory()->create(['name' => 'Old Corp']);
        Internship::factory()->create([
            'company_id' => $company->id,
            'title' => 'Existing Job',
            'status' => 'published',
            'is_external' => true,
            'external_source' => 'csv',
            'external_id' => 'dup-101',
        ]);

        $syncService = new SyncService;

        // This item should be detected as duplicate by external_id
        $items = [
            [
                'external_id' => 'dup-101',
                'title' => 'Existing Job',
                'company_name' => 'Old Corp',
                'description' => 'Desc',
            ],
        ];

        $syncService->processExternalData($integration, $items);

        // Count should still be 1
        $this->assertEquals(1, Internship::count());
    }

    #[Test]
    public function admin_can_approve_external_listing()
    {
        $company = Company::factory()->create();
        $internship = Internship::factory()->create([
            'company_id' => $company->id,
            'title' => 'Pending External Job',
            'status' => 'pending_review',
            'is_external' => true,
            'external_source' => 'webhook',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.integrations.review.approve', $internship->id));

        $response->assertRedirect();
        $this->assertEquals('published', $internship->fresh()->status);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin_external_listing_approved',
        ]);
    }
}
