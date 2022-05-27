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
        $authUser = Auth::user();
        // $authUser = User::where('user_type', 'student')->first();
        // $authUser = User::where('user_type', 'teacher')->first();
        $authUser = User::where('user_type', 'parent')->first();

        /** @var \App\Models\School */
        $school = $authUser->school;

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

        $termId = $term->id;
        $academicYearId = $term->academic_year_id; 
        $term->append('formatted_name');

        $defaultProps = [
            'school' => $school,
            'academicYearsWithTerms' => $academicYearsWithTerms,
            'term' => $term,
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
                $props = $authUser->getPropsForHeadmasterDashboard($academicYearId);
                break;
            
            case 'student':
                $component = 'Student';
                $props = $authUser->getPropsForStudentDashboard($academicYearId, $termId);
                break;
            
            case 'teacher':
                $component = 'Teacher';
                $props = $authUser->getPropsForTeacherDashboard($academicYearId, $termId);
                break;

            case 'parent':
                $component = 'Parent';
                $props = [];
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
