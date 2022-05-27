<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ClassTeacherPivotFactory extends Factory
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
            'teacher_id' => User::factory(),
            'academic_year_id' => AcademicYear::factory()
        ];
    }
}
