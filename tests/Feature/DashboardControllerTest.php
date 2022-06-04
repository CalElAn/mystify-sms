<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
use App\Models\User;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_dashboard_can_be_viewed()
    {
        // $this->withoutExceptionHandling();
        $this->seed();

        $this->get('/dashboard')->assertRedirect();

        /** @var \Illuminate\Contracts\Auth\Authenticatable */
        $user = User::factory()->create(['email_verified_at' => null]);
        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect();

        //Headteacher asserions
        $this->assertHeadteacherDashboardProps(
            User::where('user_type', 'headteacher')->first(),
        );

        //Student asserions
        $this->assertStudentDashboardProps(
            User::where('user_type', 'student')->first(),
        );

        //Teacher asserions
        $this->assertTeacherDashboardProps(
            User::where('user_type', 'teacher')->first(),
        );

        //Parent asserions
        $this->assertParentDashboardProps(
            User::where('user_type', 'parent')->first(),
        );
    }

    public function assertHeadteacherDashboardProps($user)
    {
        $this->actingAs($user)
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

        $this->assertDefaultProps($user);
    }

    public function assertStudentDashboardProps($user)
    {
        $this->actingAs($user)
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

        $this->assertDefaultProps($user);
    }

    public function assertTeacherDashboardProps($user)
    {
        $this->actingAs($user)
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

        $this->assertDefaultProps($user);
    }

    public function assertParentDashboardProps($user)
    {
        $this->actingAs($user)
            ->get('dashboard')
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Dashboard/Parent')
                    ->hasAll(
                        'showTerm',
                        'children',
                    ),
            );

        $this->assertDefaultProps($user);
    }

    public function assertDefaultProps($user)
    {
        $this->actingAs($user)
            ->get('dashboard')
            ->assertInertia(
                fn(Assert $page) => $page->hasAll(
                    'user',
                    'shouldShowDashboardHeading',
                    'school',
                    'academicYearsWithTerms',
                    'term',
                    'showTerm',
                    'noticeBoardMessages',
                ),
            );
    }
}
