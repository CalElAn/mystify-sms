<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $primaryKey = 'academic_year_id';

    public function terms()
    {
        return $this->hasMany(Term::class, 'academic_year_id', 'academic_year_id');
    }
}
