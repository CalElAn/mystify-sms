<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;

    protected $primaryKey = 'class_id';
    protected $table = 'classes';

    public function students()
    {
        return $this->belongsToMany(User::class, 'class_student_pivot', 'class_id', 'student_id', 'class_id', 'id')->withPivot('academic_year_id')->withTimestamps();
    }
}
