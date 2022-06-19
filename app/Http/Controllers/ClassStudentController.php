<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\ClassStudent;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;

class ClassStudentController extends Controller
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

        return Inertia::render('ClassStudent/Form', [
            'classes' => $user->school->classes,
            'academicYears' => $user->school
                ->academicYears()
                ->orderBy('name', 'desc')
                ->get(),
        ]);
    }

    public function students(ClassModel $classModel, AcademicYear $academicYear)
    {
        //TODO authorize
        //TODO test

        return [
            'students' => $classModel->load([
                'students' => fn($query) => $query
                    ->where('academic_year_id', $academicYear->id)
                    ->orderBy('name'),
            ])->students,
            // 'students' => $request
            //     ->user()
            //     ->school->users()
            //     ->studentScope()
            //     ->orderBy('name')
            //     ->whereHas(
            //         'classStudentPivot',
            //         fn(Builder $query) => $query
            //             ->where([
            //                 ['academic_year_id', $academicYear->id],
            //                 ['class_id', $classModel->id],
            //             ]),
            //     )
            //     ->get(),
        ];
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
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClassStudent  $classStudent
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClassStudent $classStudent)
    {
        //TODO test
        //TODO authorize
        $classStudent->delete();

        return back();
    }
}
