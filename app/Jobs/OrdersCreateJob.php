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
use App\CapillaryPayment;
use Osiset\BasicShopifyAPI\Clients\Rest;

class OrdersCreateJob implements ShouldQueue
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
        \Log::info("Called Order Create webhook construct");
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
            \Log::info('$this->shopDomain -- ' . $this->shopDomain);
            $user = User::where('name', $this->shopDomain)->orWhere('alternate_name', $this->shopDomain)->first();
            $config = Customer::where('user_id', $user->id)->first();
            if ($config) {
                if ($config->add_transaction == 1) {
                    //Get Order data from webhook
                    $data = json_decode(json_encode($this->data), true);
                    \Log::info('order create -- ' . json_encode($this->data));
                    \Log::info('gateway -- ' . $data['gateway']);

                    $customerMetafields = $user->api()->rest('GET', '/admin/api/' . config('global.shopify-version') . '/orders/' . $data['id'] . '/metafields.json', ['query' => array()]);
                    // \Log::info($data);
                    if ($config->grouping == 1) {
                        $auth = CapilleryAuthenticate::where('user_id', $user->id)->first();
                        $client = new Client(commonFunction::getClientParams($auth));
                        $url = config('global.api-version-v2') . '/' . config('global.usergroups') . '?identifierName=email&identifierValue=' . $data['customer']['email'] . '&loyaltyDetails=true';
                        $result = $client->request('GET', $url);
                        $primaryUser = json_decode($result->getBody(), true);
                        if (array_key_exists('errors', $primaryUser)) {
                            $email = $data['customer']['email'];
                            $first_name = $data['customer']['first_name'];
                            $last_name = $data['customer']['last_name'];
                        } else {
                            foreach ($primaryUser['members'] as $member) {
                                if ($member['role'] == 'PRIMARY' || $member['role'] == 'primary' || $member['role'] == 'Primary') {
                                    foreach ($member['identifiers'] as $identifier) {
                                        if ($identifier['type'] == 'email') {
                                            $email = $identifier['value'];
                                        }
                                    }
                                    $first_name = $member['firstName'];
                                    $last_name = $member['lastName'];
                                }
                            }
                        }
                    } else {
                        $email = $data['customer']['email'];
                        $phone = $data['customer']['phone'];
                        $first_name = $data['customer']['first_name'];
                        $last_name = $data['customer']['last_name'];
                    }

                    $total_price = $data['total_price'];
                    if ($config->shipping_charges_included == 0) {
                        $total_price = $total_price - $data['total_shipping_price_set']['shop_money']['amount'];
                    }

                    $params = [
                        "root" => [
                            "transaction" => [
                                "bill_client_id" => "",
                                "type" => "regular",
                                "number" => $data['name'],
                                "amount" => $total_price,
                                "currency_code" => $data['current_subtotal_price_set']['shop_money']['currency_code'],
                                "notes" => "",
                                "billing_time" => $data['created_at'],
                                "gross_amount" => $total_price + $data['total_discounts'],
                                "shipping_source_till_code" => "magento.demo.solutions",
                                // Need to fix this
                                "outlier_status" => "NORMAL",
                                "credit_notes" => "",
                                "discount" => $data['total_discounts'],
                                "customer" => [
                                    "email" => $email,
                                    "firstname" => $first_name,
                                    "lastname" => $last_name,
                                    "mobile" => $phone
                                ]
                            ]
                        ]
                    ];


                    $fromshippingMobile = false;
                    try {
                        if (array_key_exists('phone', $data) && $data['phone'] && $data['phone'] != NULL) {
                            $phoneValue = str_replace("+", "", $data['phone']);

                            $phoneValue = str_replace(" ", "", $phoneValue);
                            $params['root']['transaction']['customer']['mobile'] = $phoneValue;
                        } else {
                            $foundMobileNumber = false;
                            if (array_key_exists('customer', $data)) {
                                if (array_key_exists('phone', $data['customer'])) {
                                    if ($data['customer']['phone'] && $data['customer']['phone'] != NULL) {
                                        $phoneValue = str_replace("+", "", $data['customer']['phone']);

                                        $phoneValue = str_replace(" ", "", $phoneValue);
                                        $params['root']['transaction']['customer']['mobile'] = $phoneValue;
                                        $foundMobileNumber = true;
                                    }
                                }
                            }
                            /*if (!$foundMobileNumber && array_key_exists('shipping_address', $data)) {
                                if (array_key_exists('phone', $data['shipping_address'])) {
                                    if ($data['shipping_address']['phone'] && $data['shipping_address']['phone'] != NULL) {
                                        $phoneValue = str_replace("+", "", $data['shipping_address']['phone']);

                                        $phoneValue = str_replace(" ", "", $phoneValue);
                                        $params['root']['transaction']['customer']['mobile'] = $phoneValue;
                                        $fromshippingMobile = true;

                                    }
                                }
                            }*/
                        }
                    } catch (Exception $pex) {
                        \Log::info($pex->getMessage());
                    }

                    $lineitems = array();
                    foreach ($data['line_items'] as $lineitem) {
                        $total_discount=0;

                        foreach($lineitem['discount_allocations'] as $discount_allocations)
                        {
                            $total_discount=$total_discount+$discount_allocations['amount'];
                        }
                        $item = array(
                            "type" => "regular",
                            "amount" => ($lineitem['price'] * $lineitem['quantity'])-$total_discount,
                            "item_code" => $lineitem['sku'],
                            "qty" => $lineitem['quantity'],
                            "rate" => $lineitem['price'],
                            "discount" => $total_discount,
                            "value" => $lineitem['price'],
                            "description" => ""
                        );

                        array_push($lineitems, $item);
                    }
                    $params['root']['transaction']['line_items']['line_item'] = $lineitems;

                    // get mapped field

                    $capillaryFieldMap = CapillaryFieldMap::where('user_id', $user->id)->where('mapping_role', 'transaction')->get();
                    $metaData = json_decode(json_encode($customerMetafields['body']['metafields']));
                    if ($capillaryFieldMap) {
                        $custom_fields = array(
                            "field" => array()
                        );
                        $extended_fields = array(
                            "field" => array()
                        );
                        foreach ($capillaryFieldMap as $attribute) {
                            $capillaryAttribute = $attribute['capillary_field'];
                            $shopifyAttribute = $attribute['shopify_field'];
                            if ($attribute['status'] == 1) {
                                if ($shopifyAttribute == 'phone') {
                                    if ($data[$shopifyAttribute] != NULL) {
                                        $number = PhoneNumber::parse($data[$shopifyAttribute]);
                                        $value = $number->getNationalNumber();
                                    } else {
                                        $value = NULL;
                                    }
                                } else if (strpos($shopifyAttribute, "note.") !== false) {
                                    $note_data = explode("\n", $data['note']);
                                    for ($i = 0; $i < count($note_data); $i++) {
                                        $note_value = explode(":", $note_data[$i]);

                                        if (explode(".", $shopifyAttribute)[1] == $note_value[0]) {
                                            $value = trim($note_value[1]);
                                        }
                                    }
                                } else if (strpos($shopifyAttribute, "custom.") !== false) {
                                    if (sizeof($metaData) > 0) {

                                        $key = array_search(explode(".", $shopifyAttribute)[1], array_column($metaData, 'key'));

                                        if ($key > -1) {
                                            $value = $customerMetafields['body']['metafields'][$key]['value'];
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
                                } else if (strpos($shopifyAttribute, ".") !== false) {
                                    $array_index = explode('.', $shopifyAttribute);

                                    if (!array_key_exists($array_index[0], $data)) {
                                        $value = "";
                                        break;
                                    } else {
                                        if (sizeof($array_index) == 2) {
                                            if (!array_key_exists($array_index[1], $data[$array_index[0]])) {
                                                $value = "";
                                                break;
                                            } else {
                                                $value = $data[$array_index[0]][$array_index[1]];
                                            }

                                        } else if (sizeof($array_index) == 3) {
                                            if (!array_key_exists($array_index[1], $data[$array_index[0]]) || !array_key_exists($array_index[2], $data[$array_index[0]][$array_index[1]])) {
                                                $value = "";
                                                break;
                                            } else {
                                                $value = $data[$array_index[0]][$array_index[1]][$array_index[2]];
                                            }

                                        } else if (sizeof($array_index) == 4) {
                                            if (!array_key_exists($array_index[1], $data[$array_index[0]]) || !array_key_exists($array_index[2], $data[$array_index[0]][$array_index[1]]) || !array_key_exists($array_index[3], $data[$array_index[0]][$array_index[1]][$array_index[2]])) {
                                                $value = "";
                                                break;
                                            } else {
                                                $value = $data[$array_index[0]][$array_index[1]][$array_index[2]][$array_index[3]];
                                            }

                                        } else {
                                            $value = "";
                                            break;
                                        }
                                    }


                                } else {
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
                        if (count($custom_fields['field']) > 0) {
                            $params['root']['transaction']['custom_fields'] = $custom_fields;
                        }
                        if (count($extended_fields['field']) > 0) {
                            $params['root']['transaction']['extended_fields'] = $extended_fields;
                        }
                    }

                    $capillaryPayments = CapillaryPayment::where('user_id', $user->id)->where('status', 1)->get();

                    $capillaryPaymentDataArray = [];
                    if ($capillaryPayments) {
                        foreach ($capillaryPayments as $capillaryPayment) {
                            if ($capillaryPayment['shopify_payment_method'] == $data['gateway']) {
                                $tender = array(
                                    'mode' => $capillaryPayment['cap_payment_method'],
                                    'value' => $data['current_total_price']
                                );
                                \Log::info('$tender -- ' . json_encode($tender));
                                array_push($capillaryPaymentDataArray, $tender);
                                break;
                            }
                        }

                        if ($config->transaction_mode == 1) {
                            foreach ($capillaryPayments as $capillaryPayment) {
                                if (($capillaryPayment['cap_payment_method'] == 'points' || $capillaryPayment['cap_payment_method'] == 'Points')) {
                                    foreach ($data['discount_codes'] as $pointredeem) {
                                        if ($pointredeem['code'] == "Redeemed Point") {
                                            $tender = array('mode' => $capillaryPayment['cap_payment_method'], "value" => $pointredeem['amount']);
                                            \Log::info('$tender -- ' . json_encode($tender));
                                            array_push($capillaryPaymentDataArray, $tender);
                                            break;
                                        } else if (strpos($pointredeem['code'], "Redeemed Point and Coupon ") > -1) {
                                            $pointredeemAmount = 0;
                                            foreach ($data['note_attributes'] as $noteAttribute) {
                                                if ($noteAttribute['name'] == "Redeemed Point Value") {
                                                    $pointredeemAmount = round((float) ($noteAttribute['value']), 2);
                                                    break;
                                                }
                                            }
                                            if ($pointredeemAmount > 0) {
                                                $tender = array('mode' => $capillaryPayment['cap_payment_method'], "value" => $pointredeemAmount);
                                                \Log::info('$point_tender -- ' . json_encode($tender));
                                                array_push($capillaryPaymentDataArray, $tender);
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if ($capillaryPaymentDataArray) {
                        $params['root']['transaction']['payment_details']['payment'] = $capillaryPaymentDataArray;
                    }

                    $auth = CapilleryAuthenticate::where('user_id', $user->id)->first();

                    //Insert into 3rd party
                    $url = config('global.api-version') . '/' . config('global.add-transaction');
                    $client = new Client(commonFunction::getClientParams($auth));
                    \Log::info('order params');
                    \Log::info(json_encode($params));
                    try {
                        $result = $client->request('POST', $url, ['json' => $params]);
                        $transactionResponse = json_decode($result->getBody(), true);
                        $transactionId = 0;

                        \Log::info('transactionResponse ' . json_encode($transactionResponse));

                        if (
                            $transactionResponse['response'] && $transactionResponse['response']['transactions']
                            && $transactionResponse['response']['transactions']['transaction']
                        ) {
                            $transactionId = $transactionResponse['response']['transactions']['transaction'][0]['id'];
                        }

                        \Log::info('discount_applications');
                        \Log::info(json_encode($data['discount_applications']));

                        if (count($data['discount_applications'])) {
                            \Log::info('$data[discount_applications] -- ' . json_encode($data['discount_applications']));
                            $hasCoupon = false;
                            $couponcode = '';
                            foreach ($data["discount_applications"] as $discount_code) {
                                if (array_key_exists('description', $discount_code)) {
                                    if ($discount_code['description'] == 'CAP_COUPON' || $discount_code['description'] == 'CAP_COUPON_AND_POINT' || $discount_code['description'] == 'CAP_COUPON_AND_SHOPIFY_COUPON'|| str_contains($discount_code['title'],"CAP-Coupon")) {
                                        $couponcode = $discount_code['title'];
                                        if ($couponcode) {
                                          //  $couponcode = str_replace("Redeemed Point and CAP-Coupon ", "", $couponcode);
                                            // $couponcode = str_replace("CAP-Coupon ", "", $couponcode);
                                            $couponcode =explode("#",$couponcode)[1];
                                        }
                                        $hasCoupon = true;
                                    }
                                }
                            }

                            if ($hasCoupon) {
                                $params = [
                                    'root' => [
                                        'coupon' => [
                                            'code' => $couponcode,
                                            'customer' => [
                                                'email' => $email
                                            ]
                                        ]
                                    ]
                                ];

                                if ($transactionId > 0) {
                                    $params['root']['coupon']['transaction'] = array('number' => $transactionId, 'amount' => $data['total_price']);
                                }

                                $url = config('global.api-version') . '/' . config('global.redeem-coupon');
                                \Log::info('coupon redeem params');
                                \Log::info(json_encode($params));
                                try {
                                    $resultCoupon = $client->request('POST', $url, ['json' => $params]);
                                    $couponRedeemData = json_decode($resultCoupon->getBody(), true);
                                    $couponRedeemData['response']['is_redeemable'] = 'true';
                                    // return $data['response'];
                                    \Log::info('success coupon redeemed: ');
                                    \Log::info(json_decode($resultCoupon->getBody(), true));
                                } catch (Throwable $e) {
                                    // return $e->getMessage();
                                    \Log::info('Redeem Coupon Error : ' . $e->getMessage());
                                }
                            }
                        }
                        // if(count($data['discount_codes'])){
                        \Log::info('$data[discount_codes] -- ' . json_encode($data['discount_codes']));
                        $point = 0;
                        $token = '';
                        foreach ($data['discount_codes'] as $discount_code) {
                            \Log::info('$discount_code[code] -- ' . $discount_code['code']);

                            if (strpos($discount_code['code'], "Redeemed Point") > -1) {
                                \Log::info('$data[note_attributes] -- ' . json_encode($data['note_attributes']));
                                foreach ($data['note_attributes'] as $noteAttribute) {
                                    // if ($noteAttribute['name'] == "Redeemed Point Value") {
                                    if ($noteAttribute['name'] == "Redeemed Point") {
                                        $point = round((float) ($noteAttribute['value']), 2);
                                        continue;
                                    }
                                    if ($noteAttribute['name'] == "Token Value") {
                                        $token = (string) $noteAttribute['value'];
                                        continue;
                                    }
                                }
                            } else if (strpos($discount_code['code'], "Loyalty Point") > -1) {
                                \Log::info('$data[note] -- ' . $data['note']);
                                $note_array = json_decode($data['note'], true);
                                foreach ($note_array as $noteAttribute) {
                                    if ($noteAttribute['name'] == "Redeemed Point") {
                                        $point = round((float) ($noteAttribute['value']), 2);
                                        continue;
                                    }
                                    if ($noteAttribute['name'] == "Token Value") {
                                        $token = (string) $noteAttribute['value'];
                                        continue;
                                    }
                                }
                            }
                        }

                        \Log::info('$point -- ' . $point);
                        //TODO: define mlpid
                        $token_data = \App\CartRedeemPoints::where('token', $token)->first();
                        if ($token_data) {
                            $mlpId = $token_data->mlp_id;
                        } else {
                            $mlpId = 0;
                        }

                        \Log::info('redeemed mlpId -- ' . $mlpId);
                        $primaryKey = ['email' => $email];
                        if ($config->mlp == 'card') {
                            $primaryKey = ['card_number' => $mlpId];
                        }
                        \Log::info('$point -- ' . $point);
                        if ($point > 0) {
                            $paramsPoint = [
                                'root' => [
                                    'redeem' => [
                                        'points_redeemed' => $point,
                                        'customer' => $primaryKey
                                    ]
                                ]
                            ];

                            if ($transactionId > 0) {
                                $paramsPoint['root']['redeem']['transaction_number'] = $transactionId;
                            }

                            \Log::info('point params');
                            \Log::info(json_encode($paramsPoint));
                            $urlPoint = config('global.api-version') . '/' . config('global.redeem-point') . '?skip_validation=true';
                            if ($config->mlp == 'brand') {
                                $urlPoint = $urlPoint . "&program_id=" . $mlpId;
                                $paramsPoint['root']['redeem']['group_redemption'] = true;
                            }
                            try {
                                $resultPoint = $client->request('POST', $urlPoint, ['json' => $paramsPoint]);
                                $redeemedPointData = json_decode($resultPoint->getBody(), true);
                                $redemption_id = $redeemedPointData['response']['responses']['points']['redemption_id'];
                                $metafieldData = array('redemption_id' => $redemption_id, 'point' => $point, 'email' => $email);
                                $metafieldDataJson = json_encode($metafieldData);
                                $orderMetaJson = array(
                                    "metafield" => array(
                                        "namespace" => "point_redeem",
                                        "key" => "point_redeem",
                                        "value" => $metafieldDataJson,
                                        "type" => "single_line_text_field"
                                    )
                                );
                                $metafield = $user->api()->rest('POST', '/admin/api/' . config('global.shopify-version') . '/orders/' . $data['id'] . '/metafields.json', ['json' => $orderMetaJson]);
                                \Log::info('Metafield : ' . json_encode($metafield, true));
                                \Log::info('success point redeemed: ');
                                \Log::info(json_decode($resultPoint->getBody(), true));
                            } catch (Exception $e) {
                                \Log::info('Redeem Point Error : ' . $e->getMessage());
                            }
                        }
                        // }
                        \Log::info('success add transaction: ');
                        \Log::info(json_encode(json_decode($result->getBody(), true)));
                    } catch (Exception $e) {
                        \Log::info($e->getMessage());
                    }
                } else {
                    \Log::info('Add Transaction is disabled');
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