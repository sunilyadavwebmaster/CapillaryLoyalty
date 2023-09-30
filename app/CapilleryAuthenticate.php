<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CapilleryAuthenticate extends Model
{
    protected $fillable = [
        'user_id',
        'app_key',
        'client_secret_key',
        'base_url',
    ];
   
}
