<?php

namespace Tests\Feature;

use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
use App\Models\User;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true; //the database will be seeded before every test

    /** @test */
    public function the_dashboard_can_be_viewed()
    {
        // $this->withoutExceptionHandling();
        // $this->seed();

        $this->get('/dashboard')->assertRedirect();

        /** @var \Illuminate\Contracts\Auth\Authenticatable */
        $user = User::factory()->create(['email_verified_at' => null]);
        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect();
    }

    /** @test */
    public function headteacher_dashboard_can_be_viewed()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable */
        $user1 = User::factory()->create(['school_id' => 1, 'default_user_type' => 'headteacher']);

        //No one apart from the particluar headmaster can view his dashboard
        $this->actingAs($user1)
            ->get('dashboard?userId=1')
            ->assertForbidden();

        $headteacher = User::where('user_type', 'headteacher')->first();

        $this->actingAs($headteacher)
            ->get('dashboard')
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Dashboard/Headteacher')
                    ->hasAll(
                        'numberOfStudents',
                        'numberOfParents',
                        'numberOfTeachers',
                        'numberOfAdministrators',
                        'schoolFeesDataForLineChart',
                        'totalSchoolFees',
                        'totalSchoolFeesCollected',
                    ),
            );

        $this->assertDefaultProps($headteacher);
    }

    /** @test */
    public function student_dashboard_can_be_viewed()
    {
        $student = User::where('user_type', 'student')->first();

        /** @var \Illuminate\Contracts\Auth\Authenticatable */
        $unauthorizedStudentUser = User::factory()->create([
            'user_type' => 'student',
            'school_id' => 1,
        ]);
        //a student cannot view another student's dashboard
        $this->actingAs($unauthorizedStudentUser)
            ->get('dashboard?userId=' . $student->id)
            ->assertForbidden();

        /** @var \Illuminate\Contracts\Auth\Authenticatable */
        $userWhoIsNotAStudent = User::where([
            ['user_type', '<>', 'student'],
            ['school_id', 1],
        ])->first();
        //a user who is not a student can view another student's dashboard
        $this->actingAs($userWhoIsNotAStudent)
            ->get('dashboard?userId=' . $student->id)
            ->assertOk();

        /** @var \Illuminate\Contracts\Auth\Authenticatable */
        $unauthorizedParentUser = User::factory()->create([
            'user_type' => 'parent',
        ]);
        //a parent cannot view dashboard of someone who is not its child
        $this->actingAs($unauthorizedParentUser)
            ->get('dashboard?userId=' . $student->id)
            ->assertForbidden();

        //a parent can view the dashboard of his child
        $this->actingAs($student->parents()->first())
            ->get('dashboard?userId=' . $student->id)
            ->assertOk();

        $this->actingAs($student)
            ->get('dashboard')
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Dashboard/Student')
                    ->hasAll(
                        'classesWithTerms',
                        'classModel',
                        'numberOfStudentsInClass',
                        'positionStatisticsOfAllStudentsInClass',
                        'classTeacher',
                        'gradesDataForLineChart',
                        'gradesDataPerSubjectForLineChart',
                        'totalSchoolFees',
                        'totalSchoolFeesPaid',
                        'positionInClass',
                        'averageMark',
                        'gradeForAverageMark',
                        'subjectsAndGrades',
                    ),
            );

        $this->assertDefaultProps($student);
    }

    /** @test */
    public function teacher_dashboard_can_be_viewed()
    {
        $teacher = User::where('user_type', 'teacher')->first();

        /** @var \Illuminate\Contracts\Auth\Authenticatable */
        $unauthorizedTeacherUser = User::factory()->create(['school_id' => 1]);

        //No one apart from the particluar teacher can view the dashboard
        $this->actingAs($unauthorizedTeacherUser)
            ->get('dashboard?userId=' . $teacher->id)
            ->assertForbidden();

        $this->actingAs($teacher)
            ->get('dashboard')
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Dashboard/Teacher')
                    ->hasAll(
                        'classes',
                        'classModel',
                        'studentsInClass',
                        'subjects',
                        'currentSubject',
                        'gradesForCurrentSubjectWithStudent',
                    ),
            );

        $this->assertDefaultProps($teacher);
    }

    /** @test */
    public function parent_dashboard_can_be_viewed()
    {
        $child = User::where('user_type', 'student')->first();
        $parent = $child->parents()->first();

        //a child can view the parent's dashboard
        $this->actingAs($child)
            ->get('dashboard?userId=' . $parent->id)
            ->assertOk();

        /** @var \Illuminate\Contracts\Auth\Authenticatable */
        $unauthorizedUser = User::factory()->create(['school_id' => 1]);

        //No one apart from the particluar parent can view his dashboard
        $this->actingAs($unauthorizedUser)
            ->get('dashboard?userId=' . $parent->id)
            ->assertForbidden();

        $this->actingAs($parent)
            ->get('dashboard')
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Dashboard/Parent')
                    ->hasAll('children'),
            );

        $this->assertDefaultProps($parent);
    }

    public function assertDefaultProps($user)
    {
        $this->actingAs($user)
            ->get('dashboard')
            ->assertInertia(
                fn(Assert $page) => $page->hasAll(
                    'user',
                    'shouldShowDashboardHeading',
                    'academicYearsWithTerms',
                    'term',
                    'noticeBoardMessages',
                ),
            );
    }
}
