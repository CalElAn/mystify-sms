<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Grade extends Model
{
    use HasFactory;

    protected $primaryKey = 'grade_id';

    protected $appends = ['overall_mark', 'class_name_and_suffix'];

    protected function overallMark(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->class_mark + $this->exam_mark
        );
    }

    protected function classNameAndSuffix(): Attribute
    {
        return Attribute::make(
            get: fn() => "{$this->class_name} {$this->class_suffix}"
        );
    }

    public function school()
    {
        return $this->hasOne(School::class, 'school_id', 'school_id');
    }

    public function student() //TODO test
    {
        return $this->hasOne(User::class, 'id', 'student_id');
    }
}
