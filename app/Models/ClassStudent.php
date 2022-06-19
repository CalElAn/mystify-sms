<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Model;

class ClassStudent extends Model
{
    use HasFactory;
    protected $table = 'class_student';
    public $incrementing = true;

    protected $guarded = [];

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'id');
    }

    public function student()
    {
        //TODO test
        return $this->belongsTo(User::class, 'student_id', 'id');
    }

    public function academicYear()
    {
        //TODO test
        return $this->belongsTo(AcademicYear::class, 'academic_year_id', 'id');
    }

    // public function terms()
    // {
    //     // return $this->hasManyThrough(Term::class, AcademicYear::class, 'id', 'academic_year_id', 'id', 'id');
    //     return $this->hasMany(Term::class, 'academic_year_id', 'academic_year_id');
    // }
}
