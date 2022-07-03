<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\ClassStudent;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
            'defaultAcademicYear' => $user->school->getDefaultAcademicYear(),
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

    public function joinClassForm(Request $request)
    {
        $this->authorize('viewJoinClassForm', ClassStudent::class);

        $user = $request->user();

        return Inertia::render('ClassStudent/JoinClass/Form', [
            'classStudentPivotData' => $user
                ->classStudentPivot()
                ->whereRelation('classModel', 'school_id', $user->school->id)
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
        $this->authorize('viewJoinClassForm', ClassStudent::class);

        $validator = Validator::make(
            $request->all(),
            [
                'class_id' => [
                    'required',
                    Rule::unique('class_student')->where(
                        fn($query) => $query->where([
                            ['student_id', $request->user()->id],
                            ['academic_year_id', $request->academic_year_id],
                        ]),
                    ),
                ],
                'academic_year_id' => 'required',
            ],
            [
                'class_id.required' => 'A class is required',
                'class_id.unique' =>
                    'You cannot be in the same class twice for the same academic year',
                'academic_year_id.required' => 'An academic year is required',
            ],
        );

        $request
            ->user()
            ->classStudentPivot()
            ->create($validator->validate());

        return back();
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
