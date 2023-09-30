<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{


    protected $fillable = [
        'user_id',
        'creation',
        'updation',
        'fetch',
        'grouping',
        'add_transaction',
        'enable_points',
        'min_redeem_point',
        'max_redeem_point',
        'multi_redeem_point_claim',
        'cancel_transaction',
        'transaction_mode',
        'mobile_otp',
        'mobile_otp_attribute',
        'email_otp',
        'email_otp_attribute',
        'min_val_progerss_bar',
        'max_val_progerss_bar',
        'total_num_tier',
        'tier_data',
        'pilot_program',
        'pilot_custom_field',
        'pilot_custom_field_value',
        'enable_coupon',
        'group_coupon',
        'country_code_attribute',
        'country_code',
        'check_cart_value',
        'shipping_charges_included',
        'mlp',
        'reward_catalog_enabled',
        'gamification_enabled',
        'brand_name',
        'brand_username',
        'ccms_enabled',
        'show_mlp',
        'show_cumulative_points'
    ];

}
