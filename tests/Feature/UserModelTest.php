<?php

namespace Tests\Feature;

use App\Models\Grade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use App\Models\SchoolFeesPaid;
use App\Models\SchoolFees;
use App\Models\ClassModel;
use App\Models\AcademicYear;
use App\Models\ClassStudent;
use App\Models\ClassTeacher;
use App\Models\Subject;
use App\Models\Term;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_belongs_to_a_school()
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);

        $this->assertInstanceOf('App\Models\School', $user->school);
        $this->assertEquals($school->fresh(), $user->school);
    }

    /** @test */
    public function a_student_belongs_to_many_parents()
    {
        $student = User::factory()->create(['default_user_type' => 'student']);
        $parent1 = User::factory()->create(['default_user_type' => 'parent']);
        $parent2 = User::factory()->create(['default_user_type' => 'parent']);
        $parent3 = User::factory()->create(['default_user_type' => 'parent']);

        DB::table('parent_student')->insert([
            ['student_id' => $student->id, 'parent_id' => $parent1->id],
            ['student_id' => $student->id, 'parent_id' => $parent2->id],
        ]);

        $this->assertTrue($student->parents->contains($parent1));
        $this->assertTrue($student->parents->contains($parent2));
        $this->assertTrue($student->parents->doesntContain($parent3));
        $this->assertInstanceOf('App\Models\User', $student->parents[0]);
    }

    /** @test */
    public function a_parent_has_many_children()
    {
        $parent = User::factory()->create(['default_user_type' => 'parent']);
        $student1 = User::factory()->create(['default_user_type' => 'student']);
        $student2 = User::factory()->create(['default_user_type' => 'parent']);
        $student3 = User::factory()->create(['default_user_type' => 'parent']);

        DB::table('parent_student')->insert([
            ['student_id' => $student1->id, 'parent_id' => $parent->id],
            ['student_id' => $student2->id, 'parent_id' => $parent->id],
        ]);

        $this->assertTrue($parent->children->contains($student1));
        $this->assertTrue($parent->children->contains($student2));
        $this->assertTrue($parent->children->doesntContain($student3));
        $this->assertInstanceOf('App\Models\User', $parent->children[0]);
    }

    /** @test */
    public function a_teacher_has_many_class_teacher_pivot_models()
    {
        $teacher = User::factory()->create(['default_user_type' => 'teacher']);
        $classTeacher = ClassTeacher::factory()->create([
            'teacher_id' => $teacher->id,
        ]);

        $this->assertInstanceOf(
            'App\Models\ClassTeacher',
            $teacher->classTeacherPivot->first(),
        );
        $this->assertEquals(
            $classTeacher->fresh(),
            $teacher->classTeacherPivot->first(),
        );
    }

    /** @test */
    public function a_student_has_many_class_student_pivot_models()
    {
        $student = User::factory()->create(['default_user_type' => 'student']);
        $classStudent = ClassStudent::factory()->create([
            'student_id' => $student->id,
        ]);

        $this->assertInstanceOf(
            'App\Models\ClassStudent',
            $student->classStudentPivot->first(),
        );
        $this->assertEquals(
            $classStudent->fresh(),
            $student->classStudentPivot->first(),
        );
    }

    /** @test */
    public function a_user_knows_if_it_is_the_authenticated_user()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable */
        $userToAuthenticate = User::factory()->create();

        Auth::login($userToAuthenticate);

        $otherUser = User::factory()->create();

        $this->assertFalse($otherUser->is_this_user_the_auth_user);
        $this->assertTrue($userToAuthenticate->is_this_user_the_auth_user);
    }

    /** @test */
    public function scopes_are_applied_when_called()
    {
        $this->seed();

        User::studentScope()
            ->get()
            ->each(
                fn($item) => $this->assertEquals(
                    $item->default_user_type,
                    'student',
                ),
            );

        User::parentScope()
            ->get()
            ->each(
                fn($item) => $this->assertEquals(
                    $item->default_user_type,
                    'parent',
                ),
            );

        User::teacherScope()
            ->get()
            ->each(
                fn($item) => $this->assertEquals(
                    $item->default_user_type,
                    'teacher',
                ),
            );

        User::administratorScope()
            ->get()
            ->each(
                fn($item) => $this->assertContains($item->default_user_type, [
                    'administrator',
                    'headteacher',
                ]),
            );
    }

    /** @test */
    public function a_user_can_get_props_for_headteacher_dashboard()
    {
        $this->seed();
        $headteacher = User::where('user_type', 'headteacher')->first();
        $props = $headteacher->getPropsForHeadteacherDashboard(Term::find(6));

        $this->assertArrayHasKey('numberOfStudents', $props);
        $this->assertArrayHasKey('numberOfParents', $props);
        $this->assertArrayHasKey('numberOfTeachers', $props);
        $this->assertArrayHasKey('numberOfAdministrators', $props);
        $this->assertArrayHasKey('schoolFeesDataForLineChart', $props);
        $this->assertArrayHasKey('totalSchoolFees', $props);
        $this->assertArrayHasKey('totalSchoolFeesCollected', $props);

        $props['studentsWhoOweSchoolFees']->each(function ($item) {
            $this->assertEquals(
                $item->amountOwed,
                $item->schoolFees->first()->amount -
                    $item->schoolFeesPaid->sum('amount'),
            );
        });
    }

    /** @test */
    public function a_user_can_get_props_for_student_dashboard()
    {
        $this->seed();
        $student = User::where('user_type', 'student')->first();
        $props = $student->getPropsForStudentDashboard(Term::find(6));

        $this->assertArrayHasKey('parents', $props);
        $this->assertArrayHasKey('classesWithTerms', $props);
        $this->assertArrayHasKey('classModel', $props);
        $this->assertArrayHasKey('classTeacher', $props);
        $this->assertArrayHasKey('gradesDataForLineChart', $props);
        $this->assertArrayHasKey('gradesDataPerSubjectForLineChart', $props);
        $this->assertArrayHasKey('totalSchoolFees', $props);
        $this->assertArrayHasKey('totalSchoolFeesPaid', $props);
        $this->assertArrayHasKey('positionInClass', $props);
        $this->assertArrayHasKey(
            'positionStatisticsOfAllStudentsInClass',
            $props,
        );
        $this->assertArrayHasKey('numberOfStudentsInClass', $props);
        $this->assertArrayHasKey('averageMark', $props);
        $this->assertArrayHasKey('gradeForAverageMark', $props);
        $this->assertArrayHasKey('subjectsAndGrades', $props);
    }

    /** @test */
    public function a_user_can_get_props_for_teacher_dashboard()
    {
        $this->seed();
        $teacher = User::where('user_type', 'student')->first();
        $props = $teacher->getPropsForTeacherDashboard(Term::find(6));

        $this->assertArrayHasKey('classes', $props);
        $this->assertArrayHasKey('classModel', $props);
        $this->assertArrayHasKey('studentsInClass', $props);
    }

    /** @test */
    public function a_user_can_get_props_for_parent_dashboard()
    {
        $this->seed();
        $parent = User::where('user_type', 'parent')->first();
        $props = $parent->getPropsForParentDashboard(Term::find(6));

        $this->assertArrayHasKey('showTerm', $props);
        $this->assertArrayHasKey('children', $props);
    }

    /** @test */
    public function a_student_knows_how_much_school_fees_he_has_paid()
    {
        $student = User::factory()->create(['default_user_type' => 'student']);
        $notStudent = User::factory()->create([
            'default_user_type' => 'parent',
        ]);
        $feesPaid = SchoolFeesPaid::factory(3)->create([
            'student_id' => $student->id,
        ]);

        // $this->expectException('LogicException');
        // $notStudent->schoolFeesPaid;

        $this->assertTrue($student->schoolFeesPaid->contains($feesPaid[0]));
        $this->assertInstanceOf(
            'App\Models\SchoolFeesPaid',
            $student->schoolFeesPaid[0],
        );
        $this->assertEquals(3, $student->schoolFeesPaid->count());
    }

    public function assert_overall_grades_data_for_line_chart(
        $grades,
        $chartData,
        $subjectName = null,
    ) {
        foreach ($chartData['gradesDataForStudent'] as $value) {
            $this->assertEquals(
                $grades
                    ->where(
                        'class_name_and_suffix',
                        $value['class_name_and_suffix'],
                    )
                    ->average('overall_mark'),
                $value['average_mark'],
            );
        }

        $termIds = $grades
            ->pluck('term_id')
            ->unique()
            ->values();
        $classNames = $grades
            ->pluck('class_name')
            ->unique()
            ->values();
        $classSuffixes = $grades
            ->pluck('class_suffix')
            ->unique()
            ->values();

        foreach ($chartData['gradesDataForOtherStudents'] as $value) {
            $this->assertEquals(
                Grade::whereIn('term_id', $termIds)
                    ->whereIn('class_name', $classNames)
                    ->whereIn('class_suffix', $classSuffixes)
                    ->when($subjectName, function ($query, $subjectName) {
                        $query->where('subject_name', $subjectName);
                    })
                    ->get()
                    ->where(
                        'class_name_and_suffix',
                        $value['class_name_and_suffix'],
                    )
                    ->average('overall_mark'),
                $value['average_mark'],
            );
        }
    }

    /** @test */
    public function a_student_can_get_overall_grades_data_for_line_chart()
    {
        $this->seed();

        $student = User::where('user_type', 'student')->first();

        $chartData = $student->getOverallGradesDataForLineChart();

        $grades = $student->grades;

        $this->assert_overall_grades_data_for_line_chart($grades, $chartData);
    }

    /** @test */
    public function a_student_can_get_overall_grades_data_per_subject_for_line_chart()
    {
        $this->seed();

        $student = User::where('user_type', 'student')->first();

        $chartData = $student->getOverallGradesDataPerSubjectForLineChart();

        $grades = $student->grades;

        $subjectNames = $grades
            ->pluck('subject_name')
            ->unique()
            ->values();

        foreach ($subjectNames as $subjectName) {
            $this->assert_overall_grades_data_for_line_chart(
                $grades->where('subject_name', $subjectName),
                $chartData[$subjectName],
                $subjectName,
            );
        }
    }

    public User $student;
    public ClassModel $classModel;
    public int $termId;

    public function seedAndSetUpStudent()
    {
        $this->seed();

        $academicYearId = 6; //for class 6 results

        $this->student = User::where('user_type', 'student')->first();
        $this->classModel = $this->student
            ->classes()
            ->where('academic_year_id', $academicYearId)
            ->first();
        $this->termId = AcademicYear::find($academicYearId)->terms->first()->id;
        $this->student->classModel = $this->classModel;
        $this->student->termId = $this->termId;
    }

    /** @test */
    public function get_all_students_and_their_grades_in_class()
    {
        $this->seedAndSetUpStudent();

        $studentsInClass = $this->classModel->load('students')->students; //dd($studentsInClass->toArray());

        $studentsAndGrades = $this->student->getAllStudentsAndTheirGradesInClass();

        $studentsInClass->each(
            fn($item, $key) => $this->assertEquals(
                $item->load([
                    'grades' => fn($query) => $query->where([
                        ['school_id', $this->student->school_id],
                        ['term_id', $this->termId],
                        ['class_name', $this->classModel->name],
                        ['class_suffix', $this->classModel->suffix],
                    ]),
                ])->grades,
                $studentsAndGrades->find($item->id)->grades,
            ),
        );
    }

    /** @test */
    public function a_student_can_get_position_statistics_of_all_students_in_class()
    {
        $this->seedAndSetUpStudent();

        $studentsAndGrades = $this->student->getAllStudentsAndTheirGradesInClass();
        $positionStats = $this->student->getPositionStatisticsOfAllStudentsInClass();

        $this->assertEquals(
            $studentsAndGrades
                ->sortByDesc(function ($item, $key) {
                    return $item->grades->sum('overall_mark');
                })
                ->values()
                ->pluck('id'),

            $positionStats->pluck('id'),
        );

        $nf = new \NumberFormatter('en_US', \NumberFormatter::ORDINAL);

        $positionStats->each(function ($item, $key) use ($nf) {
            $averageMark = round($item->grades->average('overall_mark'), 2);
            $this->assertEquals($item->position, $nf->format($key + 1));
            $this->assertEquals($item->averageMark, $averageMark);
            $this->assertEquals(
                $item->averageGrade,
                $this->student->school->getGradeForMark($averageMark),
            );
        });
    }

    /** @test */
    public function a_student_can_get_position_in_class()
    {
        $this->seedAndSetUpStudent();

        $nf = new \NumberFormatter('en_US', \NumberFormatter::ORDINAL);

        $this->assertEquals(
            $nf->format(
                $this->student
                    ->getPositionStatisticsOfAllStudentsInClass()
                    ->search(function ($item, $key) {
                        return $item->id === $this->student->id;
                    }) + 1,
            ),
            $this->student->getPositionInClass(),
        );
    }

    /** @test */
    public function a_student_can_get_total_number_of_students_in_class()
    {
        $this->seedAndSetUpStudent();

        $this->assertEquals(
            $this->student->getAllStudentsAndTheirGradesInClass()->count(),
            $this->student->getTotalNumberOfStudentsInClass(),
        );
    }

    /** @test */
    public function a_student_can_get_average_mark_of_student_in_class()
    {
        $this->seedAndSetUpStudent();

        $this->assertEquals(
            $this->student
                ->getPositionStatisticsOfAllStudentsInClass()
                ->firstWhere('id', $this->student->id)->averageMark,
            $this->student->getAverageMarkOfStudentInClass(),
        );
    }

    /** @test */
    public function a_student_can_get_all_its_subjects_and_grades()
    {
        $this->seedAndSetUpStudent();

        $subjectsAndGrades = $this->student->getSubjectsAndGrades();
        $nf = new \NumberFormatter('en_US', \NumberFormatter::ORDINAL);

        $subjectsAndGrades->each(function ($item, $key) use ($nf) {
            $this->assertEquals(
                $nf->format(
                    Grade::where([
                        ['school_id', $this->student->school_id],
                        ['term_id', $this->termId],
                        ['class_name', $this->classModel->name],
                        ['class_suffix', $this->classModel->suffix],
                        ['subject_name', $item->subject_name],
                    ])
                        ->get()
                        ->sortByDesc('overall_mark')
                        ->values()
                        ->search(function ($itemToSearch, $keyToSearch) {
                            return $itemToSearch->student_id ===
                                $this->student->id;
                        }) + 1,
                ),
                $item->position,
            );
            $this->assertEquals(
                $this->student
                    ->load('school')
                    ->school->getGradeForMark($item->overall_mark),
                $item->overall_grade,
            );
        });
    }

    /** @test */
    public function a_student_belongs_to_many_classes()
    {
        $this->seedAndSetUpStudent();

        $this->assertInstanceOf(
            'App\Models\ClassModel',
            $this->student->classes[0],
        );
    }

    /** @test */
    public function a_teacher_belongs_to_many_classes()
    {
        $class = ClassModel::factory()->create();
        $teacher = User::factory()->create([
            'default_user_type' => 'teacher',
            'user_type' => 'teacher',
        ]);

        DB::table('class_teacher')->insert([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'academic_year_id' => AcademicYear::factory()->create()->id,
        ]);

        $this->assertInstanceOf(
            'App\Models\ClassModel',
            $teacher->classes->first(),
        );
    }

    /** @test */
    public function a_student_has_many_grades()
    {
        $this->seedAndSetUpStudent();

        $this->assertInstanceOf('App\Models\Grade', $this->student->grades[0]);
    }

    /** @test */
    public function a_student_knows_how_much_school_fees_he_has()
    {
        $student = User::factory()->create(['default_user_type' => 'student']);
        $notStudent = User::factory()->create([
            'default_user_type' => 'parent',
        ]);
        $fees = SchoolFees::factory(3)->create([
            'student_id' => $student->id,
        ]);

        // $this->expectException('LogicException');
        // $notStudent->schoolFeesPaid;

        $this->assertTrue($student->schoolFees->contains($fees[0]));
        $this->assertInstanceOf(
            'App\Models\SchoolFees',
            $student->schoolFees[0],
        );
        $this->assertEquals(3, $student->schoolFees->count());
    }
}
