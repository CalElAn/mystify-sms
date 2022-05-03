<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

    protected $primaryKey = 'term_id';

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function getFormattedName(AcademicYear $academicYear) //TODO test
    {
        $name = ucfirst($this->name);
        return "{$academicYear->name}, {$name} ({$this->start_date->format('jS M')} - {$this->end_date->format('jS M')})";
    }
}
