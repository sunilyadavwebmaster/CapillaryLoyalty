<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\CapilleryAuthenticate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
 
class AuthenticateController extends Controller
{
    
    public function index($id) {        
        $user = Auth::user();
        $authenticate = CapilleryAuthenticate::where('user_id',$user->id)->first();
        $data = Customer::where('user_id', $user->id)->first();
        if (!$data) {
            $customer = new Customer;
            $customer->user_id = $user->id;
            $customer->creation = 1;
            $customer->updation = 1;
            $customer->fetch = 1;
            $customer->grouping = 0;
            $customer->add_transaction = 1;
            $customer->enable_points = 1;
            $customer->min_redeem_point = 5;
            $customer->max_redeem_point = 35;
            $customer->multi_redeem_point_claim = 2;
            $customer->cancel_transaction = 1;
            $customer->transaction_mode = 1;
            $customer->mobile_otp = 0;
            $customer->mobile_otp_attribute = 'RegisterForm-Phone';
            $customer->email_otp = 0;
            $customer->email_otp_attribute = 'RegisterForm-email';
            $customer->min_val_progerss_bar = 0;
            $customer->max_val_progerss_bar = 1000;
            $customer->total_num_tier = 4;
            $customer->tier_data = '({1,0,100,Free Tier},{2,101,300,Bronze},{3,301,700,Silver},{4,701,100,Gold})';
            $customer->pilot_program = 0;
            $customer->pilot_custom_field = 'pilot';
            $customer->pilot_custom_field_value = 'yes';
            $customer->enable_coupon = 1;
            $customer->group_coupon = 0;
            $customer->country_code=0;
            $customer->country_code_attribute="";
            $customer->show_mlp=0;
            $customer->save();
            $data = Customer::where('user_id', $user->id)->first();
        }
        return view('authenticate')->with('data',$authenticate);
    }

    public function store(Request $request,$id="") {
        $user = Auth::user();
        $request->validate([ 
            'app_key' =>'required',
            'client_secret_key'=>'required',
            'base_url'=>'required'
        ]);
        $authenticateCheck = CapilleryAuthenticate::where('user_id', $user->id)->first();
        if($authenticateCheck){
            $authenticate = CapilleryAuthenticate::where('id', $authenticateCheck->id)->first();
            $data = array(
                'app_key' => $request->app_key,
                'client_secret_key' => $request->client_secret_key,
                'base_url' => $request->base_url
            );
            $authenticate->update($data);
        }else {
            $authenticate = new CapilleryAuthenticate;
            $authenticate->user_id = $user->id;
            $authenticate->app_key = $request->app_key;
            $authenticate->client_secret_key = $request->client_secret_key;
            $authenticate->base_url = $request->base_url;
            $authenticate->save();
        }
        return redirect()->route('shop.capilleryAuthenticate',$user->id);
    }
}
