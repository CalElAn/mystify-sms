<?php

namespace App\Http\Controllers;

use App\Models\ClassTeacher;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class ClassTeacherController extends Controller
{
    /**
     * Show the form for creating or editing a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form(Request $request)
    {
        $this->authorize('viewForm', ClassTeacher::class);

        $user = $request->user();

        return Inertia::render('ClassTeacher/Form', [
            'classTeacherPivotData' => $user
                ->classTeacherPivot()
                ->with(['classModel', 'academicYear'])
                ->get()
                ->sortBy([
                    ['academicYear.name', 'desc'],
                    ['classModel.name', 'desc'],
                ])
                ->values(),
            'classes' => $user->school->classes,
            'academicYears' => $user->school
                ->academicYears()
                ->orderBy('name', 'desc')
                ->get(),
            'defaultAcademicYear' => $user->school->getDefaultAcademicYear(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', ClassTeacher::class);

        $validator = Validator::make(
            $request->all(),
            [
                'class_id' => 'required',
                'academic_year_id' => 'required',
            ],
            [
                'class_id.required' => 'A class is required',
                'academic_year_id.required' => 'An academic year is required',
            ],
        );

        $request
            ->user()
            ->classTeacherPivot()
            ->create($validator->validate());

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClassTeacher  $classTeacher
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClassTeacher $classTeacher)
    {
        $this->authorize('delete', ClassTeacher::class);

        $classTeacher->delete();

        return back();
    }
}
