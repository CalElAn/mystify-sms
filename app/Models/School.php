<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function academicYears()
    {
        return $this->hasMany(AcademicYear::class);
    }

    public function schoolFeesPaid()
    {
        return $this->hasMany(SchoolFeesPaid::class);
    }

    public function schoolFees()
    {
        return $this->hasMany(SchoolFees::class);
    }

    public function noticeBoard()
    {
        return $this->hasMany(NoticeBoard::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function classes()
    {
        //TODO test
        return $this->hasMany(ClassModel::class);
    }

    public function gradingScale()
    {
        return $this->belongsTo(GradingScale::class);
    }

    public function getAcademicYearsWithTerms(): Collection
    {
        return $this->academicYears()
            ->with('terms')
            ->latest('end_date')
            ->get();
    }

    public function getTerm(Request $request): Term
    {
        switch (true) {
            case $request->termId:
                $term = Term::find($request->termId);
                break;

            case $request->academicYearId:
                $term = AcademicYear::find($request->academicYearId)
                    ->terms()
                    ->latest('end_date')
                    ->first();
                break;

            default:
                $term = $this->getAcademicYearsWithTerms()
                    ->first()
                    ->terms->sortByDesc('end_date')
                    ->values()
                    ->first();
                break;
        }

        return $term->append('formatted_name');
    }

    public function getStudents()
    {
        return $this->users()
            ->where('default_user_type', 'student')
            ->get();
    }

    public function getGradeForMark(int|float $mark): string
    {
        $mark = round($mark);

        return $this->gradingScale->scale->search(function ($item, $key) use (
            $mark,
        ) {
            return $item[0] <= $mark && $mark <= $item[1]; //checks if item is between start and end rage of each scale
        });
    }

    public function getSchoolFeesDataForLineChart($academicYearId)
    {
        $chartData = collect();
        $weekNumber = 1;

        foreach (
            $this->schoolFeesPaid()
                ->where('academic_year_id', $academicYearId)
                ->oldest()
                ->get()
            as $schoolFeesPaidValue
        ) {
            $startOfWeek = $schoolFeesPaidValue->created_at
                ->startOfWeek()
                ->format('Y-m-d');

            if (
                $chartData->doesntContain(function ($value) use ($startOfWeek) {
                    if (isset($value['startOfWeek'])) {
                        return $value['startOfWeek'] === $startOfWeek;
                    }

                    return false;
                })
            ) {
                $chartData->push(
                    collect([
                        'weekName' => 'Week ' . $weekNumber,
                        'startOfWeek' => $startOfWeek,
                        'startOfWeekFormat' => $schoolFeesPaidValue->created_at->format(
                            'jS F',
                        ),
                        'sumOfAmount' => $schoolFeesPaidValue->amount,
                    ]),
                );

                $weekNumber++;
            } else {
                $chartData->firstWhere('startOfWeek', $startOfWeek)[
                    'sumOfAmount'
                ] += $schoolFeesPaidValue->amount;
            }
        }

        return $chartData;
    }

    public function getParents()
    {
        //TODO rewrite to use scope
        $parents = collect();
        $students = $this->getStudents();

        foreach ($students as $student) {
            foreach ($student->parents as $parentsValue) {
                if (
                    $parents->doesntContain(function ($value, $key) use (
                        $parentsValue,
                    ) {
                        return $value->id === $parentsValue->id;
                    })
                ) {
                    $parents->push($parentsValue);
                }
            }
        }

        return $parents;
    }

    public function getTeachers()
    {
        return $this->users()
            ->where('default_user_type', 'teacher')
            ->get();
    }

    public function getAdministrators()
    {
        return $this->users()
            ->where('default_user_type', 'school administrator')
            ->get();
    }
}
