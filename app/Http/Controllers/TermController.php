<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Term;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\StoreOrUpdateTermRequest;

class TermController extends Controller
{
    /**
     * Show the form for creating or editing a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form(Request $request)
    {
        $this->authorize('viewForm', Term::class);

        return Inertia::render('Terms/Form', [
            'academicYears' => $request
                ->user()
                ->school->academicYears->append('formatted_name')
                ->sortByDesc('end_date')
                ->values(),
        ]);
    }

    public function terms(AcademicYear $academicYear)
    {//TODO update test? (the call to appends)
        return [
            'terms' => $academicYear
                ->terms()
                ->latest('end_date')
                ->get()
                ->append('formatted_short_name')
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(
        StoreOrUpdateTermRequest $request,
        AcademicYear $academicYear,
    ) {
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
        StoreOrUpdateTermRequest $request,
        Term $term,
    ) {
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
        $this->authorize('delete', $term);
        
        $term->delete();

        return back();
    }
}
