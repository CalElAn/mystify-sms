<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectTeacherPivot extends Model
{
    use HasFactory;
    protected $table = 'subject_teacher_pivot';
    protected $with = ['classModel', 'term'];


    public function classModel()//TODO test
    {
        return $this->hasOne(ClassModel::class, 'class_id', 'class_id');
    }

    public function term()//TODO test
    {
        return $this->hasOne(Term::class, 'term_id', 'term_id');
    }
}
