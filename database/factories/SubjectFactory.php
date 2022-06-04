<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fn() => $this->faker->randomElement([
                'English',
                'Mathematics',
                'Science',
                'French',
                'Religious and Moral Education',
                'Social Studies',
                'Basic Design in Technology',
                'Ghanaian Language',
                'ICT',
            ]),
        ];
    }
}
