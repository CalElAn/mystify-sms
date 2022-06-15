<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;

class AcademicYear extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function terms()
    {
        return $this->hasMany(Term::class);
    }

    public function formattedName(): Attribute
    {
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);

        return Attribute::make(
            get: fn() => "{$this->name} ({$startDate->format(
                'jS M',
            )} - {$endDate->format('jS M')})",
        );
    }
}
