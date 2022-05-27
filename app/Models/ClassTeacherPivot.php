<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassTeacherPivot extends Model
{
    use HasFactory;
    protected $table = 'class_teacher_pivot';
    public $incrementing = true;
}
