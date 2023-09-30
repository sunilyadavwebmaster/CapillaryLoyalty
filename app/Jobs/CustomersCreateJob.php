<?php

namespace App\Jobs;

use App\CapillaryFieldMap;
use App\CapilleryAuthenticate;
use App\Customer;
use App\functionClass\CommonFunction;
use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CustomersCreateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Shop's myshopify domain
     *
     * @var ShopDomain
     */
    public $shopDomain;

    /**
     * The webhook data
     *
     * @var object
     */
    public $data;
    public $timeout = 0;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shopDomain, $data)
    {
        \Log::info("Called customer create webhook construct");
        $this->shopDomain = $shopDomain;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            \Log::info('$this->shopDomain -- ' . $this->shopDomain);
            // Get shop details
            $user = User::where('name', $this->shopDomain)->orWhere('alternate_name', $this->shopDomain)->first();
            $config = Customer::where('user_id', $user->id)->first();
            if ($config) {
                if ($config->creation == 1) {

                    //Get customer data from webhook
                    $data = json_decode(json_encode($this->data), true);
                    \Log::info(json_encode($this->data));
                    // Get client id, secret and base url
                    $customerMetafields = $user->api()->rest('GET', '/admin/api/'.config('global.shopify-version').'/customers/'.$data['id'].'/metafields.json', ['query' => array()]);
                    \Log::info("customerMetafields".json_encode($customerMetafields));

                    $auth = CapilleryAuthenticate::where('user_id', $user->id)->first();

                    //Insert into 3rd party
                    $client = new Client(commonFunction::getClientParams($auth));

                    $url = config('global.api-version').'/'.config('global.add-customer');
                    $params = [
                        "root" => [
                            "customer" => [
                                "firstname" => $data['first_name'],
                                "lastname" => $data['last_name'],
                                "email" => $data['email'],
                                "registered_on" => date('Y-m-d h:i:s', strtotime($data['created_at'])),
                                "source" => "INSTORE",
                                "type" => "LOYALTY"
                            ]
                        ]
                    ];

                    if ($data['phone'] && $data['phone'] != null) {
                        $phoneValue = str_replace("+", "", $data['phone']);
                       
                        $phoneValue = str_replace(" ", "", $phoneValue);
                        $params = [
                            "root" => [
                                "customer" => [
                                    "firstname" => $data['first_name'],
                                    "lastname" => $data['last_name'],
                                    "email" => $data['email'],
                                    "mobile" => $phoneValue,
                                    "registered_on" => date('Y-m-d h:i:s', strtotime($data['created_at'])),
                                    "source" => "INSTORE",
                                    "type" => "LOYALTY"
                                ]
                            ]
                        ];
                    } else if (array_key_exists('default_address', $data)) {
                        if (array_key_exists('phone', $data['default_address'])) {
                            $phoneValue = str_replace("+", "", $data['default_address']['phone']);
                    
                            $phoneValue = str_replace(" ", "", $phoneValue);
                            $params = [
                                "root" => [
                                    "customer" => [
                                        "firstname" => $data['first_name'],
                                        "lastname" => $data['last_name'],
                                        "email" => $data['email'],
                                        "mobile" => $phoneValue,
                                        "registered_on" => date('Y-m-d h:i:s', strtotime($data['created_at'])),
                                        "source" => "INSTORE",
                                        "type" => "LOYALTY"
                                    ]
                                ]
                            ];
                        }
                    }

                    // get mapped field
                    $capillaryFieldMap = CapillaryFieldMap::where('user_id', $user->id)->where('mapping_role', 'customer')->get();
                    $metaData=json_decode(json_encode($customerMetafields['body']['metafields']));
                    if ($capillaryFieldMap) {
                        $custom_fields = array(
                            "field" => array()
                        );
                        $extended_fields = array(
                            "field" => array()
                        );
                        foreach ($capillaryFieldMap as $attribute) {
                            $value = NULL;
                            $capillaryAttribute = $attribute['capillary_field'];
                            $shopifyAttribute = $attribute['shopify_field'];
                            $value = "";
                            if ($attribute['status'] == 1) {

                                if ($shopifyAttribute == 'phone') {
                                    if ($data[$shopifyAttribute] != NULL) {
                                        $number = PhoneNumber::parse($data[$shopifyAttribute]);
                                        $value = $number->getNationalNumber();
                                    } else {
                                        $value = NULL;
                                    }
                                }else if(strpos($shopifyAttribute,"note.")!==false){
                                    $note_data=explode("\n",$data['note']);
                                    for($i=0;$i<count($note_data);$i++)
                                    {
                                        $note_value=explode(":",$note_data[$i]);

                                        if(explode(".",$shopifyAttribute)[1]==$note_value[0]){
                                            $value=trim($note_value[1]);
                                        }
                                    }
                                }else if(strpos($shopifyAttribute,"custom.")!==false){
                                    if(sizeof($metaData)>0){

                                        $key = array_search(explode(".",$shopifyAttribute)[1], array_column($metaData, 'key'));

                                        if($key>-1)
                                        {
                                            $value=$customerMetafields['body']['metafields'][$key]['value'];
                                        }

                                    }
                                }
                                
                               
                                else if(strpos($shopifyAttribute,".")!==false)
                                {
                                    $array_index = explode('.',$shopifyAttribute);
                    
                                    if(!array_key_exists($array_index[0],$data))
                                    {
                                        $value = "";
                                        break;
                                    }else{
                                        if(sizeof($array_index)==2)
                                        {
                                            if(!array_key_exists($array_index[1],$data[$array_index[0]]))
                                            {
                                                $value = "";
                                                break;
                                            }else{
                                                $value = $data[$array_index[0]][$array_index[1]];
                                            }
                                            
                                        }else if(sizeof($array_index)==3)
                                        {
                                            if(!array_key_exists($array_index[1],$data[$array_index[0]]) || !array_key_exists($array_index[2],$data[$array_index[0]][$array_index[1]]))
                                            {
                                                $value = "";
                                                break;
                                            }else{
                                                $value = $data[$array_index[0]][$array_index[1]][$array_index[2]];
                                            }
                                            
                                        }else if(sizeof($array_index)==4)
                                        {
                                            if(!array_key_exists($array_index[1],$data[$array_index[0]]) || !array_key_exists($array_index[2],$data[$array_index[0]][$array_index[1]]) || !array_key_exists($array_index[3],$data[$array_index[0]][$array_index[1]][$array_index[2]]))
                                            {
                                                $value = "";
                                                break;
                                            }else{
                                                $value = $data[$array_index[0]][$array_index[1]][$array_index[2]][$array_index[3]];
                                            }
                                            
                                        }else{
                                            $value = "";
                                            break;
                                        }
                                    }
                                    
                                    
                                }
                                else {
                                    $mapFieldArray = explode(",", $shopifyAttribute);
                                    $tmpData = $data;
                                    for ($i = 0; $i < count($mapFieldArray); $i++) {
                                        if (!array_key_exists(trim($mapFieldArray[$i]), $tmpData)) {
                                            $value = NULL;
                                            break;
                                        } else {
                                            $field = $tmpData[trim($mapFieldArray[$i])];
                                            $tmpData = $field;
                                            $value = $field;
                                        }
                                    }
                                }
                                if ($attribute['field_type'] == 'extended') {
                                    $field = array(
                                        'name' => $capillaryAttribute,
                                        'value' => $value
                                    );
                                    array_push($extended_fields['field'], $field);
                                    // $params['root']['customer']['[$capillaryAttribute]=$data[$shopifyAttribute];
                                }else if($attribute['field_type']=='core'){
                                    if($capillaryAttribute=="mobile")
                                    {
                                        $value = str_replace("+", "", $value);
                                        $value = str_replace(" ", "", $value);
                                    }
                                    $params['root']['customer'][$capillaryAttribute]=$value;
                                } else if($attribute['field_type'] == 'custom') {
                                    $field = array(
                                        'name' => $capillaryAttribute,
                                        'value' => $value
                                    );
                                    array_push($custom_fields['field'], $field);
                                }

                            }
                        }
                        if (count($custom_fields['field']) > 0) {
                            $params['root']['customer']['custom_fields'] = $custom_fields;
                        }
                        if (count($extended_fields['field']) > 0) {
                            $params['root']['customer']['extended_fields'] = $extended_fields;
                        }
                    }
                    try {
                        \Log::info(json_encode($params));
                        $result = $client->request('POST', $url, ['json' => $params]);
                    } catch (Exception $e) {
                        \Log::info($e->getMessage());
                    }
                    // \Log::info(json_decode($result->getBody(), true));
                } else {
                    \Log::info('Customer Creation is disabled');
                }
            } else {
                \Log::info('No Config Data Found');
            }
        } catch (ClientException $gce) {
            \Log::info(Psr7\Message::toString($gce->getResponse()));
        } catch (Exception $gex) {
            \Log::info($gex->getMessage());
        }
    }
}
