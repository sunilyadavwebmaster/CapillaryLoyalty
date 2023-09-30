<?php

namespace App\Http\Controllers;

use App\CapilleryAuthenticate;
use App\Customer;
use App\functionClass\CommonFunction;
use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateDomain extends Controller
{
   public function index($id)
    {
        $shop = User::where('id',$id)->first();
        return view('update-domain')->with('data',$shop);;
    }
    public function update(Request $request)
    {
        $shop = Auth::user();
        $data = $request->post('alternate_name');

        if ($this->domainExist($data)) {
            return response()->json(['error' => 'Domain Name already in use']);
        }

        if ($shop) {
            $shop->update(['alternate_name' => $data]);
        }

        return response()->json(['success' => 'Record updated successfully']);
    }

    private function domainExist($domain)
    {
        return User::where('name', $domain)
            ->orWhere('alternate_name', $domain)
            ->exists();
    }
}