<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NoticeBoard>
 */
class NoticeBoardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'school_id' => 1,
            'term_id' => 1,
            'user_id' => 2,
            'message' => $this->faker->paragraphs(5, true),
        ];
    }
}
