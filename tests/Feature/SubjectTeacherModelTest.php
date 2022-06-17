<?php

namespace Tests\Feature;

use App\Models\SubjectTeacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubjectTeacherModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_subject_teacher_model_has_one_class_model()
    {
        $subject = SubjectTeacher::factory()->create();

        $this->assertInstanceOf('App\Models\ClassModel', $subject->classModel);
    }

    /** @test */
    public function a_subject_teacher_model_has_one_term()
    {
        $subject = SubjectTeacher::factory()->create();

        $this->assertInstanceOf('App\Models\Term', $subject->term);
    }
}
