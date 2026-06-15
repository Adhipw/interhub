<?php

namespace Database\Seeders;

use App\Models\Internship;
use App\Models\RecruitmentStage;
use Illuminate\Database\Seeder;

class RecruitmentPipelineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $internships = Internship::all();

        foreach ($internships as $internship) {
            $stages = [
                ['name' => 'Sourcing', 'order' => 1, 'type' => 'screening', 'sla_days' => 3],
                ['name' => 'HR Interview', 'order' => 2, 'type' => 'interview', 'sla_days' => 5],
                ['name' => 'User Interview', 'order' => 3, 'type' => 'interview', 'sla_days' => 5],
                ['name' => 'Technical Test', 'order' => 4, 'type' => 'technical', 'sla_days' => 7],
                ['name' => 'Offering', 'order' => 5, 'type' => 'offer', 'sla_days' => 3],
                ['name' => 'Hired', 'order' => 6, 'type' => 'hired', 'sla_days' => null],
                ['name' => 'Rejected', 'order' => 7, 'type' => 'rejected', 'sla_days' => null],
            ];

            foreach ($stages as $stageData) {
                RecruitmentStage::firstOrCreate(
                    [
                        'internship_id' => $internship->id,
                        'name' => $stageData['name'],
                    ],
                    $stageData
                );
            }
        }
    }
}
