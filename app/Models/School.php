<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $primaryKey = 'school_id';

    public function users()
    {
        return $this->hasMany(User::class, 'school_id', 'school_id');
    }

    public function schoolFeesPaid() //TODO test
    {
        return $this->hasMany(SchoolFeesPaid::class, 'school_id', 'school_id');
    }

    public function schoolFees() //TODO test
    {
        return $this->hasMany(SchoolFees::class, 'school_id', 'school_id');
    }

    public function noticeBoard() //TODO test
    {
        return $this->hasMany(NoticeBoard::class, 'school_id', 'school_id');
    }

    /**
     * @return Illuminate\Support\Collection
     */
    public function getStudents()
    {
        return $this->users()
            ->where('default_user_type', 'student')
            ->get();
    }

    public function getSchoolFeesDataForLineChart($termId) //TODO write test
    {
        $chartData = collect();
        $weekNumber = 1;

        foreach (
            $this->schoolFeesPaid()
                ->where('term_id', $termId)
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
                        'startOfWeekFormat' => $schoolFeesPaidValue->created_at->format('jS F'),
                        'sumOfAmount' => $schoolFeesPaidValue->amount,
                    ]),
                );

                $weekNumber++;
            } else {
                $chartData->firstWhere(
                    'startOfWeek',
                    $startOfWeek,
                )['sumOfAmount'] += $schoolFeesPaidValue->amount;
            }
        }

        return $chartData;
    }

    public function getParents()
    {
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
