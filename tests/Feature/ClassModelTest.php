<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ClassModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_class_belongs_to_many_students()
    {
        $class = ClassModel::factory()->create();
        $student = User::factory()->create(); 

        DB::table('class_student')->insert([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'academic_year_id' => AcademicYear::factory()->create()->id,
        ]);

        $this->assertInstanceOf('App\Models\User', $class->students[0]);
    }

    /** @test */
    public function a_class_belongs_to_many_teachers()
    {
        $class = ClassModel::factory()->create();
        $teacher = User::factory()->create(); 

        DB::table('class_teacher')->insert([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'academic_year_id' => AcademicYear::factory()->create()->id,
        ]);

        $this->assertInstanceOf('App\Models\User', $class->teachers[0]);
    }
}
