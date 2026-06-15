<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Internship;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Internship>
 */
class InternshipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->jobTitle();

        return [
            'company_id' => Company::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::random(5),
            'description' => $this->faker->paragraphs(3, true),
            'requirements' => $this->faker->paragraphs(2, true),
            'type' => $this->faker->randomElement(['WFH', 'Office', 'Hybrid']),
            'location' => $this->faker->city(),
            'status' => 'published',
            'deadline_at' => $this->faker->dateTimeBetween('+1 month', '+3 months'),
        ];
    }
}
