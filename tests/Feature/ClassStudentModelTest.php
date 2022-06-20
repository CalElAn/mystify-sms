<?php

namespace Tests\Feature;

use App\Models\ClassStudent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\ClassModel;
use App\Models\User;
use App\Models\AcademicYear;
use App\Models\Term;
use Illuminate\Support\Facades\DB;

class ClassStudentModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_class_student_has_one_class()
    {
        $classStudent = ClassStudent::factory()->create();
        $this->assertInstanceOf('App\Models\ClassModel', $classStudent->classModel);
    }
    
    /** @test */
    public function a_class_student_has_one_student()
    {
        $classStudent = ClassStudent::factory()->create();
        $this->assertInstanceOf('App\Models\User', $classStudent->student);
    }

    /** @test */
    public function a_class_student_has_one_academic_year()
    {
        $classStudent = ClassStudent::factory()->create();
        $this->assertInstanceOf('App\Models\AcademicYear', $classStudent->academicYear);
    }
}
