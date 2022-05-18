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
    public function showDashboard(Request $request)
    {
        /** @var \App\Models\User */
        // $authUser = Auth::user();
        $authUser = User::where('user_type', 'student')->first();
        $authUser = User::where('user_type', 'teacher')->first();

        /** @var \App\Models\School */
        $school = $authUser->school()->with('academicYears.terms')->first();

        $academicYearsWithTerms = $school->academicYears()->with('terms')->latest('end_date')->get();

        switch (true) {
            case $request->termId:
                $term = Term::find($request->termId);
                break;

            case $request->academicYearId:
                $term = AcademicYear::find($request->academicYearId)->terms()->latest('end_date')->first();
                break;
            
            default:
                $term = $academicYearsWithTerms->first()->terms->sortByDesc('end_date')->values()->first();
                break;
        }

        $termId = $term->term_id;
        $academicYearId = $term->academic_year_id; 
        $term->append('formatted_name');

        $defaultProps = [
            'school' => $school,
            'term' => $term,
            'academicYearsWithTerms' => $academicYearsWithTerms,
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
                $classesWithTerms = ClassStudentPivot::where('student_id', $authUser->id)->with(['terms', 'classModel'])->get();
                $class = $classesWithTerms
                    ->where('academic_year_id', $academicYearId)
                    ->first()
                    ->classModel;
                $authUser->class = $class;
                $authUser->termId = $termId;
                $averageMark = $authUser->getAverageMarkOfStudentInClass();
                $props = [
                    'classesWithTerms' => $classesWithTerms,
                    'classModel' => $class, //seems "class" is a reserved keyword
                    'classTeacher' => $class->teachers->first(),
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
                break;
            
            case 'teacher':
                $component = 'Teacher';
                $classes = $authUser->classes;
                $class = $classes->where('pivot.academic_year_id', $academicYearId)->first();
                $subjects = $authUser->subjects;
                $subjects->each(fn ($subjectItem) => $subjectItem->term->append('formatted_short_name'));
                $currentSubject = $subjects->where('class_id', $class->class_id)->where('term_id', $termId)->sortByDesc('created_at')->values()->first();
                // $currentSubject->term->append('formatted_short_name');
                $props = [
                    'classes' => $classes,
                    'classModel' => $class,
                    'classTeacher' => User::find($class->pivot->teacher_id),
                    'studentsInClass' => $class->students->where('pivot.academic_year_id', $academicYearId)->sortBy('name')->values(),
                    'subjects' => $subjects,
                    'currentSubject' => $currentSubject,
                ];
                break;
            
            default:
                # code...
                break;
        }
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
