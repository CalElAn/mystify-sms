<?php

namespace Tests\Feature;

use App\Models\SubjectTeacherPivot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubjectTeacherPivotModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_subject_teacher_pivot_model_has_one_class_model()
    {
        $subject = SubjectTeacherPivot::factory()->create();

        $this->assertInstanceOf('App\Models\ClassModel', $subject->classModel);
    }

    /** @test */
    public function a_subject_teacher_pivot_model_has_one_term()
    {
        $subject = SubjectTeacherPivot::factory()->create();

        $this->assertInstanceOf('App\Models\Term', $subject->term);
    }
}
