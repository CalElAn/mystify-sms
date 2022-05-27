<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Term extends Model
{
    use HasFactory;

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function formattedName(): Attribute
    {
        $name = ucfirst($this->name);

        return Attribute::make(
            get: fn() => "{$this->academicYear->name}, {$name} ({$this->start_date->format(
                'jS M',
            )} - {$this->end_date->format('jS M')})",
        );
    }

    public function formattedShortName(): Attribute
    {
        $name = ucfirst($this->name);

        return Attribute::make(
            get: fn() => "{$this->academicYear->name}, {$name}",
        );
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
