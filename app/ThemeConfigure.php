<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ThemeConfigure extends Model
{
    protected $fillable = [
        'user_id',
        'status',
    ];
}
