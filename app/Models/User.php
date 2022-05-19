<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'sex',
        'phone_number',
        'default_user_type',
        'user_type',
        'profile_picture_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public ClassModel $class;
    public int $termId;
    //so as to cache the results in a variable and not re-compute them every time its called
    public ?Collection $allStudentsAndTheirGradesInClass = null;

    public function getOverallGradesDataForLineChart(
        Collection $grades = null,
    ): array {
        if (!$grades) {
            $grades = $this->grades()->get();
        }

        $chartData = [];

        $grades
            ->groupBy('class_name_and_suffix')
            ->each(function ($item, $key) use (&$chartData) {
                $chartData['gradesDataForStudent'][] = [
                    'class_name_and_suffix' => $key,
                    'average_mark' => $item->average('overall_mark'),
                ];
            });

        $termIds = $grades
            ->pluck('term_id')
            ->unique()
            ->values();
        $classNames = $grades
            ->pluck('class_name')
            ->unique()
            ->values();
        $classSuffixes = $grades
            ->pluck('class_suffix')
            ->unique()
            ->values();

        $gradesDataForOtherStudents = $this->school->grades()->whereIn('term_id', $termIds)
            ->get()
            ->whereIn('class_name', $classNames)
            ->whereIn('class_suffix', $classSuffixes);

        $gradesDataForOtherStudents
            ->groupBy('class_name_and_suffix')
            ->each(function ($item, $key) use (&$chartData) {
                $chartData['gradesDataForOtherStudents'][] = [
                    'class_name_and_suffix' => $key,
                    'average_mark' => $item->average('overall_mark'),
                ];
            });

        return $chartData;
    }

    public function getOverallGradesDataPerSubjectForLineChart(): array
    {
        $grades = $this->grades()->get();
        $subjectNames = $grades
            ->pluck('subject_name')
            ->unique()
            ->values();
        $chartData = [];

        foreach ($subjectNames as $subjectName) {
            $chartData[$subjectName] = $this->getOverallGradesDataForLineChart(
                $grades->where('subject_name', $subjectName),
            );
        }

        return $chartData;
    }

    public function getAllStudentsAndTheirGradesInClass(): Collection
    {
        //returns each student in the class with their grades
        if ($this->allStudentsAndTheirGradesInClass) {
            return $this->allStudentsAndTheirGradesInClass;
        }

        $this->allStudentsAndTheirGradesInClass = $this->class->load([
            'students.grades' => function ($query) {
                //the $query constrains only the 'grades' relationship
                $query->where([
                    ['school_id', $this->school_id],
                    ['term_id', $this->termId],
                    ['class_name', $this->class->name],
                    ['class_suffix', $this->class->suffix],
                ]);
            },
        ])->students;

        return $this->allStudentsAndTheirGradesInClass;
    }

    public function getPositionStatisticsOfAllStudentsInClass(): Collection
    {
        $school = $this->school;
        $nf = new \NumberFormatter('en_US', \NumberFormatter::ORDINAL);

        return $this->getAllStudentsAndTheirGradesInClass()
            ->sortByDesc(function ($item, $key) {
                return $item->grades->sum('overall_mark');
            })
            ->values()
            ->each(function ($item, $key) use ($school, $nf) {
                $averageMark = round($item->grades->average('overall_mark'), 2);
                $item->position = $nf->format($key + 1);
                $item->averageMark = $averageMark;
                $item->averageGrade = $school->getGradeForMark($averageMark);
            });
    }

    public function getPositionInClass(): string
    {
        if (
            $this->getPositionStatisticsOfAllStudentsInClass()
                ->find($this->id)
                ->grades->isEmpty()
        ) {
            return 'N/A';
        }

        $nf = new \NumberFormatter('en_US', \NumberFormatter::ORDINAL);

        return $nf->format(
            $this->getPositionStatisticsOfAllStudentsInClass()->search(
                function ($item, $key) {
                    return $item->id === $this->id;
                },
            ) + 1,
        ); //add 1 since position is zero indexed
    }

    public function getTotalNumberOfStudentsInClass(): int
    {
        return $this->getAllStudentsAndTheirGradesInClass()->count();
    }

    public function getAverageMarkOfStudentInClass(): float
    {
        return $this->getPositionStatisticsOfAllStudentsInClass()->firstWhere(
            'id',
            $this->id,
        )->averageMark;
    }

    public function getSubjectsAndGrades(): Collection
    {
        $allGrades = $this->school->grades()->where([
            ['school_id', $this->school_id],
            ['term_id', $this->termId],
            ['class_name', $this->class->name],
            ['class_suffix', $this->class->suffix],
        ])->get();

        $allGradesBySubject = $allGrades->groupBy('subject_name');

        $allGradesBySubjectSorted = collect();

        //form an array of each subject mapped onto its grades sorted by overall_mark
        $allGradesBySubject->each(function ($eachItem, $eachKey) use (
            &$allGradesBySubjectSorted,
        ) {
            $allGradesBySubjectSorted->put(
                $eachKey,
                $eachItem
                    ->sortByDesc(function ($itemToSort, $keyToSort) {
                        return $itemToSort->overall_mark;
                    })
                    ->values(),
            );
        });

        $school = $this->school;
        $nf = new \NumberFormatter('en_US', \NumberFormatter::ORDINAL);

        //return a collection of all this student's grades, with his position and overall grade for each grade record
        return $allGrades
            ->where('student_id', $this->id)
            ->each(function ($item, $key) use (
                $allGradesBySubjectSorted,
                $school,
                $nf,
            ) {
                $item->position = $nf->format(
                    $allGradesBySubjectSorted[$item->subject_name]->search(
                        function ($item, $key) {
                            return $item->student_id === $this->id;
                        },
                    ) + 1,
                );
                $item->overall_grade = $school->getGradeForMark(
                    $item->overall_mark,
                );
            });
    }

    public function classes()
    {
        if ($this->default_user_type === 'student') {
            return $this->belongsToMany(
                ClassModel::class,
                'class_student_pivot',
                'student_id',
                'class_id',
                'id',
                'class_id',
            )
                ->withPivot('academic_year_id')
                ->withTimestamps();
        } elseif ($this->default_user_type === 'teacher') {
            return $this->belongsToMany(
                ClassModel::class,
                'class_teacher_pivot',
                'teacher_id',
                'class_id',
                'id',
                'class_id',
            )
                ->withPivot('academic_year_id', 'teacher_id')
                ->withTimestamps();
        }
        return null;
    }

    public function subjects()
    {
        //TODO test
        if ($this->default_user_type === 'teacher') {
            return $this->hasMany(
                SubjectTeacherPivot::class,
                'teacher_id',
                'id',
            );
        }
        return null;
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'student_id', 'id');
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'school_id');
    }

    public function schoolFeesPaid()
    {
        if ($this->default_user_type === 'student') {
            return $this->hasMany(SchoolFeesPaid::class, 'student_id', 'id');
        }

        return null;
    }

    public function schoolFees()
    {
        if ($this->default_user_type === 'student') {
            return $this->hasMany(SchoolFees::class, 'student_id', 'id');
        }

        return null;
    }

    public function parents()
    {
        if ($this->default_user_type === 'student') {
            return $this->belongsToMany(
                User::class,
                'parent_student_pivot',
                'student_id',
                'parent_id',
                'id',
                'id',
            )->withTimestamps();
        }

        return null;
    }
}
