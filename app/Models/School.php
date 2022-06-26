<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
        return $this->hasMany(ClassModel::class);
    }

    public function gradingScale()
    {
        return $this->belongsTo(GradingScale::class);
    }

    public function getNoticeBoardMessages(int $term_id): Collection
    {
        return $this->noticeBoard()
            ->where('term_id', $term_id)
            ->latest()
            ->get()
            ->groupBy(function ($item) {
                //example of returned format: Sunday, 19th June 2022 ...(2 days ago)
                return "{$item->created_at->format(
                    'l\, jS F Y',
                )} ...({$item->created_at->diffForHumans()})";
            });
    }

    public function getAcademicYearsWithTerms(): Collection
    {
        return $this->academicYears()
            ->has('terms')
            ->with('terms')
            ->latest('end_date')
            ->get();
    }

    public function getDefaultAcademicYear(): AcademicYear
    {
        return $this->getDefaultTerm()->academicYear;
    }

    public function getDefaultTerm(): Term
    {
        return $this->getAcademicYearsWithTerms()
            ->first()
            ->terms->sortByDesc('end_date')
            ->values()
            ->first();
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
                $term = $this->getDefaultTerm();
                break;
        }

        return $term->append('formatted_name');
    }

    public function getStudents(): Collection
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

    public function getSchoolFeesDataForLineChart(
        $academicYearId,
    ): \Illuminate\Support\Collection {
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

    public function getParents(): Collection
    {
        return $this->users()
            ->parentScope()
            ->whereHas(
                'children',
                fn(Builder $query) => $query->where('school_id', $this->id),
            )
            ->get();
    }

    public function getTeachers(): Collection
    {
        return $this->users()
            ->teacherScope()
            ->get();
    }

    public function getAdministrators(): Collection
    {
        return $this->users()
            ->administratorScope()
            ->get();
    }
}
