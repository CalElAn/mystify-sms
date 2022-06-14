<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\StoreOrUpdateAcademicYearRequest;

class AcademicYearController extends Controller
{
    /**
     * Show the form for creating or editing a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function form(Request $request)
    {
        //TODO authorize
        //TODO test

        return Inertia::render('AcademicYears/Form', [
            'academicYears' => $request
                ->user()
                ->school->academicYears()
                ->latest('end_date')
                ->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrUpdateAcademicYearRequest $request)
    {
        //TODO test

        $request
            ->user()
            ->school->academicYears()
            ->create($request->validated());

        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function update(
        StoreOrUpdateAcademicYearRequest $request,
        AcademicYear $academicYear,
    ) {
        //TODO test
        $academicYear->update($request->validated());

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicYear $academicYear)
    {
        //TODO authorize
        //TODO test
        $academicYear->delete();

        return back();
    }
}
