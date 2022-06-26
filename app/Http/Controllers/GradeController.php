<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Subject;
use App\Models\Term;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GradeController extends Controller
{
    /**
     * Show the form for creating or editing a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form(Request $request)
    {
        $this->authorize('viewForm', Grade::class);

        $user = $request->user();

        return Inertia::render('Grades/Form', [
            'subjects' => Subject::orderBy('name')->get(),
            'classes' => $user->school->classes,
            'academicYears' => $user->school
                ->academicYears()
                ->orderBy('name', 'desc')
                ->get(),
            'defaultAcademicYear' => $user->school->getDefaultAcademicYear(),
        ]);
    }

    public function getStudentsWithGrades(
        ClassModel $classModel,
        Term $term,
        Request $request,
    ) {
        $subjectName = $request->subjectName;

        return [
            'studentsWithGrades' => $classModel
                ->load([
                    'students' => fn($query) => $query
                        ->where('academic_year_id', $term->academic_year_id)
                        ->orderBy('name')
                        ->with([
                            'grades' => fn($query) => $query->where([
                                ['school_id', $request->user()->school_id],
                                ['subject_name', $subjectName],
                                ['class_name', $classModel->name],
                                ['class_suffix', $classModel->suffix],
                                ['term_id', $term->id],
                            ]),
                        ]),
                ])
                ->students->each(function ($item) use (
                    $term,
                    $classModel,
                    $subjectName,
                ) {
                    //having empty grades throws errors, this is because at the frontend,
                    //v-model needs a value to be present for class_mark and exam_mark,
                    //so we create nonEmptyGrades property and use that at the frontend
                    if ($item->grades->isEmpty()) {
                        $item->nonEmptyGrades = [
                            'school_id' => $item->school_id,
                            'student_id' => $item->id,
                            'teacher_id' => null,
                            'term_id' => $term->id,
                            'class_name' => $classModel->name,
                            'class_suffix' => $classModel->suffix,
                            'subject_name' => $subjectName,
                            'class_mark' => null,
                            'exam_mark' => null,
                        ];
                        return;
                    }
                    $item->nonEmptyGrades = $item->grades->first();
                }),
        ];
    }

    /**
     * upsert or delete grade records
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upsert(Request $request)
    {
        $this->authorize('upsert', Grade::class);

        $grades = $request->grades;

        foreach ($grades as $key => $item) {
            if (is_null($item['class_mark']) && is_null($item['exam_mark'])) {
                Grade::where([
                    ['student_id', $item['student_id']],
                    ['school_id', $item['school_id']],
                    ['subject_name', $item['subject_name']],
                    ['class_name', $item['class_name']],
                    ['class_suffix', $item['class_suffix']],
                    ['term_id', $item['term_id']],
                ])->delete();

                unset($grades[$key]);
            }
        }

        Grade::upsert(
            $grades,
            [
                'student_id',
                'subject_name',
                'class_name',
                'class_suffix',
                'term_id',
            ],
            ['teacher_id', 'class_mark', 'exam_mark'],
        );

        return back();
    }
}
