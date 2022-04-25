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
use Illuminate\Support\Facades\DB;

class SchoolModelTest extends TestCase
{
    use RefreshDatabase;

    protected $school;

    public function setUp() :void
    {
        parent::setUp();

        // $this->withoutExceptionHandling();
        $this->school = School::factory()->create();
    } 

    /** @test */
    public function a_school_has_many_users()
    {
        $user = User::factory(10)->create(['school_id' => $this->school->school_id]);
     
        $this->assertTrue($this->school->users->contains($user[0]));
        $this->assertInstanceOf('App\Models\User', $this->school->users[0]);
        $this->assertEquals(10, $this->school->users->count());
    }

    /** @test */
    public function a_school_has_many_school_fees_paid()
    {
        $student = User::factory(3)->create([
            'default_user_type' => 'student',
            'school_id' => $this->school->school_id
        ]);
        $feesPaid = SchoolFeesPaid::factory(3)->create(['student_id' => $student[0]->id, 'school_id' => $this->school->school_id]);
        SchoolFeesPaid::factory(2)->create(['student_id' => $student[1]->id, 'school_id' => $this->school->school_id]);

        $this->assertTrue($this->school->schoolFeesPaid->contains($feesPaid[0]));
        $this->assertInstanceOf('App\Models\SchoolFeesPaid', $this->school->schoolFeesPaid[0]);
        $this->assertEquals(5, $this->school->schoolFeesPaid->count());
    }

    /** @test */
    public function a_school_has_many_school_fees_owed()
    {
        $student = User::factory(3)->create([
            'default_user_type' => 'student',
            'school_id' => $this->school->school_id
        ]);
        $student->each(function ($item) {
            SchoolFees::factory()->create(['student_id' => $item->id, 'school_id' => $this->school->school_id]);
        });

        $this->assertTrue($this->school->schoolFees->contains(SchoolFees::all()[0]));
        $this->assertInstanceOf('App\Models\SchoolFees', $this->school->schoolFees[0]);
        $this->assertEquals(3, $this->school->schoolFees->count());
    }

    /** @test */
    public function get_school_fees_data_for_line_chart()
    {
        $students = User::factory(3)->create([
            'default_user_type' => 'student',
            'school_id' => $this->school->school_id
        ]);
        $academicYear = AcademicYear::factory()->create();

        $arrayOfSchoolFeesPaid = [[
            "student_id" => $students[0]->id,
            "school_id" => $this->school->school_id,
            "academic_year_id" => $academicYear->academic_year_id,
            "amount" => 26,
            "created_at" => "2022-03-01T04:49:30.000000Z",
        ], [
            "student_id" => $students[1]->id,
            "school_id" => $this->school->school_id,
            "academic_year_id" => $academicYear->academic_year_id,
            "amount" => 27,
            "created_at" => "2022-04-04T01:10:06.000000Z",
        ], [
            "student_id" => $students[2]->id,
            "school_id" => $this->school->school_id,
            "academic_year_id" => $academicYear->academic_year_id,
            "amount" => 72,
            "created_at" => "2022-02-07T00:03:12.000000Z",
        ]];

        foreach ($arrayOfSchoolFeesPaid as $value) {
            SchoolFeesPaid::forceCreate($value);
        }

        $sortedArray = [[
            "weekName" => "Week 1",
            "startOfWeek" => "2022-02-07",
            "startOfWeekFormat" => "7th February",
            "sumOfAmount" => 72,
        ], [
            "weekName" => "Week 2",
            "startOfWeek" => "2022-02-28",
            "startOfWeekFormat" => "1st March",
            "sumOfAmount" => 26,
        ], [
            "weekName" => "Week 3",
            "startOfWeek" => "2022-04-04",
            "startOfWeekFormat" => "4th April",
            "sumOfAmount" => 27,
        ]];

        $this->assertEquals($sortedArray, $this->school->getSchoolFeesDataForLineChart($academicYear->academic_year_id)->toArray());
    }

    /** @test */
    public function a_school_has_many_notice_board_messages()
    {
        $academicYear = AcademicYear::factory()->create();
        $term = Term::factory()->create(['academic_year_id' => $academicYear->academic_year_id]);

        NoticeBoard::factory(3)->create(['school_id' => $this->school->school_id, 'term_id' => $term->term_id]);

        $this->assertTrue($this->school->noticeBoard->contains(NoticeBoard::all()[0]));
        $this->assertInstanceOf('App\Models\NoticeBoard', $this->school->noticeBoard[0]);
        $this->assertEquals(3, $this->school->noticeBoard->count());
    }

    /** @test */
    public function a_school_has_one_grading_scale()
    {
        $gradingScale = GradingScale::factory()->create();
        $this->school->grading_scale_id = $gradingScale->grading_scale_id;
        $this->school->save();
        $this->assertInstanceOf('App\Models\GradingScale', $this->school->gradingScale);
        $this->assertEquals($gradingScale->fresh(), $this->school->gradingScale);
    }

    /** @test */
    public function a_school_can_get_the_grade_for_a_mark()
    {
        
    }
            
    /** @test */
    public function a_school_can_get_its_students()
    {
        $user = User::factory(10)->create([
            'school_id' => $this->school->school_id,
            'default_user_type' => 'student'
        ]);
     
        $this->assertTrue($this->school->getStudents()->contains($user[0]));
        $this->assertInstanceOf('App\Models\User', $this->school->getStudents()[0]);
        $this->assertEquals(10, $this->school->getStudents()->count());
    }

    /** @test */
    public function a_school_can_get_its_parents()
    {
        $student0 = User::factory()->create([
            'school_id' => $this->school->school_id,
            'default_user_type' => 'student'
        ]);
        $student1 = User::factory()->create([
            'school_id' => $this->school->school_id,
            'default_user_type' => 'student'
        ]);
        $student2 = User::factory()->create([
            'school_id' => $this->school->school_id,
            'default_user_type' => 'student'
        ]);
        $parent1 = User::factory()->create([
            'school_id' => $this->school->school_id,
            'default_user_type' => 'parent'
        ]);
        $parent2 = User::factory()->create([
            'school_id' => $this->school->school_id,
            'default_user_type' => 'parent'
        ]);
        $parent3 = User::factory()->create([
            'school_id' => $this->school->school_id,
            'default_user_type' => 'parent'
        ]);
        $parent4 = User::factory()->create([
            'school_id' => $this->school->school_id,
            'default_user_type' => 'parent'
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
            'school_id' => $this->school->school_id,
            'default_user_type' => 'teacher'
        ]);
        
        $this->assertTrue($this->school->getTeachers()->contains($user[0]));
        $this->assertInstanceOf('App\Models\User', $this->school->getTeachers()[0]);
        $this->assertEquals(10, $this->school->getTeachers()->count());
    }

    /** @test */
    public function a_school_can_get_its_administrators()
    {
        $user = User::factory(10)->create([
            'school_id' => $this->school->school_id,
            'default_user_type' => 'school administrator'
        ]);
        
        $this->assertTrue($this->school->getAdministrators()->contains($user[0]));
        $this->assertInstanceOf('App\Models\User', $this->school->getAdministrators()[0]);
        $this->assertEquals(10, $this->school->getAdministrators()->count());
    }   
}
