<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CapillaryFieldMap extends Model
{
    protected $fillable = [
        'user_id',
        'mapping_role',
        'status',
        'field_type',
        'shopify_field',
        'capillary_field',
    ];
}
