<?php

namespace App\Http\Controllers;

use App\CapillaryFieldMap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class FieldMapController extends Controller
{
    public function CustomerFields() {
        $user = Auth::user();
        $data = CapillaryFieldMap::where('user_id',$user->id)->where('mapping_role','customer')->get();
        return view('customerMap')->with('data',$data);
    }

    public function TransactionFields() {
        $user = Auth::user();
        $data = CapillaryFieldMap::where('user_id',$user->id)->where('mapping_role','transaction')->get();
        return view('transactionMap')->with('data',$data);
    }

    public function capillaryFieldsForm($type='',$id='') {
        if($id != 0 && $id != ''){
            $data = CapillaryFieldMap::find($id);
            return view('fieldMapForm')->with('data',$data);
        }else {
            return view('fieldMapForm')->with('data_type',$type);
        }
    }

    public function capillaryFieldsEdit(Request $request, $id = '') {
        // echo $request->id;
        if(!empty($request->id)){
            // echo $request->id; die;
            $request->validate([ 
                'shopify_field' =>'required',
                'capillary_field'=>'required'
            ]);
            $capillaryFieldsMap = CapillaryFieldMap::where('id',$request->id)->first();
            $data = array(
                'status'=>$request->status,
                'field_type'=> $request->field_type,
                'shopify_field'=> $request->shopify_field,
                'capillary_field'=> $request->capillary_field
            );
            $capillaryFieldsMap->update($data);
            if($capillaryFieldsMap->mapping_role == 'transaction'){
                return route('capillary.transaction_attribute');
            }else {
                return route('capillary.customer_attribute');
            }
            // return route('capillary.customer_attribute');
        } elseif(empty($request->id) && $request->has('_token')) {
            $request->validate([ 
                'shopify_field' =>'required',
                'capillary_field'=>'required'
            ]);
            $user = Auth::user();
            $FieldMap = new CapillaryFieldMap;
            $FieldMap->user_id = $user->id;
            $FieldMap->mapping_role = $request->data_type;
            $FieldMap->status = $request->status;
            $FieldMap->field_type = $request->field_type;
            $FieldMap->shopify_field = $request->shopify_field;
            $FieldMap->capillary_field = $request->capillary_field;
            $FieldMap->save();
            if($request->data_type == 'transaction'){
                return route('capillary.transaction_attribute');
            }elseif ($request->data_type == 'customer') {
                return route('capillary.customer_attribute');
            }
            
        }
    }

    public function deleteAttribute($id) {
        $capillaryFieldsMap = CapillaryFieldMap::where('id',$id)->first();
        $mapping_role = $capillaryFieldsMap->mapping_role;
        $capillaryFieldsMap = CapillaryFieldMap::where('id', $id)->firstorfail()->delete();
        if($mapping_role == 'transaction'){
            return redirect()->route('capillary.transaction_attribute');
        }else {
            return redirect()->route('capillary.customer_attribute');
        }
    }
}
