<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ClassStudentPivot extends Pivot
{
    use HasFactory;
    protected $table = 'class_student_pivot';
    public $incrementing = true;

    // protected $with = ['terms'];

    public function classModel()
    {
        return $this->hasOne(ClassModel::class, 'class_id', 'class_id');
    }

    public function terms() //TODO test
    {
        return $this->hasMany(Term::class, 'academic_year_id', 'academic_year_id');
    }
}
