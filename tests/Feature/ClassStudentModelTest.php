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
        $class = ClassModel::factory()->create();
        $student = User::factory()->create(); 

        $classStudentId = DB::table('class_student')->insertGetId([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'academic_year_id' => AcademicYear::factory()->create()->id,
        ]);

        $this->assertInstanceOf('App\Models\ClassModel', ClassStudent::find($classStudentId)->classModel);
    }

    /** @test */
    // public function a_class_student_has_many_terms()
    // {
    //     $class = ClassModel::factory()->create();
    //     $student = User::factory()->create(); 

    //     $classStudentId = DB::table('class_student')->insertGetId([
    //         'class_id' => $class->id,
    //         'student_id' => $student->id,
    //         'academic_year_id' => $academicYearId = AcademicYear::factory()->create()->id,
    //     ]);

    //     Term::factory()->create(['academic_year_id' => $academicYearId]);

    //     $this->assertInstanceOf('App\Models\Term', ClassStudent::find($classStudentId)->terms->first());
    // }
}
