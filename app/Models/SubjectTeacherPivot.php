<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectTeacherPivot extends Model
{
    use HasFactory;
    protected $table = 'subject_teacher_pivot';
    protected $with = ['classModel', 'term.academicYear'];


    public function classModel()
    {
        return $this->hasOne(ClassModel::class, 'id', 'class_id');
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }
}
