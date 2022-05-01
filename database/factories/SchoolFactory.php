<?php

namespace Database\Factories;

use App\Models\GradingScale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\School>
 */
class SchoolFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $this->faker->addProvider(new \Bezhanov\Faker\Provider\Educator($this->faker)); 

        return [
            'name' => $this->faker->secondarySchool,
            'city' => $this->faker->state(),
            'town' => $this->faker->streetName(),
            'street_address' => $this->faker->address(),
            'grading_scale_id' => GradingScale::factory(),
        ];
    }
}
