<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Term;
use App\Models\Subject;
use App\Models\School;
use App\Models\ClassModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubjectTeacherPivot>
 */
class SubjectTeacherPivotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'teacher_id' => User::factory(),
            'term_id' => Term::factory(),
            'subject_name' => Subject::factory()->create()->name,
            'class_id' => ClassModel::factory(),
        ];
    }
}
