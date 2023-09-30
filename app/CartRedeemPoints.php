<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CartRedeemPoints extends Model
{
    protected $fillable = [
        'id','points','token','mlp_id'
    ];
}
