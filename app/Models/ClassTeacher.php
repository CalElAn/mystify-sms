<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ClassTeacher extends Model
{
    use HasFactory;
    protected $table = 'class_teacher';
    public $incrementing = true;

    protected $guarded = [];

    public function classModel()
    {
        //TODO test
        return $this->hasOne(ClassModel::class, 'id', 'class_id');
    }

    public function academicYear()
    {
        //TODO test
        return $this->hasOne(AcademicYear::class, 'id', 'academic_year_id');
    }
}
