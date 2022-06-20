<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClassStudent>
 */
class ClassStudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'class_id' => ClassModel::factory(),
            'student_id' => User::factory(),
            'academic_year_id' => AcademicYear::factory(),
        ];
    }
}
