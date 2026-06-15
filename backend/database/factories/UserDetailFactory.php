<?php

namespace Database\Factories;

use App\Models\UserDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserDetail>
 */
class UserDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bio' => fake()->paragraph(),
            'address' => fake()->address(),
            'skills' => ['PHP', 'Laravel', 'Vue.js'],
            'ai_consent' => false,
        ];
    }
}
