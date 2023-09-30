<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CapillaryPayment extends Model
{
    protected $fillable = [
        'user_id',
        'shopify_payment_method',
        'cap_payment_method',
        'status',
    ];
}
