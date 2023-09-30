<?php

namespace App\Jobs;

use App\functionClass\CommonFunction;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Customer;
use App\CapilleryAuthenticate;
use App\User;
use App\CapillaryFieldMap;

class OrdersCancelledJob implements ShouldQueue
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


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shopDomain, $data)
    {
        \Log::info("Called Order Cancelled webhook construct");
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
            // Get shop details
            $user = User::where('name',$this->shopDomain)->orWhere('alternate_name' , $this->shopDomain)->first();
            $config = Customer::where('user_id',$user->id)->first();
            $hasRedeemedPoints = false;
            if($config){
                if($config->cancel_transaction == 1) {
                    //Get Order data from webhook

                    \Log::info('order cancel data :-'.json_encode($this->data));
                    $data = json_decode(json_encode($this->data), true);
                    $customerMetafields = $user->api()->rest('GET', '/admin/api/'.config('global.shopify-version').'/orders/'.$data['id'].'/metafields.json', ['query' => array()]);
                    if(array_key_exists('customer', $data)) {
                        // \Log::info($data);
                        //$metafield = $user->api()->rest('GET', '/admin/api/'.config('global.shopify-version').'/orders/'.$data['id'].'/metafields.json');
                        \Log::info('Metafield : '.json_encode($customerMetafields, true));
                        $metafieldData = $customerMetafields['body']['metafields'];
                        foreach($metafieldData as $mtdata){
                            if($mtdata['key'] == 'point_redeem'){
                                $redeemedPointData = json_decode($mtdata['value'],true);
                                $hasRedeemedPoints = true;
                            }
                        }
                        // \Log::info('Metafield : '.json_encode($redeemedPointData, true));
                        if($config->grouping == 1){
                            $auth = CapilleryAuthenticate::where('user_id',$user->id)->first();
                            $client = new Client(commonFunction::getClientParams($auth));
                            $url = config('global.api-version-v2').'/'.config('global.usergroups').'?identifierName=email&identifierValue='.$data['customer']['email'].'&loyaltyDetails=true';
                            $result = $client->request('GET', $url);
                            $primaryUser = json_decode($result->getBody(), true);
                            if(array_key_exists('errors',$primaryUser)){
                                $email = $data['customer']['email'];
                                $first_name = $data['customer']['first_name'];
                                $last_name = $data['customer']['last_name'];
                            }else{
                                foreach ($primaryUser['members'] as $member) {
                                    if($member['role'] == 'PRIMARY' || $member['role'] == 'primary' || $member['role'] == 'Primary'){
                                        foreach($member['identifiers'] as $identifier){
                                            if($identifier['type'] == 'email'){
                                                $email = $identifier['value'];
                                            }
                                        }
                                        $first_name = $member['firstName'];
                                        $last_name = $member['lastName'];
                                    }
                                }
                            }
                        }else{
                            $email = $data['customer']['email'];
                            $first_name = $data['customer']['first_name'];
                            $last_name = $data['customer']['last_name'];
                        }

                        $total_price=$data['total_price'];
                        if($config->shipping_charges_included==0)
                        {
                            $total_price=$total_price-$data['total_shipping_price_set']['shop_money']['amount'];
                        }

                        $params = [
                            "root" => [
                                "transaction" => [
                                    "comment" => "cancel order",
                                    "number" => $data['name'],
                                    "amount" => $total_price,
                                    "billing_time" => $data['cancelled_at'],
                                    "purchase_time" => $data['created_at'],
                                    "gross_amount" => $total_price+$data['total_discounts'],
                                    "return_type"=> "FULL",
                                    "type"=> "RETURN",
                                    "customer" => [
                                        "email" => $email,
                                        "firstname" => $first_name,
                                        "lastname" => $last_name
                                    ]
                                ]
                            ]
                        ];
                        $fromshippingMobile=false;
                        try {
                            if (array_key_exists('phone', $data) && $data['phone'] && $data['phone'] != NULL) {
                                $phoneValue = str_replace("+", "", $data['phone']);
                                
                                $phoneValue = str_replace(" ", "", $phoneValue);
                                $params['root']['transaction']['customer']['mobile'] = $phoneValue;
                            }
                            else {
                                $foundMobileNumber = false;
                                if(array_key_exists('customer', $data)) {
                                    if(array_key_exists('phone', $data['customer'])) {
                                        if ($data['customer']['phone'] && $data['customer']['phone'] != NULL) {
                                            $phoneValue = str_replace("+", "", $data['customer']['phone']);
                                            
                                            $phoneValue = str_replace(" ", "", $phoneValue);
                                            $params['root']['transaction']['customer']['mobile'] = $phoneValue;
                                            $foundMobileNumber = true;
                                        }
                                    }
                                }
                                /*if(!$foundMobileNumber && array_key_exists('shipping_address', $data)) {
                                    if(array_key_exists('phone', $data['shipping_address'])) {
                                        if ($data['shipping_address']['phone'] && $data['shipping_address']['phone'] != NULL) {
                                            $phoneValue = str_replace("+", "", $data['shipping_address']['phone']);
                                           
                                            $phoneValue = str_replace(" ", "", $phoneValue);
                                            $params['root']['transaction']['customer']['mobile'] = $phoneValue;
                                            $fromshippingMobile=true;
                                        }
                                    }
                                }*/
                            }
                        } catch(Exception $pex){
                            \Log::info($pex->getMessage());
                        }

                        $lineitems = array();
                        $params['root']['transaction']['line_items']['line_item'] = $lineitems;

                        $capillaryFieldMap = CapillaryFieldMap::where('user_id',$user->id)->where('mapping_role','transaction')->get();
                        $metaData=json_decode(json_encode($customerMetafields['body']['metafields']));
                        if($capillaryFieldMap){
                            $custom_fields = array(
                                "field" => array()
                            );
                            $extended_fields = array(
                                "field" => array()
                            );
                            foreach ($capillaryFieldMap as $attribute) {
                                $capillaryAttribute = $attribute['capillary_field'];
                                $shopifyAttribute = $attribute['shopify_field'];
                                if($attribute['status'] == 1){
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
                                    }
                                    else if(strpos($shopifyAttribute,"custom.")!==false){
                                        if(sizeof($metaData)>0){
    
                                            $key = array_search(explode(".",$shopifyAttribute)[1], array_column($metaData, 'key'));
    
                                            if($key>-1)
                                            {
                                                $value=$customerMetafields['body']['metafields'][$key]['value'];
                                            }
    
                                        }
                                    }
                                    else if (strpos($shopifyAttribute, "[]") !== false) {
                                        
                                        $value="";
                                        $shopify_column_name=explode(".",$shopifyAttribute);
                                        $capillary_column_name=explode(".",$capillaryAttribute);
                                        if($shopify_column_name[1]=="[]")
                                        {
                                            $key=array_search("[]",$capillary_column_name);
                                            if($key == 2)
                                                        {
                                                            if(!array_key_exists($capillary_column_name[0],$params['root']['transaction']) || !array_key_exists($capillary_column_name[1],$params['root']['transaction'][$capillary_column_name[0]]))
                                                            {
                                                                $params['root']['transaction'][$capillary_column_name[0]][$capillary_column_name[1]] =array();
                                                            }
                                                            
                                                        }else{
                                                            if(!array_key_exists($capillary_column_name[0],$params['root']['transaction']))
                                                            {
                                                                $params['root']['transaction'][$capillary_column_name[0]] =array();
                                                            }
                                                        }
                                            if (!array_key_exists($shopify_column_name[0], $data))
                                            {
                                                
                                            }else{
                                                $k=0;
                                                
                                                foreach($data[$shopify_column_name[0]] as $s_data)
                                                {
                                                    $value="";
                                                    $j=0;
                                                    if(!array_key_exists($shopify_column_name[2],$s_data))
                                                    {
                                                        $value="";
                                                    }else
                                                    {
                                                        $value =$s_data[$shopify_column_name[2]];
                                                    }
                                                            
                                                            if($attribute['field_type'] == "core")
                                                            {
                                                                if($key==2)
                                                                {
                                                                    $params['root']['transaction'][$capillary_column_name[0]][$capillary_column_name[1]][$k][$capillary_column_name[$key+1]] =$value;
                                                                }else{
                                                                    $params['root']['transaction'][$capillary_column_name[0]][$k][$capillary_column_name[$key+1]] =$value;
                                                                }
                                                                $item_array[$capillary_column_name[$key-1]][$k][$capillary_column_name[$key+1]]=$s_data[$shopify_column_name[2]];
                                                            }
                                                            else
                                                            {
                                                                if($key==2)
                                                                {
                                                                    
                                                                    if(!array_key_exists($attribute['field_type'],$params['root']['transaction'][$capillary_column_name[0]][$capillary_column_name[1]][$k]) || !array_key_exists('field',$params['root']['transaction'][$capillary_column_name[0]][$capillary_column_name[1]][$k][$attribute['field_type']]))
                                                                    {
                                                                        $params['root']['transaction'][$capillary_column_name[0]][$capillary_column_name[1]][$k][$attribute['field_type']]['field'] =array();
                                                                        $params['root']['transaction'][$capillary_column_name[0]][$capillary_column_name[1]][$k][$attribute['field_type']]['field'][$j]['name'] =$capillary_column_name[$key+1];
                                                                        $params['root']['transaction'][$capillary_column_name[0]][$capillary_column_name[1]][$k][$attribute['field_type']]['field'][$j]['value'] =$value;
                                                                    }
                                                                        
                                                                    else{
                                                                        $j=sizeof($params['root']['transaction'][$capillary_column_name[0]][$capillary_column_name[1]][$k][$attribute['field_type']]['field']);
                                                                        $params['root']['transaction'][$capillary_column_name[0]][$capillary_column_name[1]][$k][$attribute['field_type']]['field'][$j]['name'] =$capillary_column_name[$key+1];
                                                                        $params['root']['transaction'][$capillary_column_name[0]][$capillary_column_name[1]][$k][$attribute['field_type']]['field'][$j]['value'] =$value;
                                                                    }
                                                                }else{
                                                                    if(!array_key_exists($attribute['field_type'],$params['root']['transaction'][$capillary_column_name[0]][$k]) || !array_key_exists('field',$params['root']['transaction'][$capillary_column_name[0]][$k][$attribute['field_type']]))
                                                                        {
                                                                        $params['root']['transaction'][$capillary_column_name[0]][$k][$attribute['field_type']]['field'] =array();
                                                                        $params['root']['transaction'][$capillary_column_name[0]][$k][$attribute['field_type']]['field'][$j]['name'] =$capillary_column_name[$key+1];
                                                                        $params['root']['transaction'][$capillary_column_name[0]][$k][$attribute['field_type']]['field'][$j]['value'] =$value;
                                                                        }
                                                                        else{
                                                                            $j=sizeof($params['root']['transaction'][$capillary_column_name[0]][$k][$attribute['field_type']]['field']);
                                                                            $params['root']['transaction'][$capillary_column_name[0]][$k][$attribute['field_type']]['field'][$j]['name'] =$capillary_column_name[$key+1];
                                                                            $params['root']['transaction'][$capillary_column_name[0]][$k][$attribute['field_type']]['field'][$j]['value'] =$value;
                                                                        }
                                                                }
                                                                
    
                                                            } 
                                                            
                                                        
                                                    
                                                    $k++;
                                                    
                                                }
                                               
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
                                    else{
                                        $mapFieldArray = explode(",",$shopifyAttribute);
                                        $tmpData = $data;
                                        for($i=0;$i<count($mapFieldArray);$i++){
                                            if(!array_key_exists(trim($mapFieldArray[$i]), $tmpData))
                                            {
                                                $value = NULL;
                                                break;
                                            }else{
                                                $field = $tmpData[trim($mapFieldArray[$i])];
                                                $tmpData = $field;
                                                $value = $field;
                                            }
                                        }

                                    }

                                    if ($attribute['field_type'] == 'extended') {
                                        if (strpos($shopifyAttribute, "[]") !== false) {
                                           
                                       }else{
                                           $field = array(
                                               'name' => $capillaryAttribute,
                                               'value' => $value
                                           );
                                           array_push($extended_fields['field'], $field);
                                       }
                                       
                                   } else if ($attribute['field_type'] == 'core') {
                                       if (explode('.', $shopifyAttribute)[0] == 'customer') {
                                           if ($capillaryAttribute == "mobile") {
                                               $value = str_replace("+", "", $value);
                                               $value = str_replace(" ", "", $value);
                                               if ($fromshippingMobile)
                                                   $params['root']['transaction']['customer'][$capillaryAttribute] = $value;
                                           } else
                                               $params['root']['transaction']['customer'][$capillaryAttribute] = $value;
                                       } else if (strpos($shopifyAttribute, "[]") !== false) {
                                           
                                       } else {
                                           $params['root']['transaction'][$capillaryAttribute] = $value;
                                       }
   
                                   } else {
                                       if (strpos($shopifyAttribute, "[]") !== false) {
                                           
                                       }else{
                                           $field = array(
                                               'name' => $capillaryAttribute,
                                               'value' => $value
                                           );
                                           array_push($custom_fields['field'], $field);
                                       }
                                       
                                   }

                                }
                            }
                            if(count($custom_fields['field']) > 0){
                                $params['root']['transaction']['custom_fields'] = $custom_fields;
                            }
                            if(count($extended_fields['field']) > 0){
                                $params['root']['transaction']['extended_fields'] = $extended_fields;
                            }
                        }

                        // Get client id, secret and base url
                        $auth = CapilleryAuthenticate::where('user_id',$user->id)->first();

                        //Insert into 3rd party
                        $url = config('global.api-version').'/'.config('global.add-transaction');
                        $client = new Client(commonFunction::getClientParams($auth));
                         \Log::info(json_encode($params));
                        try{
                            $result = $client->request('POST', $url, ['json' => $params]);
                            if ($hasRedeemedPoints) {
                                $reversePointParam = [
                                    'redemptionId' => $redeemedPointData['redemption_id'],
                                    'pointsToBeReversed' => $redeemedPointData['point'],
                                    'identifier' => [
                                        'type' => 'email',
                                        'value' => $redeemedPointData['email']
                                    ]
                                ];
                                $reversePointUrl = config('global.api-version-v2').'/'.config('global.reverse-point');
                                try {
                                    $reversePointResult = $client->request('POST', $reversePointUrl, ['json' => $reversePointParam]);
                                    \Log::info('reversePointResult');
                                    // \Log::info(json_decode($reversePointResult->getBody(), true));
                                } catch (ClientException $rce) {
                                    \Log::info(Psr7\Message::toString($rce->getResponse()));
                                } catch (Exception $e){
                                    \Log::info('point reverse error');
                                    // \Log::info($e->getMessage());
                                }
                            }
                        }catch(Exception $e){
                             \Log::info('transaction cancell error'.$e->getMessage());
                        }
                        // \Log::info(json_decode($result->getBody(), true));
                    }
                }else{
                    \Log::info('Cancelled Transaction is disabled');
                }
            }else{
                \Log::info('No Config Data Found');
            }
        } catch (ClientException $gce) {
            \Log::info(Psr7\Message::toString($gce->getResponse()));
        } catch(Exception $gex){
            \Log::info($gex->getMessage());
        }
    }
}
