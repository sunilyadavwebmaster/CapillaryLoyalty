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

class UpdateCustomerDetails extends Controller
{
    public function index()
    {
        return view('search-customer');
    }

    public function getCustomer(Request $request)
    {
        $users = Auth::user();
        $user = User::where('id', $users->id)->first();
        $serach_input=$request->searchinput;
        $search_parameter="email";
        if(is_numeric($serach_input))
            $search_parameter="phone";

        $searchTerm = array('query' => $search_parameter.":".$serach_input);
        $customerAvl = $user->api()->rest('GET', '/admin/api/'.config('global.shopify-version').'/customers/search.json', ['query' => $searchTerm]);
        echo json_encode($customerAvl);
    }

    public function updateCustomer(Request $request)
    {
        $dataResponse=array();

        $users = Auth::user();
        $user = User::where('id', $users->id)->first();

        $searchTerm = array();
        $customerDataId = $user->api()->rest('GET', '/admin/api/'.config('global.shopify-version').'/customers/'.$request->customer_id.'.json', ['query' => $searchTerm]);

        $searchTerm = array('query' => "email:".$request->customer_email);
        $customerDataEmail = $user->api()->rest('GET', '/admin/api/'.config('global.shopify-version').'/customers/search.json', ['query' => $searchTerm]);

        $searchTerm = array('query' => "phone:".$request->customer_phone);
        $customerDataPhone = $user->api()->rest('GET', '/admin/api/'.config('global.shopify-version').'/customers/search.json', ['query' => $searchTerm]);

        if(sizeof($customerDataEmail['body']['customers'])>0 && $customerDataId['body']['customer']['id']!=$customerDataEmail['body']['customers'][0]['id'])
        {
            $dataResponse['code']=101;
            $dataResponse['msg']="Email arleady exist in different account";

        }else if(sizeof($customerDataPhone['body']['customers'])>0 && $customerDataId['body']['customer']['id']!=$customerDataPhone['body']['customers'][0]['id'])
        {
            $dataResponse['code']=102;
            $dataResponse['msg']="Phone arleady exist in different account";

        }
        else{
            $update_data=true;
            $auth = CapilleryAuthenticate::where('user_id', $user->id)->first();
            $client = new Client(commonFunction::getClientParams($auth));

            $url = config('global.api-version').'/'.config('global.get-customer').'?format=json&mlp=true&user_id=true&email=' . $customerDataId['body']['customer']['email'];
            $resultWithId= $client->request('GET', $url);
            $dataWithId = json_decode($resultWithId->getBody(), true);

            $url = config('global.api-version').'/'.config('global.get-customer').'?format=json&mlp=true&user_id=true&email=' . $request->customer_email;
            $resultWithEmail= $client->request('GET', $url);
            $dataWithEmail = json_decode($resultWithEmail->getBody(), true);

            $url = config('global.api-version').'/'.config('global.get-customer').'?format=json&mlp=true&user_id=true&mobile=' . str_replace("+","",$request->customer_phone);
            $resultWithPhone= $client->request('GET', $url);
            $dataWithPhone = json_decode($resultWithPhone->getBody(), true);
            if($dataWithId['response']['status']['code']==200
                && $dataWithEmail['response']['status']['code']==200
                && $dataWithId['response']['customers']['customer'][0]['user_id']!=$dataWithEmail['response']['customers']['customer'][0]['user_id'])
            {
                $update_data=false;
                $dataResponse['code']=101;
                $dataResponse['msg']="Email arleady exist in different account in Capillary";

            }else if($dataWithId['response']['status']['code']==200
                && $dataWithPhone['response']['status']['code']==200
                && $dataWithId['response']['customers']['customer'][0]['user_id']!=$dataWithPhone['response']['customers']['customer'][0]['user_id'])
            {
                $update_data=false;
                $dataResponse['code']=102;
                $dataResponse['msg']="Phone arleady exist in different account in Capillary";

            }else{
                if($dataWithId['response']['status']['code']==200)
                {
                    if($dataWithId['response']['customers']['customer'][0]['email']!= $request->customer_email)
                    {
                        $url=config('global.api-version-v2').'/'.config('global.customers').'/'.$dataWithId['response']['customers']['customer'][0]['user_id'].'/'.config('global.changeIdentifier').'?source=INSTORE';
                        $update_email=array();
                        $update_email['add']=array();
                        $update_email['add'][0]['type']="email";
                        $update_email['add'][0]['value']=$request->customer_email;
                        $update_email['remove']=array();
                        $update_email['remove'][0]['type']="email";
                        $update_email['remove'][0]['value']=$dataWithId['response']['customers']['customer'][0]['email'];
                        \Log::info("update identifer email".json_encode($update_email));

                        $result = $client->request('POST', $url, ['json' => $update_email]);
                        \Log::info($url."  ".$result->getBody());
                    }
                    \Log::info("Capillary Phone"."".$dataWithId['response']['customers']['customer'][0]['mobile']." Shopify Phone".str_replace("+","",$request->customer_phone));
                    if($dataWithId['response']['customers']['customer'][0]['mobile']!= str_replace("+","",$request->customer_phone))
                    {
                        \Log::info("Inside update identifer phone ");
                        $url=config('global.api-version-v2').'/'.config('global.customers').'/'.$dataWithId['response']['customers']['customer'][0]['user_id'].'/'.config('global.changeIdentifier').'?source=INSTORE';
                        $update_email=array();
                        $update_email['add']=array();
                        $update_email['add'][0]['type']="mobile";
                        $update_email['add'][0]['value']=str_replace("+","",$request->customer_phone);
                        $update_email['remove']=array();
                        $update_email['remove'][0]['type']="mobile";
                        $update_email['remove'][0]['value']=str_replace("+","",$dataWithId['response']['customers']['customer'][0]['mobile']);
                        \Log::info("update identifer phone ".json_encode($update_email));

                        $result = $client->request('POST', $url, ['json' => $update_email]);
                        \Log::info($url."  ".$result->getBody());
                    }

                }
            }

            if($update_data)
            {
                $customerShopify=array();
                $customerShopify['customer']=array();
                $customerShopify['customer']['id']=$request->customer_id;
                $customerShopify['customer']['email']=$request->customer_email;
                $customerShopify['customer']['phone']=$request->customer_phone;

                $responsePut = $user->api()->rest('PUT', '/admin/api/'.config('global.shopify-version').'/customers/'.$request->customer_id.'.json', ['json' => $customerShopify]);
                $dataResponse['code']=200;
                $dataResponse['msg']="Data update successfully ";
            }




        }

        echo json_encode($dataResponse);
    }
}
