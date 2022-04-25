<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class GradingScale extends Model
{
    use HasFactory;

    protected $primaryKey = 'grading_scale_id';

    protected $casts = [
        'scale' => AsCollection::class,
    ];
}
