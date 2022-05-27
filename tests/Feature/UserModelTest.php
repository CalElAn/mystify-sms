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
use App\Models\Subject;
use App\Models\SubjectTeacherPivot;
use App\Models\Term;
use Illuminate\Support\Facades\DB;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_belongs_to_a_school()
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->school_id]);

        $this->assertInstanceOf('App\Models\School', $user->school);
        $this->assertEquals($school->fresh(), $user->school);
    }

    /** @test */
    public function a_user_who_is_a_student_belongs_to_many_parents()
    {
        $student = User::factory()->create(['default_user_type' => 'student']);
        $parent1 = User::factory()->create(['default_user_type' => 'parent']);
        $parent2 = User::factory()->create(['default_user_type' => 'parent']);

        DB::table('parent_student_pivot')->insert([
            ['student_id' => $student->id, 'parent_id' => $parent1->id],
            ['student_id' => $student->id, 'parent_id' => $parent2->id],
        ]);

        $this->expectException('LogicException');
        $parent1->parents;

        $this->assertTrue($student->parents->contains($parent1));
        $this->assertTrue($student->parents->contains($parent2));
        $this->assertInstanceOf('App\Models\User', $student->parents[0]);
    }

    /** @test */
    public function a_user_who_is_a_teacher_has_many_subjects()
    {
        $teacher = User::factory()->create(['default_user_type' => 'teacher']);
        $student = User::factory()->create(['default_user_type' => 'student']);

        SubjectTeacherPivot::factory()->create(['teacher_id' => $teacher->id]);

        $this->expectException('LogicException');
        $student->subjects;

        $this->assertTrue($teacher->subject->contains(SubjectTeacherPivot::find(1)));
        $this->assertEquals(1, $teacher->subject->count());
        $this->assertInstanceOf('App\Models\SubjectTeacherPivot', $teacher->subjects->first());
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

        $this->expectException('LogicException');
        $notStudent->schoolFeesPaid;

        $this->assertTrue($student->schoolFeesPaid->contains($feesPaid[0]));
        $this->assertInstanceOf(
            'App\Models\SchoolFeesPaid',
            $student->schoolFeesPaid[0],
        );
        $this->assertEquals(3, $student->schoolFeesPaid->count());
    }

    public function get_overall_grades_data_for_line_chart($grades, $chartData)
    {
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

        $this->get_overall_grades_data_for_line_chart($grades, $chartData);
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
            $this->get_overall_grades_data_for_line_chart(
                $grades->where('subject_name', $subjectName),
                $chartData[$subjectName],
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
        $this->termId = AcademicYear::find(
            $academicYearId,
        )->terms->first()->term_id;
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
        $teacher = User::factory()->create(['default_user_type' => 'teacher']);

        DB::table('class_teacher_pivot')->insert([
            'class_id' => $class->class_id,
            'teacher_id' => $teacher->id,
            'academic_year_id' => AcademicYear::factory()->create()
                ->academic_year_id,
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

        $this->expectException('LogicException');
        $notStudent->schoolFeesPaid;

        $this->assertTrue($student->schoolFees->contains($fees[0]));
        $this->assertInstanceOf(
            'App\Models\SchoolFees',
            $student->schoolFees[0],
        );
        $this->assertEquals(3, $student->schoolFees->count());
    }
}
