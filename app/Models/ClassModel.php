<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    protected $table = 'classes';

    public function students()
    {
        return $this->belongsToMany(User::class, 'class_student', 'class_id', 'student_id')->withPivot('academic_year_id')->withTimestamps();
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'class_teacher', 'class_id', 'teacher_id')->withPivot('academic_year_id')->withTimestamps();
    }
}
