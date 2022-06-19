<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Http\Requests\StoreOrUpdateClassModelRequest;
use Illuminate\Support\Facades\Auth;

class ClassModelController extends Controller
{
    /**
     * Show all classes and associated class teacher in the school.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', ClassModel::class);

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
                'teachers.subjects',
            ])
            ->get();
        $classes->each(
            fn($item) => $item->teachers->first()?->append('unique_subjects'),
        );

        return Inertia::render('Classes/Index', [
            'academicYearsWithTerms' => $academicYearsWithTerms,
            'term' => $term,
            'classes' => $classes,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClassModel  $classModel
     * @return \Illuminate\Http\Response
     */
    public function show(ClassModel $classModel, Request $request)
    {
        $this->authorize('viewAny', ClassModel::class);

        /** @var \App\Models\School */
        $school = Auth::user()->school;

        $academicYearsWithTerms = $school->getAcademicYearsWithTerms();
        $term = $school->getTerm($request);

        $classModel = $classModel->load([
            'teachers' => fn($query) => $query->where(
                'academic_year_id',
                $term->academic_year_id,
            ),
            'students' => fn($query) => $query
                ->where('academic_year_id', $term->academic_year_id)
                ->orderBy('name'),
        ]);

        $classModel->teachers->first()?->append('unique_subjects');

        return Inertia::render('Classes/Show', [
            'academicYearsWithTerms' => $academicYearsWithTerms,
            'term' => $school->getTerm($request),
            'classModel' => $classModel,
        ]);
    }

    /**
     * Show the form for creating or editing a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form(Request $request)
    {
        $this->authorize('viewForm', ClassModel::class);

        return Inertia::render('Classes/Form', [
            'classes' => $request
                ->user()
                ->school->classes->sortBy([['name', 'asc'], ['suffix', 'asc']])
                ->values(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrUpdateClassModelRequest $request)
    {
        $request
            ->user()
            ->school->classes()
            ->create($request->validated());

        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClassModel  $classModel
     * @return \Illuminate\Http\Response
     */
    public function update(
        StoreOrUpdateClassModelRequest $request,
        ClassModel $classModel,
    ) {
        $classModel->update($request->validated());

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClassModel  $classModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClassModel $classModel)
    {
        $this->authorize('delete', $classModel);

        $classModel->delete();

        return back();
    }
}
