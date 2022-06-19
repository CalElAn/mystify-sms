<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ClassModel extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'classes';

    protected $appends = ['name_and_suffix'];

    protected function nameAndSuffix(): Attribute
    {
        //TODO test
        return Attribute::make(
            get: fn() => "{$this->name} {$this->suffix}"
        );
    }

    public function students()
    {
        return $this->belongsToMany(
            User::class,
            'class_student',
            'class_id',
            'student_id',
        )
            ->withPivot('academic_year_id', 'id')
            ->withTimestamps();
    }

    public function teachers()
    {
        return $this->belongsToMany(
            User::class,
            'class_teacher',
            'class_id',
            'teacher_id',
        )
            ->withPivot('academic_year_id')
            ->withTimestamps();
    }
}
