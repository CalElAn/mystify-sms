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
        //TODO authorize
        //TODO test

        $user = $request->user();

        return Inertia::render('ClassTeacher/Form', [
            'classTeacher' => $user
                ->classTeacherPivot()
                ->with(['classModel', 'academicYear'])
                ->get()
                ->sortBy([
                    ['academicYear.name', 'desc'],
                    ['classModel.name', 'desc'],
                ])
                ->values(),
            'user' => $user,
            'classes' => $user->school->classes,
            'academicYears' => $user->school
                ->academicYears()
                ->orderBy('name', 'desc')
                ->get(),
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
        //TODO test
        //TODO authorize
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
        //TODO test
        //TODO authorize
        $classTeacher->delete();

        return back();
    }
}
