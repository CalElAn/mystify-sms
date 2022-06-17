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

    public function classModel()
    {
        return $this->hasOne(ClassModel::class, 'id', 'class_id');
    }

    public function terms()
    {
        return $this->hasMany(Term::class, 'academic_year_id', 'academic_year_id');
    }
}
