<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Term;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\StoreOrUpdateAcademicYearRequest;

class TermController extends Controller
{
    /**
     * Show the form for creating or editing a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form(Request $request)
    {
        //TODO authorize
        //TODO test

        return Inertia::render('Terms/Form', [
            'academicYears' => $request
                ->user()
                ->school->academicYears->append('formatted_name')
                ->sortByDesc('end_date')
                ->values(),
            // ->get(),
        ]);
    }

    public function terms(AcademicYear $academicYear)
    {
        return $academicYear
            ->terms()
            ->latest('end_date')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(
        StoreOrUpdateAcademicYearRequest $request,
        AcademicYear $academicYear,
    ) {
        //TODO test

        $academicYear->terms()->create($request->validated());

        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Term  $term
     * @return \Illuminate\Http\Response
     */
    public function update(
        StoreOrUpdateAcademicYearRequest $request,
        Term $term,
    ) {
        //TODO test
        $term->update($request->validated());

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Term  $term
     * @return \Illuminate\Http\Response
     */
    public function destroy(Term $term)
    {
        //TODO authorize
        //TODO test
        $term->delete();

        return back();
    }
}
