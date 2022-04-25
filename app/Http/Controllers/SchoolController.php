<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

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
     * @param  \App\Models\School  $school
     * @return \Illuminate\Http\Response
     */
    public function showDashboard()
    {
        /** @var \App\Models\User */
        // $authUser = Auth::user();
        $authUser = User::where('user_type', 'student')->first();

        /** @var \App\Models\School */
        $school = $authUser->school;

        $academicYearId = 6; //TODO change to request->term
        $termId = 1; //TODO change to request->term
        // $className = 'Class 6'; //TODO change to request->term
        // $classSuffix = 'A'; //TODO change to request->term

        $defaultProps = [
            'school' => $school,
            'noticeBoardMessages' 
                => $school
                    ->noticeBoard()
                    ->where('term_id', $termId)
                    ->latest()
                    ->get()
                    ->groupBy(function ($item) {
                        return "{$item->created_at->format('l\, jS F Y')} ...({$item->created_at->diffForHumans()})";
                    }),
        ];

        switch ($authUser->user_type) {
            case 'headteacher':
                $component = 'Headteacher';
                $props = [
                    'numberOfStudents' => $school->getStudents()->count(),
                    'numberOfParents' => $school->getParents()->count(),
                    'numberOfTeachers' => $school->getTeachers()->count(),
                    'numberOfAdministrators' => $school->getAdministrators()->count(),
                    'schoolFeesDataForLineChart' => $school->getSchoolFeesDataForLineChart($academicYearId),
                    'totalSchoolFees' => round($school->schoolFees()->where('academic_year_id', $academicYearId)->sum('amount'), 2),
                    'totalSchoolFeesCollected' => round($school->schoolFeesPaid()->where('academic_year_id', $academicYearId)->sum('amount'), 2),
                ];
                break;
            
            case 'student':
                $component = 'Student';
                $class = $authUser->classes()
                    ->where('academic_year_id', $academicYearId)
                    ->first();
                $authUser->class = $class;
                $authUser->termId = $termId;
                $averageMark = $authUser->getAverageMarkOfStudentInClass();
                $props = [
                    'class' => $class,
                    'gradesDataForLineChart' => $authUser->getOverallGradesDataForLineChart(),
                    'gradesDataPerSubjectForLineChart' => $authUser->getOverallGradesDataPerSubjectForLineChart(),
                    'totalSchoolFees' => round($authUser->schoolFees()->where('academic_year_id', $academicYearId)->sum('amount'), 2),
                    'totalSchoolFeesPaid' => round($authUser->schoolFeesPaid()->where('academic_year_id', $academicYearId)->sum('amount'), 2),
                    'positionInClass' => $authUser->getPositionInClass(),
                    'positionStatisticsOfAllStudentsInClass' => $authUser->getPositionStatisticsOfAllStudentsInClass(),
                    'numberOfStudentsInClass' => $authUser->getTotalNumberOfStudentsInClass(),
                    'averageMark' => $averageMark,
                    'gradeForAverageMark' => $school->getGradeForMark($averageMark),
                    'subjectsAndGrades' => $authUser->getSubjectsAndGrades(),
                ];
            
            default:
                # code...
                break;
        }
        // dd($school->gradingScale->scale);
        // $authUser->arbitraryProperty = 'k;ll;k;l';
        // dd($authUser->arbitraryProperty);
        return Inertia::render("School/Dashboard/{$component}", array_merge($props, $defaultProps));
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
