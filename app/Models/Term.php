<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;

class Term extends Model
{
    use HasFactory;

    protected $guarded = [];

    // protected $casts = [
    //     'start_date' => 'date',
    //     'end_date' => 'date',
    // ];

    public function formattedName(): Attribute
    {
        $name = ucfirst($this->name);
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);

        return Attribute::make(
            get: fn() => "{$this->academicYear->name}, {$name} ({$startDate->format(
                'jS M',
            )} - {$endDate->format('jS M')})",
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
