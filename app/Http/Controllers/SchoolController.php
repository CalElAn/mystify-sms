<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\ClassStudentPivot;
use App\Models\School;
use App\Models\SubjectTeacherPivot;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\School  $school
     * @return \Illuminate\Http\Response
     */
    public function show(School $school)
    {
        //
    }

    /**
     * Show school's dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function dashboard(Request $request)
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        // $user = User::where('user_type', 'student')->first();
        // $user = User::where('user_type', 'teacher')->first();
        // $user = User::where('user_type', 'parent')->first();

        $shouldShowDashboardHeading = false;

        if ($request->userId) {
            $user = User::find($request->userId);
            $shouldShowDashboardHeading = true;
        }

        /** @var \App\Models\School */
        $school = $user->school;

        $academicYearsWithTerms = $school
            ->academicYears()
            ->with('terms')
            ->latest('end_date')
            ->get();

        switch (true) {
            case $request->termId:
                $term = Term::find($request->termId);
                break;

            case $request->academicYearId:
                $term = AcademicYear::find($request->academicYearId)
                    ->terms()
                    ->latest('end_date')
                    ->first();
                break;

            default:
                $term = $academicYearsWithTerms
                    ->first()
                    ->terms->sortByDesc('end_date')
                    ->values()
                    ->first();
                break;
        }

        $termId = $term->id;
        $academicYearId = $term->academic_year_id;
        $term->append('formatted_name');

        $defaultProps = [
            'user' => $user,
            'shouldShowDashboardHeading' => $shouldShowDashboardHeading,
            'school' => $school,
            'academicYearsWithTerms' => $academicYearsWithTerms,
            'term' => $term,
            'showTerm' => true,
            'noticeBoardMessages' => $school
                ->noticeBoard()
                ->where('term_id', $termId)
                ->latest()
                ->get()
                ->groupBy(function ($item) {
                    return "{$item->created_at->format(
                        'l\, jS F Y',
                    )} ...({$item->created_at->diffForHumans()})";
                }),
        ];

        switch ($user->user_type) {
            case 'headteacher':
                $component = 'Headteacher';
                $props = $user->getPropsForHeadmasterDashboard($academicYearId);
                break;

            case 'student':
                $component = 'Student';
                $props = $user->getPropsForStudentDashboard(
                    $academicYearId,
                    $termId,
                );
                break;

            case 'teacher':
                $component = 'Teacher';
                $props = $user->getPropsForTeacherDashboard(
                    $academicYearId,
                    $termId,
                );
                break;

            case 'parent': //TODO update test
                $component = 'Parent';
                $props = $user->getPropsForParentDashboard();
                break;

            default:
                # code...
                break;
        }

        return Inertia::render(
            "School/Dashboard/{$component}",
            //order is important so any repeated keys in $props will overwrite the keys in $defaultProps
            array_merge($defaultProps, $props),
        );
    }

    /**
     * Show all requested user types for school.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function users(Request $request)
    {
        //TODO test
        /** @var \App\Models\School */
        $school = Auth::user()->school;
        $userType = $request->userType;

        switch ($userType) {
            case 'students':
                $query = $school
                    ->users()
                    ->studentScope();
                break;

            case 'parents':
                $query = User::parentScope()
                    ->whereHas(
                        'children',
                        fn(Builder $query) => $query->where(
                            'school_id',
                            $school->id,
                        ),
                    );
                break;

            case 'teachers':
                $query = $school
                    ->users()
                    ->teacherScope();
                break;

            case 'administrators':
                $query = $school
                    ->users()
                    ->administratorScope();
                break;

            default:
                # code...
                break;
        }

        if ($request->name) {
            $query->where('name', 'LIKE', "%{$request->name}%");
        }

        return Inertia::render('School/Users', [
            'school' => $school,
            'showTerm' => false,
            'users' => $query->orderBy('name')->paginate(10)->withQueryString(),
            'userType' => $userType,
            'name' => $request->name,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\School  $school
     * @return \Illuminate\Http\Response
     */
    public function edit(School $school)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\School  $school
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, School $school)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\School  $school
     * @return \Illuminate\Http\Response
     */
    public function destroy(School $school)
    {
        //
    }
}
