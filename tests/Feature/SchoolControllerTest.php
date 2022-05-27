<?php

namespace Tests\Feature;

use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use App\Models\User;
use App\Models\Term;
use App\Models\AcademicYear;
use App\Models\ClassStudentPivot;

class SchoolControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_dashboard_can_be_viewed()
    {
        $this->withoutExceptionHandling();
        $this->seed();

        // $this->get('/dashboard')->assertRedirect();

        /** @var \Illuminate\Contracts\Auth\Authenticatable */
        $user = User::factory()->create(['email_verified_at' => null]);
        // $this->actingAs($user)
        //     ->get('/dashboard')
        //     ->assertRedirect();

        $this->assertTermAndDefaultProps(User::all()->random());

        //Headteacher asserions
        $this->assertHeadteacherDashboardProps(User::where('user_type', 'headteacher')->first());

        //Student asserions
        $this->assertStudentDashboardProps(User::where('user_type', 'student')->first());

        //Teacher asserions
        $this->assertTeacherDashboardProps(User::where('user_type', 'teacher')->first());

        // $assertComponent(User::where('user_type', 'teacher')->first(), 'Teacher');
        // $assertComponent(User::where('user_type', 'parent')->first(), 'Parent');
    }

    public function assertHeadteacherDashboardProps($user)
    {
        $this->actingAs($user)
            ->get('dashboard')
            ->assertInertia(
                fn(Assert $page) => $page->component('School/Dashboard/Headteacher')->hasAll(
                    'numberOfStudents',
                    'numberOfParents',
                    'numberOfTeachers',
                    'numberOfAdministrators',
                    'schoolFeesDataForLineChart',
                    'totalSchoolFees',
                    'totalSchoolFeesCollected',
                ),
            );
    }

    public function assertStudentDashboardProps($user)
    {
        $this->actingAs($user)
            ->get('dashboard')
            ->assertInertia(
                fn(Assert $page) => $page
                ->component('School/Dashboard/Student')->hasAll(
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
    }

    public function assertTeacherDashboardProps($user)
    {
        $this->actingAs($user)
            ->get('dashboard')
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('School/Dashboard/Teacher')
                    ->hasAll(
                    'classes',
                    'classModel',
                    'classTeacher',
                    'studentsInClass',
                    'subjects',
                    'currentSubject',
                    'gradesForCurrentSubjectWithStudent',
                ),
            );
    }

    public function assertTermAndDefaultProps($user)
    {
        $school = $user->school;
        $academicYearsWithTerms = $school
            ->academicYears()
            ->with('terms')
            ->latest('end_date')
            ->get();
        $term = $academicYearsWithTerms
            ->first()
            ->terms->sortByDesc('end_date')
            ->values()
            ->first()
            ->append('formatted_name');

        $this->actingAs($user)
            ->get('dashboard')
            ->assertInertia(
                fn(Assert $page) => $page
                    ->where('school', $school)
                    ->where('term', $term)
                    ->where('academicYearsWithTerms', $academicYearsWithTerms)
                    ->where(
                        'noticeBoardMessages',
                        $school
                            ->noticeBoard()
                            ->where('term_id', $term->id)
                            ->latest()
                            ->get()
                            ->groupBy(function ($item) {
                                return "{$item->created_at->format(
                                    'l\, jS F Y',
                                )} ...({$item->created_at->diffForHumans()})";
                            }),
                    ),
            );

        $this->actingAs($user)
            ->get('/dashboard?termId=3')
            ->assertInertia(
                fn(Assert $page) => $page->where(
                    'term',
                    Term::find(3)->append('formatted_name'),
                ),
            );

        $this->actingAs($user)
            ->get('/dashboard?academicYearId=3')
            ->assertInertia(
                fn(Assert $page) => $page->where(
                    'term',
                    AcademicYear::find(3)
                        ->terms()
                        ->latest('end_date')
                        ->first()
                        ->append('formatted_name'),
                ),
            );
    }
}
