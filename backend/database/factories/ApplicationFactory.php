<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Internship;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'internship_id' => Internship::factory(),
            'status' => 'pending',
            'cover_letter' => $this->faker->paragraph(),
            'timeline' => [
                [
                    'status' => 'pending',
                    'label' => 'Lamaran Terkirim',
                    'description' => 'Lamaran Anda telah berhasil dikirim.',
                    'date' => now()->toDateTimeString(),
                ],
            ],
            'cv_snapshot' => 'cvs/test-cv.pdf',
        ];
    }
}
