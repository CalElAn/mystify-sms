<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\School;
use App\Models\Term;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SchoolFeesPaid>
 */
class SchoolFeesPaidFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'student_id' => User::factory(),
            'school_id' => School::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'amount' => rand(0, 100),
        ];
    }
}
