<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\School;
use App\Models\Term;
use App\Models\User;

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
            'school_id' => School::factory(),
            'term_id' => Term::factory(),
            'user_id' => User::factory(),
            'message' => $this->faker->paragraphs(5, true),
        ];
    }
}
