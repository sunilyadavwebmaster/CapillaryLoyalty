<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class shopPages extends Model
{
    protected $fillable = [
        'user_id',
        'page_name',
        'page_content',
        'override',
    ];
}
