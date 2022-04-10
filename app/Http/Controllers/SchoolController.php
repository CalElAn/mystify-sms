<?php

namespace App\Http\Controllers;

use App\Models\School;
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
        $authUser = Auth::user();

        $userType = $authUser->user_type;
        $school = $authUser->school;
        $termId = 1;

        switch ($userType) {
            case 'headteacher':
                $component = 'Headteacher';
                $props = [
                    'school' => $school,
                    'numberOfStudents' => $school->getStudents()->count(),
                    'numberOfParents' => $school->getParents()->count(),
                    'numberOfTeachers' => $school->getTeachers()->count(),
                    'numberOfAdministrators' => $school->getAdministrators()->count(),
                    'schoolFeesDataForLineChart' => $school->getSchoolFeesDataForLineChart($termId),
                    'totalSchoolFees' => round($school->schoolFees()->where('term_id', $termId)->sum('amount'), 2),
                    'totalSchoolFeesCollected' => round($school->schoolFeesPaid()->where('term_id', $termId)->sum('amount'), 2),
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
                break;
            
            default:
                # code...
                break;
        }

        return Inertia::render("School/Dashboard/{$component}", $props);
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
