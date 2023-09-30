<?php

namespace App\Http\Controllers;

use App\CapilleryAuthenticate;
use App\Customer;
use App\functionClass\CommonFunction;
use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RewardCatalogController extends Controller
{
	function getBrandRewards(Request $request, $domain)
	{
		$user = User::where('name', $domain)->orWhere('alternate_name', $domain)->first();
        $customer = Customer::where('user_id', $user->id)->first();
        $auth = CapilleryAuthenticate::where('user_id', $user->id)->first();
        $client = new Client(commonFunction::getClientParams($auth));

        if ($customer->reward_catalog_enabled) {
        	$url = '/'.config('global.api_gateway-rewards-core-v1-user-reward-brand').'/' . $customer->brand_name . '?'.http_build_query($request->query());
        	try {
	            $result = $client->request('GET', $url);
	            return json_decode($result->getBody(), true);
	        } catch (Throwable $e) {
	            \Log::channel('error')->info('Get Brand Rewards Error: ' . $e->getMessage());
	            return $e->getMessage();
	        }
        }
        return false;
	}

	function issueBrandRewards(Request $request, $domain)
	{
		$user = User::where('name', $domain)->orWhere('alternate_name', $domain)->first();
        $customer = Customer::where('user_id', $user->id)->first();
        $auth = CapilleryAuthenticate::where('user_id', $user->id)->first();
        $client = new Client(commonFunction::getClientParams($auth));

        if ($customer->reward_catalog_enabled) {
        	$url = '/'.config('global.api_gateway-rewards-core-v1-user-vouchers-brand').'/' . $customer->brand_name . '?'.http_build_query($request->query()).'&username='.$customer->brand_username;
        	try {
	            $result = $client->request('GET', $url);
	            return json_decode($result->getBody(), true);
	        } catch (Throwable $e) {
	            \Log::channel('error')->info('Get Brand Rewards Error: ' . $e->getMessage());
	            return $e->getMessage();
	        }
        }
        return false;
	}
}
