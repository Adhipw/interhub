<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\ApplicationScore;
use App\Models\Company;
use App\Models\CompanyMember;
use App\Models\Internship;
use App\Models\RecruitmentStage;
use App\Models\ScreeningRubric;
use App\Models\User;
use App\Services\AI\AiService;
use App\Services\AI\DTOs\AiResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AiBatch18Test extends TestCase
{
    use RefreshDatabase;

    protected $hrUser;

    protected $internship;

    protected $application;

    protected $stages;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'hr']);

        $this->hrUser = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $this->hrUser->assignRole('hr');

        $company = Company::factory()->create(['id' => 1]);
        CompanyMember::create([
            'user_id' => $this->hrUser->id,
            'company_id' => $company->id,
            'role' => 'owner',
            'is_active' => true,
        ]);

        $this->internship = Internship::factory()->create(['company_id' => 1]);
        $this->application = Application::factory()->create(['internship_id' => $this->internship->id]);

        // Create stages
        $this->stages = [
            RecruitmentStage::create(['internship_id' => $this->internship->id, 'name' => 'Stage 1', 'order' => 1, 'type' => 'screening']),
            RecruitmentStage::create(['internship_id' => $this->internship->id, 'name' => 'Stage 2', 'order' => 2, 'type' => 'interview']),
        ];

        config(['ai.default' => 'fake']);

        Gate::define('use-ai', function ($user) {
            return true;
        });
    }

    public function test_stage_transition_creates_history()
    {
        $this->actingAs($this->hrUser)
            ->withSession(['current_company_id' => 1])
            ->postJson(route('hr.ai.pipeline.update-stage'), [
                'application_id' => $this->application->id,
                'to_stage_id' => $this->stages[1]->id,
                'notes' => 'Passed screening',
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('application_stage_history', [
            'application_id' => $this->application->id,
            'to_stage_id' => $this->stages[1]->id,
            'notes' => 'Passed screening',
        ]);

        $this->assertEquals($this->stages[1]->id, $this->application->fresh()->current_stage_id);
    }

    public function test_cannot_transition_to_stage_of_other_internship()
    {
        $otherInternship = Internship::factory()->create(['company_id' => 1]);
        $otherStage = RecruitmentStage::create(['internship_id' => $otherInternship->id, 'name' => 'Other Stage']);

        $this->actingAs($this->hrUser)
            ->withSession(['current_company_id' => 1])
            ->postJson(route('hr.ai.pipeline.update-stage'), [
                'application_id' => $this->application->id,
                'to_stage_id' => $otherStage->id,
            ])
            ->assertStatus(422);
    }

    public function test_ai_score_requires_human_review()
    {
        ScreeningRubric::create([
            'internship_id' => $this->internship->id,
            'criteria' => [['name' => 'Skills', 'weight' => 100, 'description' => 'Technical test']],
        ]);

        $aiResult = json_encode([
            'score' => 85,
            'justification' => 'Strong skills',
            'factors_used' => ['Skills'],
            'factors_ignored' => ['Gender', 'Age'],
        ]);

        $this->mock(AiService::class, function ($mock) use ($aiResult) {
            $mock->shouldReceive('chat')->andReturn(new AiResponse($aiResult, []));
        });

        $this->actingAs($this->hrUser)
            ->withSession(['current_company_id' => 1])
            ->postJson(route('hr.ai.scoring.ai-score', ['application' => $this->application->id]))
            ->assertStatus(200);

        $this->assertDatabaseHas('application_scores', [
            'application_id' => $this->application->id,
            'score' => 85,
            'human_reviewed' => false,
            'is_ai_suggested' => true,
        ]);
    }

    public function test_fairness_guard_ignored_factors_present()
    {
        ScreeningRubric::create([
            'internship_id' => $this->internship->id,
            'criteria' => [['name' => 'Skills', 'weight' => 100, 'description' => 'Technical test']],
        ]);

        $aiResult = json_encode([
            'score' => 70,
            'justification' => 'Decent match',
            'factors_used' => ['Skills'],
            'factors_ignored' => ['Race', 'Religion'],
        ]);

        $this->mock(AiService::class, function ($mock) use ($aiResult) {
            $mock->shouldReceive('chat')->andReturn(new AiResponse($aiResult, []));
        });

        $this->actingAs($this->hrUser)
            ->withSession(['current_company_id' => 1])
            ->postJson(route('hr.ai.scoring.ai-score', ['application' => $this->application->id]))
            ->assertStatus(200);

        $this->assertDatabaseHas('application_scores', [
            'application_id' => $this->application->id,
            'score' => 70,
        ]);

        $score = ApplicationScore::where('application_id', $this->application->id)->first();
        $this->assertContains('Race', $score->factors['factors_ignored']);
    }
}
