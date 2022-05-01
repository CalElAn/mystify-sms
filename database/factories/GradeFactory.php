<?php

namespace Database\Factories;

use App\Models\ClassModel;
use App\Models\School;
use App\Models\Subject;
use App\Models\User;
use App\Models\Term;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Grade>
 */
class GradeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $school = School::factory()->create()->fresh();
        $class = ClassModel::factory()->create(['school_id' => $school->school_id]);
        $subject = Subject::factory()->create();
        return [
            'school_id' => $school->school_id,
            'student_id' => User::factory(),
            'teacher_id' => User::factory(), 
            'term_id' => Term::factory(),
            'class_name' => $class->name,
            'class_suffix' => $class->suffix,
            'subject_name' => $subject->name,
            'class_mark' => random_int(0, 30),
            'exam_mark' => random_int(0, 70),
        ];
    }
}
