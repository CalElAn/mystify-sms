<?php

namespace Tests\Feature;

use App\Models\ClassStudentPivot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\ClassModel;
use App\Models\User;
use App\Models\AcademicYear;
use App\Models\Term;
use Illuminate\Support\Facades\DB;

class ClassStudentPivotModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_class_student_pivot_has_one_class()
    {
        $class = ClassModel::factory()->create();
        $student = User::factory()->create(); 

        $classStudentPivotId = DB::table('class_student_pivot')->insertGetId([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'academic_year_id' => AcademicYear::factory()->create()->id,
        ]);

        $this->assertInstanceOf('App\Models\ClassModel', ClassStudentPivot::find($classStudentPivotId)->classModel);
    }

    /** @test */
    public function a_class_student_pivot_has_many_terms()
    {
        $class = ClassModel::factory()->create();
        $student = User::factory()->create(); 

        $classStudentPivotId = DB::table('class_student_pivot')->insertGetId([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'academic_year_id' => $academicYearId = AcademicYear::factory()->create()->id,
        ]);

        Term::factory()->create(['academic_year_id' => $academicYearId]);

        $this->assertInstanceOf('App\Models\Term', ClassStudentPivot::find($classStudentPivotId)->terms->first());
    }
}
