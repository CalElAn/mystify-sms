<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class NoticeBoard extends Model
{
    use HasFactory;

    protected $table = 'notice_board';

    protected $primaryKey = 'notice_board_id';

    protected $with = ['user'];

    protected $appends = ['time_string'];

    protected function timeString(): Attribute
    {//TODO test
        return Attribute::make(
            get: fn() => Carbon::create($this->created_at)->format('h:i A'),
        );
    }

    public function user() //TODO test
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
