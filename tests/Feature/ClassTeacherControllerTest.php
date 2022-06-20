<?php

namespace Tests\Feature;

use App\Models\ClassTeacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;

class ClassTeacherControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /** @test */
    public function the_form_can_be_viewed()
    {
        //a user who is not a teacher cannot view the form
        $this->actingAs(User::where('user_type', '<>', 'teacher')->first())
            ->get(route('class_teacher.form'))
            ->assertForbidden();

        $this->actingAs(User::where('user_type', 'teacher')->first())
            ->get(route('class_teacher.form'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('ClassTeacher/Form')
                    ->hasAll('classTeacherPivotData')
                    ->hasAll('classes')
                    ->hasAll('academicYears'),
            );
    }

    /** @test */
    public function a_class_teacher_model_can_be_stored()
    {
        $input = [
            'class_id' => 12,
            'academic_year_id' => 1
        ];

        // a user who is not a teacher cannot store a class_teacher model
        $this->actingAs(
            User::where('user_type', '<>', 'teacher')->first(),
        )
            ->post(
                route('class_teacher.store'),
                $input,
            )
            ->assertForbidden();

        $teacher = User::where('user_type', 'teacher')->first();

        $this->actingAs(
            $teacher
        )
            ->post(
                route('class_teacher.store'),
                $input,
            )
            ->assertRedirect();

        $this->assertDatabaseHas('class_teacher', [
            'teacher_id' => $teacher->id,
            'class_id' => 12,
            'academic_year_id' => 1
        ]);
    }


    /** @test */
    public function a_class_teacher_model_can_be_deleted()
    {
        $classTeacherModel = ClassTeacher::factory()->create();

        // a user who is not a teacher cannot delete an academic year
        $this->actingAs(User::where('user_type', '<>', 'teacher')->first())
            ->delete(
                route('class_teacher.destroy', [
                    'classTeacher' => $classTeacherModel->id,
                ]),
            )
            ->assertForbidden();

        $this->actingAs(User::where('user_type', 'teacher')->first())
            ->delete(
                route('class_teacher.destroy', [
                    'classTeacher' => $classTeacherModel->id,
                ]),
            )
            ->assertRedirect();

        $this->assertDatabaseMissing('class_teacher', $classTeacherModel->getAttributes());
    }
}
