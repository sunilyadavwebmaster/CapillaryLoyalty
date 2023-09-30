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

class GamificationController extends Controller
{
	function getAllGames($email, $domain)
	{
		Log::channel('info')->info('gamification');
		$user = User::where('name', $domain)->orWhere('alternate_name', $domain)->first();
        $customer = Customer::where('user_id', $user->id)->first();
        $auth = CapilleryAuthenticate::where('user_id', $user->id)->first();
        $client = new Client(commonFunction::getClientParams($auth));

        if ($customer->gamification_enabled) {
        	$searchTerm = array();
	        $customerAvl = $user->api()->rest('GET', '/admin/api/'.config('global.shopify-version').'/customers/' . $email . '.json', ['query' => $searchTerm]);
	        if ($customerAvl['errors']) {
	            Log::channel('error')->info('customer Details from Shopify : ' . json_encode($customerAvl));
	        } else
	            Log::channel('info')->info('Request Item : ' . json_encode($customerAvl));

	        $user_phone = $customerAvl['body']['customer']['phone'];
	        if ($user_phone == null) {
	            $note_attribute = explode("\n", $customerAvl['body']['customer']['note']);
	            for ($i = 0; $i < count($note_attribute); $i++) {
	                if (explode(":", $note_attribute[$i])[0] == "phone_number_full") {
	                    $user_phone = str_replace(" ","",explode(":", $note_attribute[$i])[1]);
	                }
	            }
	        }

	        $identifierColumn="email";
	        if($user_phone!=null)
	        {
	            $identifierColumn="mobile";
	        }else{
	            $user_phone=$customerAvl['body']['customer']['email'];
	        }

        	$url = '/'.config('global.api_gateway-gamification-v1-brand').'/' . $customer->brand_name . '/'.config('global.user-all-games').'?'.$identifierColumn.'='.$user_phone;
        	try {
        		Log::channel('info')->info('gamification url: ' . $url);
	            $result = $client->request('GET', $url);
	            return json_decode($result->getBody(), true);
	        } catch (Throwable $e) {
	            \Log::channel('error')->info('Get Brand Rewards Error: ' . $e->getMessage());
	            return $e->getMessage();
	        }
        }
        return false;
	}

	function getGameByUserId($email, $gameId, $domain)
	{
		$user = User::where('name', $domain)->orWhere('alternate_name', $domain)->first();
        $customer = Customer::where('user_id', $user->id)->first();
        $auth = CapilleryAuthenticate::where('user_id', $user->id)->first();
        $client = new Client(commonFunction::getClientParams($auth));

        if ($customer->gamification_enabled) {
        	$searchTerm = array();
	        $customerAvl = $user->api()->rest('GET', '/admin/api/'.config('global.shopify-version').'/customers/' . $email . '.json', ['query' => $searchTerm]);
	        if ($customerAvl['errors']) {
	            Log::channel('error')->info('customer Details from Shopify : ' . json_encode($customerAvl));
	        } else
	            Log::channel('info')->info('Request Item : ' . json_encode($customerAvl));

	        $user_phone = $customerAvl['body']['customer']['phone'];
	        if ($user_phone == null) {
	            $note_attribute = explode("\n", $customerAvl['body']['customer']['note']);
	            for ($i = 0; $i < count($note_attribute); $i++) {
	                if (explode(":", $note_attribute[$i])[0] == "phone_number_full") {
	                    $user_phone = str_replace(" ","",explode(":", $note_attribute[$i])[1]);
	                }
	            }
	        }

	        $identifierColumn="email";
	        if($user_phone!=null)
	        {
	            $identifierColumn="mobile";
	        }else{
	            $user_phone=$customerAvl['body']['customer']['email'];
	        }

        	$url = '/'.config('global.api_gateway-gamification-v1-brand').'/' . $customer->brand_name .'/'.config('global.user-game'). '/'.$gameId.'?language=en&'.$identifierColumn.'='.$user_phone;
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
