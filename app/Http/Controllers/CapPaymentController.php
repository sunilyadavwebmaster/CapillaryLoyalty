<?php

namespace App\Http\Controllers;

use App\CapillaryPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class CapPaymentController extends Controller
{
    function index() {
        $user = Auth::user();
        $data = CapillaryPayment::where('user_id',$user->id)->get();
        return view('capPayments')->with('data',$data);
    }

    public function dataForm($id='') {
        if($id != 0 && $id != ''){
            $data = CapillaryPayment::find($id);
            return view('capPaymentsEdit')->with('data',$data);
        }else {
            return view('capPaymentsEdit');
        }
    }

    public function paymentSave(Request $request, $id = '') {
        // echo $request->id;
        if(!empty($request->id)){
            // echo $request->id; die;
            $request->validate([ 
                'shopify_payment_method' =>'required',
                'cap_payment_method'=>'required'
            ]);
            $capillaryFieldsMap = CapillaryPayment::where('id',$request->id)->first();
            $data = array(
                'status'=>$request->status,
                'shopify_payment_method'=> $request->shopify_payment_method,
                'cap_payment_method'=> $request->cap_payment_method
            );
            $capillaryFieldsMap->update($data);
            return route('capillary.payments');
        } elseif(empty($request->id) && $request->has('_token')) {
            $request->validate([ 
                'shopify_payment_method' =>'required',
                'cap_payment_method'=>'required'
            ]);
            $user = Auth::user();
            $FieldMap = new CapillaryPayment;
            $FieldMap->user_id = $user->id;
            $FieldMap->status = $request->status;
            $FieldMap->shopify_payment_method = $request->shopify_payment_method;
            $FieldMap->cap_payment_method = $request->cap_payment_method;
            $FieldMap->save();
            return route('capillary.payments');
        }
    }

    public function deletePayment($id) {
        CapillaryPayment::where('id', $id)->firstorfail()->delete();
        return redirect()->route('capillary.payments');
    }
}
