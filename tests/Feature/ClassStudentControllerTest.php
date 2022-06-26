<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;

class ClassStudentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /** @test */
    public function the_form_can_be_viewed()
    {
        //a user who is not a teacher cannot view the form
        $this->actingAs(User::where('user_type', '<>', 'teacher')->first())
            ->get(route('class_student.form'))
            ->assertForbidden();

        $this->actingAs(User::where('user_type', 'teacher')->first())
            ->get(route('class_student.form'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('ClassStudent/Form')
                    ->hasAll('classes')
                    ->hasAll('academicYears'),
            );
    }

    /** @test */
    public function the_join_class_form_can_be_viewed()
    {
        //a user who is not a student cannot view the form
        $this->actingAs(User::where('user_type', '<>', 'student')->first())
            ->get(route('class_student.form'))
            ->assertForbidden();

        $this->actingAs(User::where('user_type', 'student')->first())
            ->get(route('class_student.join_class.form'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('ClassStudent/JoinClass/Form')
                    ->hasAll(
                        'classStudentPivotData',
                        'classes',
                        'academicYears',
                        'defaultAcademicYear',
                    ),
            );
    }

    /** @test */
    public function a_student_can_join_a_class()
    {
        $student = User::where('user_type', 'student')->first();

        $this->actingAs($student)->post(
            route('class_student.store', [
                'class_id' => 1,
                'academic_year_id' => 1,
            ]),
        );

        $this->assertDatabaseHas('class_student', [
                'student_id' => $student->id,
                'class_id' => 1,
                'academic_year_id' => 1,
        ]);
    }

    /** @test */
    public function students_can_be_gotten_from_class_model_and_academic_year()
    {
        $this->actingAs(User::where('default_user_type', 'teacher')->first())
            ->getJson(
                route('class_student.students', [
                    'classModel' => 12,
                    'academicYear' => 6,
                ]),
            )
            ->assertJson(fn(AssertableJson $json) => $json->has('students'));
    }

    /** @test */
    public function a_class_student_model_can_be_deleted()
    {
        $studentId = User::where('user_type', 'student')->first()->id;

        $classStudentId = DB::table('class_student')->insertGetId([
            'class_id' => 5,
            'student_id' => $studentId,
            'academic_year_id' => 6,
        ]);

        // a user who is not a teacher cannot delete an academic year
        $this->actingAs(User::where('user_type', '<>', 'teacher')->first())
            ->delete(
                route('class_student.destroy', [
                    'classStudent' => $classStudentId,
                ]),
            )
            ->assertForbidden();

        $this->actingAs(User::where('user_type', 'teacher')->first())
            ->delete(
                route('class_student.destroy', [
                    'classStudent' => $classStudentId,
                ]),
            )
            ->assertRedirect();

        $this->assertDatabaseMissing('class_student', [
            'id' => $classStudentId,
        ]);
    }
}
