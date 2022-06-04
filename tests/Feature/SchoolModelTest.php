<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\GradingScale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use App\Models\NoticeBoard;
use App\Models\SchoolFeesPaid;
use App\Models\SchoolFees;
use App\Models\Term;
use App\Models\Grade;
use App\Models\ClassModel;
use Illuminate\Support\Facades\DB;

class SchoolModelTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;

    public function setUp(): void
    {
        parent::setUp();

        // $this->withoutExceptionHandling();
        $this->school = School::factory()->create();
    }

    /** @test */
    public function a_school_has_many_users()
    {
        $user = User::factory(10)->create([
            'school_id' => $this->school->id,
        ]);

        $this->assertTrue($this->school->users->contains($user[0]));
        $this->assertInstanceOf('App\Models\User', $this->school->users[0]);
        $this->assertEquals(10, $this->school->users->count());
    }

    /** @test */
    public function a_school_has_many_academic_years()
    {
        $academicYears = AcademicYear::factory(1)->create([
            'school_id' => $this->school->id,
        ]);

        $this->assertTrue(
            $this->school->academicYears->contains($academicYears->first()),
        );
        $this->assertInstanceOf(
            'App\Models\AcademicYear',
            $this->school->academicYears->first(),
        );
        $this->assertEquals(1, $this->school->academicYears->count());
    }

    /** @test */
    public function a_school_has_many_school_fees_paid()
    {
        $student = User::factory(3)->create([
            'default_user_type' => 'student',
            'school_id' => $this->school->id,
        ]);
        $feesPaid = SchoolFeesPaid::factory(3)->create([
            'student_id' => $student[0]->id,
            'school_id' => $this->school->id,
        ]);
        SchoolFeesPaid::factory(2)->create([
            'student_id' => $student[1]->id,
            'school_id' => $this->school->id,
        ]);

        $this->assertTrue(
            $this->school->schoolFeesPaid->contains($feesPaid[0]),
        );
        $this->assertInstanceOf(
            'App\Models\SchoolFeesPaid',
            $this->school->schoolFeesPaid[0],
        );
        $this->assertEquals(5, $this->school->schoolFeesPaid->count());
    }

    /** @test */
    public function a_school_has_many_school_fees_owed()
    {
        $student = User::factory(3)->create([
            'default_user_type' => 'student',
            'school_id' => $this->school->id,
        ]);
        $student->each(function ($item) {
            SchoolFees::factory()->create([
                'student_id' => $item->id,
                'school_id' => $this->school->id,
            ]);
        });

        $this->assertTrue(
            $this->school->schoolFees->contains(SchoolFees::all()[0]),
        );
        $this->assertInstanceOf(
            'App\Models\SchoolFees',
            $this->school->schoolFees[0],
        );
        $this->assertEquals(3, $this->school->schoolFees->count());
    }

    /** @test */
    public function get_school_fees_data_for_line_chart()
    {
        $students = User::factory(3)->create([
            'default_user_type' => 'student',
            'school_id' => $this->school->id,
        ]);
        $academicYear = AcademicYear::factory()->create();

        $arrayOfSchoolFeesPaid = [
            [
                'student_id' => $students[0]->id,
                'school_id' => $this->school->id,
                'academic_year_id' => $academicYear->id,
                'amount' => 26,
                'created_at' => '2022-03-01T04:49:30.000000Z',
            ],
            [
                'student_id' => $students[1]->id,
                'school_id' => $this->school->id,
                'academic_year_id' => $academicYear->id,
                'amount' => 27,
                'created_at' => '2022-04-04T01:10:06.000000Z',
            ],
            [
                'student_id' => $students[2]->id,
                'school_id' => $this->school->id,
                'academic_year_id' => $academicYear->id,
                'amount' => 72,
                'created_at' => '2022-02-07T00:03:12.000000Z',
            ],
        ];

        foreach ($arrayOfSchoolFeesPaid as $value) {
            SchoolFeesPaid::forceCreate($value);
        }

        $sortedArray = [
            [
                'weekName' => 'Week 1',
                'startOfWeek' => '2022-02-07',
                'startOfWeekFormat' => '7th February',
                'sumOfAmount' => 72,
            ],
            [
                'weekName' => 'Week 2',
                'startOfWeek' => '2022-02-28',
                'startOfWeekFormat' => '1st March',
                'sumOfAmount' => 26,
            ],
            [
                'weekName' => 'Week 3',
                'startOfWeek' => '2022-04-04',
                'startOfWeekFormat' => '4th April',
                'sumOfAmount' => 27,
            ],
        ];

        $this->assertEquals(
            $sortedArray,
            $this->school
                ->getSchoolFeesDataForLineChart($academicYear->id)
                ->toArray(),
        );
    }

    /** @test */
    public function a_school_has_many_notice_board_messages()
    {
        $academicYear = AcademicYear::factory()->create();
        $term = Term::factory()->create([
            'academic_year_id' => $academicYear->id,
        ]);

        NoticeBoard::factory(3)->create([
            'school_id' => $this->school->id,
            'term_id' => $term->id,
        ]);

        $this->assertTrue(
            $this->school->noticeBoard->contains(NoticeBoard::all()[0]),
        );
        $this->assertInstanceOf(
            'App\Models\NoticeBoard',
            $this->school->noticeBoard[0],
        );
        $this->assertEquals(3, $this->school->noticeBoard->count());
    }

    /** @test */
    public function a_school_has_one_grading_scale()
    {
        $gradingScale = GradingScale::factory()->create();
        $this->school->grading_scale_id = $gradingScale->id;
        $this->school->save();
        $this->assertInstanceOf(
            'App\Models\GradingScale',
            $this->school->gradingScale,
        );
        $this->assertEquals(
            $gradingScale->fresh(),
            $this->school->gradingScale,
        );
    }

    /** @test */
    public function a_school_has_many_grades()
    {
        $classModel = ClassModel::factory()->create([
            'school_id' => $this->school->id,
        ]);

        Grade::factory()->create([
            'school_id' => $this->school->id,
            'class_name' => $classModel->name,
            'class_suffix' => $classModel->suffix,
        ]);

        $this->assertInstanceOf('App\Models\Grade', $this->school->grades[0]);
        $this->assertEquals(1, $this->school->grades->count());
    }

    /** @test */
    public function a_school_has_many_classes()
    {
        ClassModel::factory()->create(['school_id' => $this->school->id]);

        $this->assertInstanceOf(
            'App\Models\ClassModel',
            $this->school->classes[0],
        );
        $this->assertEquals(1, $this->school->classes->count());
    }

    /** @test */
    public function a_school_can_get_its_academic_years_with_terms()
    {
        AcademicYear::factory()
            ->count(3)
            ->state([
                'school_id' => $this->school->id,
            ])
            ->has(Term::factory()->count(2), 'terms')
            ->create();

        $academicYearsWithTerms = $this->school->getAcademicYearsWithTerms();

        $this->assertEquals(3, $academicYearsWithTerms->count());

        $academicYearsWithTerms->each(function ($item) {
            $this->assertEquals(2, $item->terms->count());
            $this->assertInstanceOf('App\Models\Term', $item->terms[0]);
        });
    }

    /** @test */
    public function a_school_can_get_the_term_from_a_request()
    {
        AcademicYear::factory()
            ->count(3)
            ->state([
                'school_id' => $this->school->id,
            ])
            ->has(
                Term::factory()
                    ->count(2)
                    ->state(function (
                        array $attributes,
                        AcademicYear $academicYear,
                    ) {
                        return [
                            //dd($attributes),
                            'start_date' =>
                                '2022-' .
                                $academicYear->terms()->count() .
                                '-1',
                            'end_date' =>
                                '2022-' .
                                $academicYear->terms()->count() .
                                '-20',
                        ];
                    }),
                'terms',
            )
            ->create();

        $this->assertEquals(
            Term::find(3)->append('formatted_name'),
            $this->school->getTerm(
                new \Illuminate\Http\Request(['termId' => 3]),
            ),
        );
        $this->assertEquals(
            Term::find(
                AcademicYear::find(2)
                    ->terms()
                    ->latest('end_date')
                    ->first()->id,
            )->append('formatted_name'),
            $this->school->getTerm(
                new \Illuminate\Http\Request(['academicYearId' => 2]),
            ),
        );
        $this->assertEquals(
            $this->school
                ->getAcademicYearsWithTerms()
                ->first()
                ->terms->sortByDesc('end_date')
                ->values()
                ->first()
                ->append('formatted_name'),
            $this->school->getTerm(new \Illuminate\Http\Request()),
        );
    }

    /** @test */
    public function a_school_can_get_the_grade_for_a_mark()
    {
        $gradingScale = GradingScale::factory()->create();
        $this->school->grading_scale_id = $gradingScale->id;
        $this->school->save();

        $this->school->gradingScale->scale->each(function ($item, $key) {
            $this->assertEquals(
                $key,
                $this->school->getGradeForMark(rand($item[0], $item[1])),
            );
        });
    }

    /** @test */
    public function a_school_can_get_its_students()
    {
        $user = User::factory(10)->create([
            'school_id' => $this->school->id,
            'default_user_type' => 'student',
        ]);

        $this->assertTrue($this->school->getStudents()->contains($user[0]));
        $this->assertInstanceOf(
            'App\Models\User',
            $this->school->getStudents()[0],
        );
        $this->assertEquals(10, $this->school->getStudents()->count());
    }

    /** @test */
    public function a_school_can_get_its_parents()
    {
        $student0 = User::factory()->create([
            'school_id' => $this->school->id,
            'default_user_type' => 'student',
        ]);
        $student1 = User::factory()->create([
            'school_id' => $this->school->id,
            'default_user_type' => 'student',
        ]);
        $student2 = User::factory()->create([
            'school_id' => $this->school->id,
            'default_user_type' => 'student',
        ]);
        $parent1 = User::factory()->create([
            'school_id' => $this->school->id,
            'default_user_type' => 'parent',
        ]);
        $parent2 = User::factory()->create([
            'school_id' => $this->school->id,
            'default_user_type' => 'parent',
        ]);
        $parent3 = User::factory()->create([
            'school_id' => $this->school->id,
            'default_user_type' => 'parent',
        ]);
        $parent4 = User::factory()->create([
            'school_id' => $this->school->id,
            'default_user_type' => 'parent',
        ]);

        DB::table('parent_student_pivot')->insert([
            ['student_id' => $student1->id, 'parent_id' => $parent1->id],
            ['student_id' => $student1->id, 'parent_id' => $parent2->id],
            ['student_id' => $student2->id, 'parent_id' => $parent1->id],
            ['student_id' => $student2->id, 'parent_id' => $parent2->id],
            ['student_id' => $student2->id, 'parent_id' => $parent3->id],
        ]);

        $this->assertEquals(3, $this->school->getParents()->count());
    }

    /** @test */
    public function a_school_can_get_its_teachers()
    {
        $user = User::factory(10)->create([
            'school_id' => $this->school->id,
            'default_user_type' => 'teacher',
        ]);

        $this->assertTrue($this->school->getTeachers()->contains($user[0]));
        $this->assertInstanceOf(
            'App\Models\User',
            $this->school->getTeachers()[0],
        );
        $this->assertEquals(10, $this->school->getTeachers()->count());
    }

    /** @test */
    public function a_school_can_get_its_administrators()
    {
        $user = User::factory(10)->create([
            'school_id' => $this->school->id,
            'default_user_type' => 'school administrator',
        ]);

        $this->assertTrue(
            $this->school->getAdministrators()->contains($user[0]),
        );
        $this->assertInstanceOf(
            'App\Models\User',
            $this->school->getAdministrators()[0],
        );
        $this->assertEquals(10, $this->school->getAdministrators()->count());
    }
}
