<?php

namespace Tests\Feature;

use App\Models\Grade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GradeModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_grade_has_an_overall_mark()
    {
        $grade = Grade::factory()->create();

        $this->assertEquals($grade->exam_mark + $grade->class_mark, $grade->overall_mark);
    }

    /** @test */
    public function a_grade_has_a_class_name_and_suffix()
    {
        $grade = Grade::factory()->create();

        $this->assertEquals("{$grade->class_name} {$grade->class_suffix}", $grade->class_name_and_suffix);
    }

    /** @test */
    public function a_grade_belongs_to_a_school()
    {
        $grade = Grade::factory()->create();

        $this->assertInstanceOf('App\Models\School', $grade->school);
    }
}
