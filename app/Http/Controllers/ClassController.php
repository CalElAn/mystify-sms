<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{
    /**
     * Show all classes and associated class teacher in the school.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /** @var \App\Models\School */
        $school = Auth::user()->school;

        $academicYearsWithTerms = $school->getAcademicYearsWithTerms();

        $term = $school->getTerm($request);

        $classes = $school
            ->classes()
            ->with([
                'teachers' => fn($query) => $query->where(
                    'academic_year_id',
                    $term->academic_year_id,
                ),
                // 'teachers.subjects',
            ])
            ->get();
        $classes->each(
            fn($item) => $item->teachers->first()?->append('unique_subjects'),
        );

        return Inertia::render('Class/Index', [
            'school' => $school,
            'academicYearsWithTerms' => $academicYearsWithTerms,
            'showTerm' => true,
            'term' => $term,
            'classes' => $classes,
        ]);
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
     * @param  \App\Models\ClassModel  $classModel
     * @return \Illuminate\Http\Response
     */
    public function show(ClassModel $classModel, Request $request)
    {
        /** @var \App\Models\School */
        $school = Auth::user()->school;

        $academicYearsWithTerms = $school->getAcademicYearsWithTerms();
        $term = $school->getTerm($request);

        $classModel = $classModel->load([
            'teachers' => fn($query) => $query->where(
                'academic_year_id',
                $term->academic_year_id,
            ),
            'students' => fn($query) => $query->where(
                'academic_year_id',
                $term->academic_year_id,
            ),
        ]);

        $classModel->teachers->first()?->append('unique_subjects');

        return Inertia::render('Class/Show', [
            'school' => $school,
            'academicYearsWithTerms' => $academicYearsWithTerms,
            'showTerm' => true,
            'term' => $school->getTerm($request),
            'classModel' => $classModel,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ClassModel  $classModel
     * @return \Illuminate\Http\Response
     */
    public function edit(ClassModel $classModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClassModel  $classModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClassModel $classModel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClassModel  $classModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClassModel $classModel)
    {
        //
    }
}
