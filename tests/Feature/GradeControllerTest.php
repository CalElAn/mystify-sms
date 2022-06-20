<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
use App\Models\User;
use App\Models\Grade;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;

class GradeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /** @test */
    public function the_form_can_be_viewed()
    {
        //a user who is not a teacher cannot view the form
        $this->actingAs(User::where('user_type', '<>', 'teacher')->first())
            ->get(route('grades.form'))
            ->assertForbidden();

        $this->actingAs(User::where('user_type', 'teacher')->first())
            ->get(route('grades.form'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Grades/Form')
                    ->hasAll('subjects')
                    ->hasAll('classes')
                    ->hasAll('academicYears'),
            );
    }

    /** @test */
    public function students_with_grades_can_be_gotten()
    {
        $this->actingAs(User::where('default_user_type', 'teacher')->first())
            ->getJson(
                route('grades.students_with_grades', [
                    'classModel' => 12,
                    'term' => 12,
                    'subjectName' => 'English',
                ]),
            )
            ->assertJson(
                fn(AssertableJson $json) => $json->has('studentsWithGrades'),
            );
    }

    /** @test */
    public function grades_can_be_upserted_and_deleted()
    {
        $teacherId = User::where('user_type', 'teacher')->first()->id;
        $secondTeacherId = User::where('user_type', 'teacher')
            ->get()
            ->random()->id;

        $students = User::where('user_type', 'student')->get();
        $firstStudentId = $students[0]->id;
        $secondStudentId = $students[1]->id;
        $thirdStudentId = $students[2]->id;

        $gradeIdToBeDeleted = DB::table('grades')->insertGetId([
            'school_id' => 1,
            'student_id' => $firstStudentId,
            'teacher_id' => $teacherId,
            'term_id' => 6,
            'class_name' => 'Class 6',
            'class_suffix' => 'A',
            'subject_name' => 'English',
            'class_mark' => 10,
            'exam_mark' => 20,
        ]);
        $secondGradeId = DB::table('grades')->insertGetId([
            'school_id' => 1,
            'student_id' => $secondStudentId,
            'teacher_id' => $teacherId,
            'term_id' => 6,
            'class_name' => 'Class 6',
            'class_suffix' => 'A',
            'subject_name' => 'English',
            'class_mark' => 30,
            'exam_mark' => 40,
        ]);
        $thirdGradeId = DB::table('grades')->insertGetId([
            'school_id' => 1,
            'student_id' => $thirdStudentId,
            'teacher_id' => $teacherId,
            'term_id' => 6,
            'class_name' => 'Class 6',
            'class_suffix' => 'A',
            'subject_name' => 'English',
            'class_mark' => 50,
            'exam_mark' => 60,
        ]);

        $input = [
            [
                'school_id' => 1,
                'student_id' => $firstStudentId,
                'teacher_id' => $teacherId,
                'term_id' => 6,
                'class_name' => 'Class 6',
                'class_suffix' => 'A',
                'subject_name' => 'English',
                'class_mark' => '',
                'exam_mark' => '',
            ],
            [
                'school_id' => 1,
                'student_id' => $secondStudentId,
                'teacher_id' => $teacherId,
                'term_id' => 6,
                'class_name' => 'Class 6',
                'class_suffix' => 'A',
                'subject_name' => 'English',
                'class_mark' => 33,
                'exam_mark' => 44,
            ],
            [
                'school_id' => 1,
                'student_id' => $thirdStudentId,
                'teacher_id' => $secondTeacherId,
                'term_id' => 6,
                'class_name' => 'Class 6',
                'class_suffix' => 'A',
                'subject_name' => 'English',
                'class_mark' => 55,
                'exam_mark' => 66,
            ],
        ];

        // a user who is not a teacher cannot delete an academic year
        $this->actingAs(User::where('user_type', '<>', 'teacher')->first())
            ->patch(route('grades.upsert', ['grades' => $input]))
            ->assertForbidden();

        $this->actingAs(User::where('user_type', 'teacher')->first())
            ->patch(route('grades.upsert', ['grades' => $input]))
            ->assertRedirect();

        $this->assertDatabaseMissing('grades', [
            'id' => $gradeIdToBeDeleted,
        ]);

        $this->assertDatabaseHas('grades', [
            'id' => $secondGradeId,
            'class_mark' => 33,
            'exam_mark' => 44,
        ]);

        $this->assertDatabaseHas('grades', [
            'id' => $thirdGradeId,
            'teacher_id' => $secondTeacherId,
            'class_mark' => 55,
            'exam_mark' => 66,
        ]);
    }
}
