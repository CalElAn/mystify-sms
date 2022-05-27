<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class GradingScale extends Model
{
    use HasFactory;

    protected $casts = [
        'scale' => AsCollection::class,
    ];
}
