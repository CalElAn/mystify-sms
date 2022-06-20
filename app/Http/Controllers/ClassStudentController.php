<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\ClassStudent;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClassStudentController extends Controller
{
    /**
     * Show the form for creating or editing a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form(Request $request)
    {
        $this->authorize('viewForm', ClassStudent::class);

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
        return [
            'students' => $classModel->load([
                'students' => fn($query) => $query
                    ->where('academic_year_id', $academicYear->id)
                    ->orderBy('name'),
            ])->students,
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
        $this->authorize('delete', ClassStudent::class);

        $classStudent->delete();

        return back();
    }
}
