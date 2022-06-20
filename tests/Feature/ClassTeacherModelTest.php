<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\ClassTeacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClassTeacherModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_class_teacher_has_one_class()
    {
        $classModel = ClassModel::factory()->create();
        $classTeacher = ClassTeacher::factory()->create(['class_id' => $classModel->id]);

        $this->assertInstanceOf('App\Models\ClassModel', $classTeacher->classModel);
        $this->assertEquals($classModel->fresh(), $classTeacher->classModel);
    }

    /** @test */
    public function a_class_student_has_one_academic_year()
    {
        $academicYear = AcademicYear::factory()->create();
        $classTeacher = ClassTeacher::factory()->create(['academic_year_id' => $academicYear->id]);

        $this->assertInstanceOf('App\Models\AcademicYear', $classTeacher->academicYear);
        $this->assertEquals($academicYear->fresh(), $classTeacher->academicYear);
    }
}
