<?php

namespace App\Http\Controllers;

use App\CapilleryAuthenticate;
use App\Customer;
use App\functionClass\CommonFunction;
use App\User;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Osiset\BasicShopifyAPI\Clients\Rest;


class CustomerController extends Controller
{
    function getData($customerEmail, $domain)
    {
        // echo "test"; die;
        // Get client id, secret and base url
        $user = User::where('name', $domain)->orWhere('alternate_name', $domain)->first();
        $customer = Customer::where('user_id', $user->id)->first();
        $auth = CapilleryAuthenticate::where('user_id', $user->id)->first();
        $client = new Client(commonFunction::getClientParams($auth));

        $url = config('global.api-version') . '/' . config('global.get-customer') . '?format=json&mlp=true&user_id=true&email=' . $customerEmail;
        if ($customer->mlp == 'card') {
            $url .= '&card_details=true';
        }
        $result = $client->request('GET', $url);
        $data = json_decode($result->getBody(), true);

        if (isset($customer->pilot_program) && $customer->pilot_program == 1) {
            $dataPilot = $data;
            // return $data['response']['customers']['customer'][0]['custom_fields']['field'];
            foreach ($dataPilot['response']['customers']['customer'][0]['custom_fields']['field'] as $custom_fields) {
                // return $custom_fields['name'];
                if ($custom_fields['name'] == $customer->pilot_custom_field && $custom_fields['value'] == $customer->pilot_custom_field_value) {
                    $in_pilot = 1;
                    break;
                } else {
                    $in_pilot = 0;
                }
            }
        }

        if (isset($customer->grouping) && $customer->grouping == 0) {
            if (isset($customer->pilot_program) && $customer->pilot_program == 1 && (isset($in_pilot) && $in_pilot == 0)) {
                return json_encode($data['response'] = array('in_pilot' => 'no'));
            } else {
                if ($customer->mlp == 'brand') {
                    $data['response']['customers']['customer'][0]['mlp'] = 'brand';
                } else if ($customer->mlp == 'card') {
                    $cardLoyaltyInfo = $this->getCardLoyaltyInfo($data['response']['customers']['customer'][0], $domain);
                    $data['response']['customers']['customer'][0]['cardLoyaltyInfo'] = $cardLoyaltyInfo;
                    $data['response']['customers']['customer'][0]['mlp'] = 'card';
                }
                return json_encode($data['response']['customers']['customer'][0]);
            }
        } else {
            if (isset($customer->pilot_program) && $customer->pilot_program == 1 && (isset($in_pilot) && $in_pilot == 0)) {
                return json_encode($data['response'] = array('in_pilot' => 'no'));
            } else {
                $url = config('global.api-version-v2') . '/' . config('global.usergroups') . '?identifierName=email&identifierValue=' . $customerEmail . '&loyaltyDetails=true';
                $result = $client->request('GET', $url);
                $primaryUser = json_decode($result->getBody(), true);
                if (array_key_exists('errors', $primaryUser)) {
                    return json_encode($primaryUser['response'] = array('in_group' => 'no'));
                } else {
                    foreach ($primaryUser['members'] as $member) {
                        if ($member['role'] == 'PRIMARY' || $member['role'] == 'primary' || $member['role'] == 'Primary') {
                            foreach ($member['identifiers'] as $identifier) {
                                if ($identifier['type'] == 'email') {
                                    $primaryUser = $identifier['value'];
                                }
                            }
                            $url = config('global.api-version') . '/' . config('global.get-customer') . '?mlp=true&user_id=true&email=' . $primaryUser;
                            $result = $client->request('GET', $url);
                            $data = json_decode($result->getBody(), true);
                            if ($customer->mlp == 'brand') {
                                $data['response']['customers']['customer'][0]['mlp'] = 'brand';
                            }
                            return json_encode($data['response']['customers']['customer'][0]);
                        }
                    }
                }
            }
        }

    }


    function getMlpInfo($customerEmail, $domain)
    {
        $user = User::where('name', $domain)->orWhere('alternate_name', $domain)->first();
        $customer = Customer::where('user_id', $user->id)->first();
        $auth = CapilleryAuthenticate::where('user_id', $user->id)->first();
        $client = new Client(commonFunction::getClientParams($auth));

        $identifierColumn = "email";
        $user_phone = $customerEmail;


        $url = config('global.api-version') . '/' . config('global.get-customer') . '?format=json&mlp=true&user_id=true&' . $identifierColumn . '=' . $user_phone;
        if ($customer->mlp == 'card') {
            $url .= '&card_details=true';
        }
        $result = $client->request('GET', $url);
        $data = json_decode($result->getBody(), true);

        if ($customer->mlp == 'card') {
            return json_encode($data['response']['customers']['customer'][0]['card_details']);
        } else {
            return json_encode($data['response']['customers']['customer'][0]['points_summaries']['points_summary']);
        }
    }

    function getCardLoyaltyInfo($customerInfo, $domain)
    {
        if (isset($customerInfo['card_details'])) {
            $pointsDetails = [];
            foreach ($customerInfo['card_details'] as $pointsData) {
                $cardNum = $pointsData['card_number'];
                $cardInfo = [
                    'programId' => $cardNum,
                    'program_title' => $pointsData['series_name'],
                ];
                if ($cardData = $this->getCardDetails($cardNum, $domain)) {
                    $pointsDetails[] = array_merge($cardInfo, $cardData);
                }
            }
            return $pointsDetails;
        }
        return false;
    }

    function getCardDetails($cardNum, $domain)
    {
        $user = User::where('name', $domain)->orWhere('alternate_name', $domain)->first();
        $auth = CapilleryAuthenticate::where('user_id', $user->id)->first();
        $client = new Client(commonFunction::getClientParams($auth));

        $url = config('global.api-version-v2') . '/' . config('global.search-customer') . '?source=INSTORE&format=json&identifierName=cardnumber&identifierValue=' . $cardNum;
        $result = $client->request('GET', $url);
        $data = json_decode($result->getBody(), true);

        if (isset($data['loyaltyProgramDetails'])) {
            $cardLoyalty = $data['loyaltyProgramDetails'][0];
            return [
                'redeemed' => $cardLoyalty['redeemed'],
                'expired' => $cardLoyalty['expired'],
                'lifetimePoints' => $cardLoyalty['lifetimePoints'],
                'loyaltyPoints' => $cardLoyalty['loyaltyPoints']
            ];
        } else {
            return false;
        }
    }


    /**
     * Get custome profile
     * @param $customerEmail customer email
     * @param $domain shopify store url
     **/
    function getProfile($customerEmail, $domain)
    {
        $user = User::where('name', $domain)->orWhere('alternate_name', $domain)->first();
        $customer = Customer::where('user_id', $user->id)->first();

        $auth = CapilleryAuthenticate::where('user_id', $user->id)->first();
        $client = new Client(commonFunction::getClientParams($auth));


        $url = config('global.api-version') . '/' . config('global.get-customer') . '?mlp=true&user_id=true&email=' . $customerEmail;
        $result = $client->request('GET', $url);
        $data = json_decode($result->getBody(), true);

        if (isset($customer->pilot_program) && $customer->pilot_program == 1) {
            foreach ($data['response']['customers']['customer'][0]['custom_fields']['field'] as $custom_fields) {
                // return $custom_fields['name'];
                if ($custom_fields['name'] == $customer->pilot_custom_field && $custom_fields['value'] == $customer->pilot_custom_field_value) {
                    $in_pilot = 1;
                    break;
                } else {
                    $in_pilot = 0;
                }
            }
        }


        $url = config('global.api-version') . '/' . config('global.get-transaction') . '?email=' . $customerEmail;
        $result = $client->request('GET', $url);
        $transaction = json_decode($result->getBody(), true);
        $data['response']['customers']['customer'][0]['current_slab'] = $transaction['response']['customer']['current_slab'];
        $data['response']['customers']['customer'][0]['total_available_points'] = $transaction['response']['customer']['total_available_points'];
        if (isset($customer->grouping) && $customer->grouping == 1) {
            $url = config('global.api-version-v2') . '/' . config('global.usergroups') . '?identifierName=email&identifierValue=' . $customerEmail . '&loyaltyDetails=true';
            $result = $client->request('GET', $url);
            $primaryUser = json_decode($result->getBody(), true);
            if (array_key_exists('errors', $primaryUser)) {
                if (isset($customer->pilot_program) && $customer->pilot_program == 1 && (isset($in_pilot) && $in_pilot == 0)) {
                    $data['response']['customers']['customer'][0]['in_pilot'] = 0;
                }
                return json_encode($data['response']['customers']['customer'][0]);
            } else {
                foreach ($primaryUser['members'] as $member) {
                    if ($member['role'] == 'PRIMARY' || $member['role'] == 'primary' || $member['role'] == 'Primary') {
                        foreach ($member['loyaltySummary']['enrolledPrograms'] as $enrolledProgram) {
                            $data['response']['customers']['customer'][0]['current_slab'] = $enrolledProgram['currentSlab'];
                            $data['response']['customers']['customer'][0]['total_available_points'] = $enrolledProgram['totalAvailablePoints'];
                        }
                    }
                }
            }
        }

        $data['response']['customers']['customer'][0]['cfetch'] = $customer->fetch;

        if (isset($customer->pilot_program) && $customer->pilot_program == 1 && (isset($in_pilot) && $in_pilot == 0)) {
            $data['response']['customers']['customer'][0]['in_pilot'] = 0;
        }
        return json_encode($data['response']['customers']['customer'][0]);
    }


    /**
     * Get coupon history
     * @param id shopify customer id
     * @param customer email
     * @param shopify store url
     **/
    function getCouponHistory($id, $email, $domain)
    {
        $user = User::where('name', $domain)->orWhere('alternate_name', $domain)->first();
        $customer = Customer::where('user_id', $user->id)->first();
        $auth = CapilleryAuthenticate::where('user_id', $user->id)->first();
        $client = new Client(commonFunction::getClientParams($auth));

        $url = config('global.api-version-v2') . '/' . config('global.customers') . '/' . $id . '/' . config('global.coupons');
        $result = $client->request('GET', $url);
        $data = json_decode($result->getBody(), true);

        if (isset($customer->pilot_program) && $customer->pilot_program == 1) {
            $url = config('global.api-version') . '/' . config('global.get-customer') . '?mlp=true&user_id=true&email=' . $email;
            $result = $client->request('GET', $url);
            $dataPilot = json_decode($result->getBody(), true);
            // return $data['response']['customers']['customer'][0]['custom_fields']['field'];
            foreach ($dataPilot['response']['customers']['customer'][0]['custom_fields']['field'] as $custom_fields) {
                // return $custom_fields['name'];
                if ($custom_fields['name'] == $customer->pilot_custom_field && $custom_fields['value'] == $customer->pilot_custom_field_value) {
                    $in_pilot = 1;
                    break;
                } else {
                    $in_pilot = 0;
                }
            }
        }
        if (isset($customer->grouping) && $customer->grouping == 0 && $customer->group_coupon == 0) {
            if (isset($customer->pilot_program) && $customer->pilot_program == 1 && (isset($in_pilot) && $in_pilot == 0)) {
                return json_encode($data['response'] = array('in_pilot' => 'no'));
            } else {
                return json_encode($data['entity']);
            }
        } else {
            if (isset($customer->pilot_program) && $customer->pilot_program == 1 && (isset($in_pilot) && $in_pilot == 0)) {
                return json_encode($data['response'] = array('in_pilot' => 'no'));
            } else {
                $url = config('global.api-version-v2') . '/' . config('global.usergroups') . '?identifierName=email&identifierValue=' . $email . '&loyaltyDetails=true';
                $result = $client->request('GET', $url);
                $primaryUser = json_decode($result->getBody(), true);
                if (array_key_exists('errors', $primaryUser)) {
                    return json_encode($primaryUser['response'] = array('in_group' => 'no'));
                } else {
                    foreach ($primaryUser['members'] as $member) {
                        if ($member['role'] == 'PRIMARY' || $member['role'] == 'primary' || $member['role'] == 'Primary') {
                            $id = $member['userId'];
                            $url = config('global.api-version-v2') . '/' . config('global.customers') . '/' . $id . '/' . config('global.coupons');
                            $result = $client->request('GET', $url);
                            $data = json_decode($result->getBody(), true);
                            return json_encode($data['entity']);
                        }
                    }
                }
            }
        }

    }





    function getTransactionHistory($email, $domain)
    {
        $user = User::where('name', $domain)->orWhere('alternate_name', $domain)->first();
        $customer = Customer::where('user_id', $user->id)->first();
        $auth = CapilleryAuthenticate::where('user_id', $user->id)->first();
        $client = new Client(commonFunction::getClientParams($auth));

        $url = config('global.api-version') . '/' . config('global.get-transaction') . '?email=' . $email;
        $result = $client->request('GET', $url);
        $data = json_decode($result->getBody(), true);

        if (isset($customer->pilot_program) && $customer->pilot_program == 1) {
            $url = config('global.api-version') . '/' . config('global.get-customer') . '?mlp=true&user_id=true&email=' . $email;
            $result = $client->request('GET', $url);
            $dataPilot = json_decode($result->getBody(), true);
            // return $data['response']['customers']['customer'][0]['custom_fields']['field'];
            foreach ($dataPilot['response']['customers']['customer'][0]['custom_fields']['field'] as $custom_fields) {
                // return $custom_fields['name'];
                if ($custom_fields['name'] == $customer->pilot_custom_field && $custom_fields['value'] == $customer->pilot_custom_field_value) {
                    $in_pilot = 1;
                    break;
                } else {
                    $in_pilot = 0;
                }
            }
        }

        if (isset($customer->grouping) && $customer->grouping == 0) {
            if (isset($customer->pilot_program) && $customer->pilot_program == 1 && (isset($in_pilot) && $in_pilot == 0)) {
                return json_encode($data['response'] = array('in_pilot' => 'no'));
            } else {
                return json_encode($data['response']);
            }
        } else {
            if (isset($customer->pilot_program) && $customer->pilot_program == 1 && (isset($in_pilot) && $in_pilot == 0)) {
                return json_encode($data['response'] = array('in_pilot' => 'no'));
            } else {
                $url = config('global.api-version-v2') . '/' . config('global.usergroups') . '?identifierName=email&identifierValue=' . $email . '&loyaltyDetails=true';
                $result = $client->request('GET', $url);
                $primaryUser = json_decode($result->getBody(), true);
                if (array_key_exists('errors', $primaryUser)) {
                    return json_encode($primaryUser['response'] = array('in_group' => 'no'));
                } else {
                    foreach ($primaryUser['members'] as $member) {
                        if ($member['role'] == 'PRIMARY' || $member['role'] == 'primary' || $member['role'] == 'Primary') {
                            foreach ($member['identifiers'] as $identifier) {
                                if ($identifier['type'] == 'email') {
                                    $email = $identifier['value'];
                                }
                            }
                            $url = config('global.api-version') . '/' . config('global.get-transaction') . '?email=' . $email;
                            $result = $client->request('GET', $url);
                            $data = json_decode($result->getBody(), true);
                            return json_encode($data['response']);
                        }
                    }
                }
            }
        }
    }


    public function sendOTP(Request $request)
    {
        $user = User::where('name', $request->domain)->orWhere('alternate_name', $request->domain)->first();
        $customer = Customer::where('user_id', $user->id)->first();
        $auth = CapilleryAuthenticate::where('user_id', $user->id)->first();
        $client = new Client(commonFunction::getClientParams($auth));
        $entityType = ($request->otpon == 'email') ? 'email' : 'MOBILE';

        if ($entityType == 'email') {
            $email = $request->value;
            $searchTerm = array('query' => 'email:' . $email);
            $customerAvl = $user->api()->rest('GET', '/admin/api/' . config('global.shopify-version') . '/customers/search.json', ['query' => $searchTerm]);
            if (count($customerAvl['body']['customers']) > 0) {
                return $response['emailAvl'] = true;
            }
        }

        $params = [
            'entityType' => $entityType,
            'entityValue' => $request->value,
            'action' => 'REGISTRATION',
            'template' => 'Hi user.Your OTP for validation is {{ validation_code }}.Enjoy',
            'channels' => [
                array(
                    'type' => $entityType,
                    'value' => $request->value
                )
            ]
        ];
        \Log::info('OTP Params : ' . json_encode($params));
        $url = config('global.api-version-v2') . '/' . config('global.get-otp');
        try {
            $result = $client->request('POST', $url, ['json' => $params]);
            $data = json_decode($result->getBody(), true);
            \Log::info('OTP generated : ' . json_encode($data));
            return $result->getBody();
        } catch (Throwable $e) {
            \Log::info('OTP Error: ' . $e->getMessage());
            return $e->getMessage();
        }
    }

    public function getSettings($domain, $token)
    {
        $user = User::where('name', $domain)->orWhere('alternate_name', $domain)->first();
        $settings = Customer::where('user_id', $user->id)->first();
        $token_data = \App\CartRedeemPoints::where('token', $token)->first();
        $response = array();
        $response['setting'] = $settings;
        $response['redeem_points'] = $token_data;
        return $response;
    }

    public function cancelPoint($token)
    {
        $token_data = \App\CartRedeemPoints::where('token', $token)->first();

        if ($token_data) {
            var_dump($token_data);
            $token_data->delete();
        }
    }

    public function validateOTP(Request $request)
    {
        $user = User::where('name', $request->domain)->orWhere('alternate_name', $request->domain)->first();
        $customer = Customer::where('user_id', $user->id)->first();
        $auth = CapilleryAuthenticate::where('user_id', $user->id)->first();
        $client = new Client(commonFunction::getClientParams($auth));
        $entityType = ($request->otpon == 'email') ? 'email' : 'MOBILE';
        $params = [
            'entityType' => $entityType,
            'entityValue' => $request->value,
            'action' => 'REGISTRATION',
            'code' => $request->code
        ];
        \Log::info('Validate Params : ' . json_encode($params));
        $url = config('global.api-version-v2') . '/' . config('global.otp-validate');
        try {
            $result = $client->request('POST', $url, ['json' => $params]);
            $data = json_decode($result->getBody(), true);
            return $data;
        } catch (Throwable $e) {
            \Log::info('Point Error: ' . $e->getMessage());
            return $e->getMessage();
        }
    }


    public function isRedeemableCoupon(Request $request)
    {

        $user = User::where('name', $request->domain)->orWhere('alternate_name', $request->domain)->first();
        $customer = Customer::where('user_id', $user->id)->first();
        $auth = CapilleryAuthenticate::where('user_id', $user->id)->first();

        $client = new Client(commonFunction::getClientParams($auth));

        if ($customer->grouping == 1 || $customer->group_coupon == 1) {
            $url = config('global.api-version-v2') . '/' . config('global.usergroups') . '?identifierName=email&identifierValue=' . $request->email . '&loyaltyDetails=true';
            $result = $client->request('GET', $url);
            $primaryUser = json_decode($result->getBody(), true);
            if (array_key_exists('errors', $primaryUser)) {
                $customerEmail = $request->email;
            } else {
                foreach ($primaryUser['members'] as $member) {
                    if ($member['role'] == 'PRIMARY' || $member['role'] == 'primary' || $member['role'] == 'Primary') {
                        foreach ($member['identifiers'] as $identifier) {
                            if ($identifier['type'] == 'email') {
                                $customerEmail = $identifier['value'];
                            }
                        }
                    }
                }
            }
        } else {
            $customerEmail = $request->email;
        }

        $url = config('global.api-version') . '/' . config('global.coupon-isredeemable') . '?details=extended&code=' . $request->coupon . '&email=' . $customerEmail . '&format=json';
        try {
            $result = $client->request('GET', $url);

            $data = json_decode($result->getBody(), true);
            $response = array();
            if ($data['response']['coupons']['redeemable']['is_redeemable'] == 'true') {
                $data['response']['is_redeemable'] = 'true';
                $is_redeemable = 1;
                $productInfo = count($data['response']['coupons']['redeemable']['series_info']['productInfo']);
                if ($productInfo > 0) {
                    $skues = $request->skus;
                    $product_names = $request->name; // json_decode($skus, true);
                    $is_redeemable = 0;
                    $sku_array = array();
                    $product_array = array();
                    if ($data['response']['coupons']['redeemable']['series_info']['products'] == null) {
                        $sku_string = "";

                        foreach ($skues as $sku) {
                            if ($sku_string != "")
                                $sku_string .= ",";
                            if ($sku['key'] != "")
                                $sku_string .= $sku['key'];
                        }
                        \Log::info('sku' . $sku_string);
                        $url = config('global.api-version') . '/' . config('global.get-product') . '?sku=' . urlencode($sku_string) . '&format=json';

                        try {

                            $result = $client->request('GET', $url);
                            $data_product = json_decode($result->getBody(), true);
                            \Log::info('sku ' . json_encode($data_product));
                            if ($data_product['response']['status']['code'] == 200 || $data_product['response']['status']['code'] == 201) {
                                if (sizeof($data_product['response']['product']['item']) > 0) {
                                    foreach ($data_product['response']['product']['item'] as $product) {
                                        if ($product['item_status']['status']) {
                                            $responses = array();
                                            $responses['sku'] = $product['sku'];
                                            $responses['is_redeemable'] = false;
                                            $responses['name'] = $product['category']['name'];
                                            array_push($response, $responses);
                                        }

                                    }
                                    foreach ($data['response']['coupons']['redeemable']['series_info']['categories']['category'] as $cSku) {
                                        $sku_array[] = $cSku['name'];
                                    }
                                    $in = 0;
                                    $data['response']['is_redeemable'] = 'false';

                                    foreach ($response as $sku) {
                                        for ($counter = 0; $counter < sizeof($sku_array); $counter++) {
                                            if ($sku['name'] == $sku_array[$counter]) {
                                                $response[$in]['is_redeemable'] = true;
                                                $data['response']['is_redeemable'] = 'true';
                                            }

                                        }

                                        $in++;

                                    }
                                }
                            } else {
                                $data['response']['is_redeemable'] = 'false';
                            }


                        } catch (RequestException $e) {
                            $data['response']['is_redeemable'] = 'false';

                        }


                    } else {
                        foreach ($data['response']['coupons']['redeemable']['series_info']['products']['product'] as $cSku) {
                            $sku_array[] = $cSku['sku'];

                        }
                        $in = 0;
                        $data['response']['is_redeemable'] = 'false';
                        foreach ($skues as $sku) {
                            $responses = array();
                            $responses['sku'] = $sku['key'];
                            $responses['is_redeemable'] = false;
                            array_push($response, $responses);
                            if ($sku['key'] != "")
                                for ($counter = 0; $counter < sizeof($sku_array); $counter++) {

                                    if ($sku['key'] == $sku_array[$counter]) {
                                        $response[$in]['is_redeemable'] = true;
                                        $data['response']['is_redeemable'] = 'true';
                                    }

                                }

                            $in++;

                        }
                    }

                }
                $data['response']['sku'] = $response;
                return $data['response'];
            } else {
                $data['response']['is_redeemable'] = 'false';
                $data['response']['code'] = $data['response']['coupons']['redeemable']['item_status']['code'];
                return $data['response'];
            }
        } catch (Throwable $e) {
            \Log::info('Coupon Error: ' . $e->getMessage());
            return $e->getMessage();
        }
    }


    public function isRedeemablePoint($point, $email, $domain, $token, $mlpId = null)
    {
        $user = User::where('name', $domain)->orWhere('alternate_name', $domain)->first();
        $customer = Customer::where('user_id', $user->id)->first();
        $auth = CapilleryAuthenticate::where('user_id', $user->id)->first();
        $client = new Client(commonFunction::getClientParams($auth));

        if ($customer->grouping == 1) {
            $url = config('global.api-version-v2') . '/' . config('global.usergroups') . '?identifierName=email&identifierValue=' . $email . '&loyaltyDetails=true';
            $result = $client->request('GET', $url);
            $primaryUser = json_decode($result->getBody(), true);
            if (array_key_exists('errors', $primaryUser)) {
                $customerEmail = $email;
            } else {
                foreach ($primaryUser['members'] as $member) {
                    if ($member['role'] == 'PRIMARY' || $member['role'] == 'primary' || $member['role'] == 'Primary') {
                        foreach ($member['identifiers'] as $identifier) {
                            if ($identifier['type'] == 'email') {
                                $customerEmail = $identifier['value'];
                            }
                        }
                    }
                }
            }
        } else {
            $customerEmail = $email;
        }

        $mlpType = null;
        if ($customer->mlp == 'card') {
            $mlpType = 'card_number';
        } elseif ($customer->mlp == 'brand') {
            $mlpType = 'program_id';
        }

        $url = config('global.api-version') . '/' . config('global.get-customer') . '?format=json&mlp=true&user_id=true&email=' . $customerEmail;
        if ($customer->mlp == 'card') {
            $url .= '&card_details=true';
        }
        $result = $client->request('GET', $url);
        $customerdata = json_decode($result->getBody(), true);
        $group_redemption = "false";
        if ($customer->show_mlp == 0 && $customer->mlp == 'brand') {
            foreach ($customerdata['response']['customers']['customer'][0]['points_summaries']['points_summary'] as $points_summary) {
                $pos = strpos(strtolower($points_summary['program_description']), strtolower("Default program"));
                if ($pos !== false) {
                    
                    $mlpId = $points_summary['programId'];
                    break;
                }
            }

            if (sizeof($customerdata['response']['customers']['customer'][0]['group_points_summaries']['group_points_summary']) > 0) {
                foreach ($customerdata['response']['customers']['customer'][0]['group_points_summaries']['group_points_summary'] as $points_summary) {
                    if ($mlpId == $points_summary['group_program_id']) {
                        $group_redemption = "true";
                    }
                }
            }

        }
        $url = config('global.api-version') . '/' . config('global.points-isredeemable') . '?points=' . $point . '&email=' . $customerEmail . '&format=json&skip_validation=true';
        if ($mlpType != null && $mlpId != null) {
            $url = $url . '&' . $mlpType . '=' . $mlpId . '&group_redemption=' . $group_redemption;
        }
        try {
            $result = $client->request('GET', $url);
            $data = json_decode($result->getBody(), true);
            if ($data['response']['status']['code'] == 200 && $data['response']['points']['redeemable']['is_redeemable'] == true) {

                $token_data = \App\CartRedeemPoints::where('token', $token)->first();
                if (!$token_data) {
                    $cartRedeem = new \App\CartRedeemPoints();
                    $cartRedeem->points = $point;
                    $cartRedeem->token = $token;
                    $cartRedeem->mlp_id = $mlpId;
                    $cartRedeem->save();
                } else {
                    $cartUpdate = array('points' => $point, 'token' => $token, 'mlp_id' => $mlpId);
                    $token_data->update($cartUpdate);
                }
            }
            return $data;
        } catch (Throwable $e) {
            \Log::info('OTP Error: ' . $e->getMessage());
            return $e->getMessage();
        }

    }

    public function tracker($email, $domain)
    {
        $user = User::where('name', $domain)->orWhere('alternate_name', $domain)->first();
        $customer = Customer::where('user_id', $user->id)->first();
        $auth = CapilleryAuthenticate::where('user_id', $user->id)->first();
        $client = new Client(commonFunction::getClientParams($auth));

        /** get customer **/
        $customerGetUrl = config('global.api-version') . '/' . config('global.get-customer') . '?mlp=true&user_id=true&email=' . $email;
        try {
            $result = $client->request('GET', $customerGetUrl);
            try {
                $customerData = json_decode($result->getBody(), true);
                /** tracker data **/
                $trackerUrl = '/api/customers/' . $customerData['response']['customers']['customer'][0]['user_id'] . '/trackers';
                $result = $client->request('GET', $trackerUrl);
                $trackerDataResponse = json_decode($result->getBody(), true);
                if (count($trackerDataResponse['results']['data']) > 0) {
                    $trackerData = array(
                        'id' => $trackerDataResponse['results']['data'][0]['id'],
                        'name' => $trackerDataResponse['results']['data'][0]['name'],
                        'conditionId' => $trackerDataResponse['results']['data'][0]['conditionId'],
                        'type' => $trackerDataResponse['results']['data'][0]['type'],
                        'value' => $trackerDataResponse['results']['data'][0]['value'],
                        'updatedOn' => $trackerDataResponse['results']['data'][0]['updatedOn']
                    );
                } else {
                    $trackerData = false;
                }
                $data = array(
                    'tracker_data' => $trackerData,
                    'min_value' => $customer['min_val_progerss_bar'],
                    'max_value' => $customer['max_val_progerss_bar'],
                    'current_slab' => $customerData['response']['customers']['customer'][0]['current_slab'],
                    'tiers_count' => $customer['total_num_tier'],
                    'tiers' => $customer['tier_data']
                );
                return $data;
            } catch (Throwable $e) {
                \Log::info('Tracker Error: ' . $e->getMessage());
                return $e->getMessage();
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $data = array(
                'tracker_data' => null,
                'min_value' => null,
                'max_value' => null,
                'current_slab' => null,
                'tiers_count' => null,
                'tiers' => null
            );
            return $data;
        }

    }


    /**
     * Check either pilot progiram is enable or not and user is part of pilot program or not
     * @param $email string
     * @param $domain string
     */
    public function isPilotProgram($email, $domain)
    {
        $user = User::where('name', $domain)->orWhere('alternate_name', $domain)->first();
        $customer = Customer::where('user_id', $user->id)->first();
        $auth = CapilleryAuthenticate::where('user_id', $user->id)->first();
        $client = new Client(commonFunction::getClientParams($auth));

        $url = config('global.api-version') . '/' . config('global.get-customer') . '?mlp=true&user_id=true&email=' . $email;
        $result = $client->request('GET', $url);
        $dataPilot = json_decode($result->getBody(), true);
        // return $data['response']['customers']['customer'][0]['custom_fields']['field'];
        foreach ($dataPilot['response']['customers']['customer'][0]['custom_fields']['field'] as $custom_fields) {
            // return $custom_fields['name'];
            if ($custom_fields['name'] == $customer->pilot_custom_field && $custom_fields['value'] == $customer->pilot_custom_field_value) {
                $in_pilot = 1;
                break;
            } else {
                $in_pilot = 0;
            }
        }

        return $data['response'] = array('in_pilot' => $in_pilot);
    }

    /**
     * it will use to create draft order
     * @param $request object
     */

     public function applyDiscount($productIDArray,$lineItems,$draftOrder,$description,$request)  {

        foreach($productIDArray as $product)
        {
            $k=0;
            foreach($lineItems as $lineItem)
            {
                
                if($product['id'] == $lineItem['variant_id'])
                {
                    if(array_key_exists('applied_discount',$lineItem) && sizeof($lineItem['applied_discount'])>0)
                    {
                        $discountValue=$product['discount_amount']+$draftOrder['draft_order']['line_items'][$k]['applied_discount']['amount'];
                        $draftOrder['draft_order']['line_items'][$k]['applied_discount']['value_type']="fixed_amount";
                        $draftOrder['draft_order']['line_items'][$k]['applied_discount']['description']=$description;
                        $draftOrder['draft_order']['line_items'][$k]['applied_discount']['amount']=$discountValue;
                        $draftOrder['draft_order']['line_items'][$k]['applied_discount']['value']=$discountValue/$lineItem['quantity'];
                        $draftOrder['draft_order']['line_items'][$k]['applied_discount']['title']=$draftOrder['draft_order']['line_items'][$k]['applied_discount']['title']." and CAP-Coupon #" . $request->coupon;
                    }else{
                        $discountValue = $product['discount_amount'];
                        $appliedDiscount_ = array(
                            'description' => $description,
                            'value_type' => "fixed_amount",
                            'value' => $discountValue/$lineItem['quantity'],
                            'amount' => $discountValue,
                            'title' => "CAP-Coupon #" . $request->coupon
                        );
                        $draftOrder['draft_order']['line_items'][$k]['applied_discount']=$appliedDiscount_;
                    }
                }
                $k++;
            }
        }
        return $draftOrder;
     }
    public function draftOrder(Request $request)
    {
        $user = User::where('name', $request->domain)->orWhere('alternate_name', $request->domain)->first();
        $customer = Customer::where('user_id', $user->id)->first();
        $auth = CapilleryAuthenticate::where('user_id', $user->id)->first();
        $client = new Client(commonFunction::getClientParams($auth));
        // return $request->items;
        \Log::info('Request Item : ' . json_encode($request->items));
        //\Log::info('Request Prdocu : ' . $request->productID);
        $lineItems = array();
        $main_level_discount = 0;
        $main_level_discount_name ="";
        foreach ($request->items as $item) {
            $lineItem = array(
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'],
                'quantity' => $item['quantity'],
                'price' => $item['final_price']
            );
            if (
                $item['line_level_total_discount'] && $item['line_level_total_discount'] > 0
                && $item['line_level_discount_allocations'] && count($item['line_level_discount_allocations']) > 0
            ) {
                $lineLevelDiscAllocation = $item['line_level_discount_allocations'][0];
                if ($lineLevelDiscAllocation && $lineLevelDiscAllocation['amount'] > 0) {

                    // $lineDiscAmount = number_format( (float)(($lineLevelDiscAllocation['amount'])/100), 2, '.', '');
                    $lineDiscAmount = round((float) (($lineLevelDiscAllocation['amount']) / 100), 2);
                    $lineAppliedDiscount = array(
                        'amount' => $lineDiscAmount
                    );
                    if ($lineLevelDiscAllocation['discount_application']) {
                        $lineAppliedDiscount['description'] = $lineLevelDiscAllocation['discount_application']['description'];
                        $lineAppliedDiscount['value'] = $lineLevelDiscAllocation['discount_application']['value'];
                        $lineAppliedDiscount['title'] = $lineLevelDiscAllocation['discount_application']['title'];
                        $lineAppliedDiscount['value_type'] = $lineLevelDiscAllocation['discount_application']['value_type'];
                    } else {
                        $lineAppliedDiscount['description'] = '';
                        $lineAppliedDiscount['value'] = $lineDiscAmount;
                        $lineAppliedDiscount['title'] = '';
                        $lineAppliedDiscount['amount'] = 10;
                        $lineAppliedDiscount['value_type'] = 'fixed_amount';
                    }
                    $lineItem['applied_discount'] = $lineAppliedDiscount;
                }
            }else{
                if($item['total_discount']>0 && $item['line_level_total_discount']==0)
                {
                    $main_level_discount+=$item['total_discount']/100;
                    $main_level_discount_name=$item['discounts'][0]['title']." ";
                }
            }
            array_push($lineItems, $lineItem);
        }

        $draftOrder = array(
            'draft_order' => array(
                'line_items' => $lineItems
            )
        );

        if (($request->coupon && $request->discount > 0) && ($request->point > 0 && $request->pointValue > 0)) {
            if(is_array($request->productI) && sizeof($request->productID)>0)
            {

               $draftOrder= $this->applyDiscount($request->productID,$lineItems,$draftOrder,"CAP_COUPON_AND_SHOPIFY_COUPON",$request);
                
                $discountValue =  $request->pointValue+$main_level_discount;
                $appliedDiscount = array(
                    'description' => "CAP_POINT",
                    'value_type' => "fixed_amount",
                    'value' => $discountValue,
                    'amount' => $discountValue,
                    'title' => $main_level_discount_name."Redeemed Point"
                );
                
            }
            else{
                
                $discountValue = $request->discount + $request->pointValue+$main_level_discount;
                $appliedDiscount = array(
                    'description' => "CAP_COUPON_AND_POINT",
                    'value_type' => "fixed_amount",
                    'value' => $discountValue,
                    'amount' => $discountValue,
                    'title' => $main_level_discount_name."Redeemed Point and Coupon #" . $request->coupon
                );
            }

            $note_attributes = [];
            
            
            \Log::info('CAP_COUPON_AND_POINT : ' . json_encode($appliedDiscount));
            $draftOrder['draft_order']['applied_discount'] = $appliedDiscount;
            $noteDataPoint = array(
                "name" => "Redeemed Point",
                "value" => $request->point
            );
            array_push($note_attributes, $noteDataPoint);
            $noteDataPointValue = array(
                "name" => "Redeemed Point Value",
                "value" => $request->pointValue
            );
            array_push($note_attributes, $noteDataPointValue);

            $noteTokenInfo = array(
                "name" => "Token Value",
                "value" => $request->token
            );
            array_push($note_attributes, $noteTokenInfo);
            \Log::info('note_attributes : ' . json_encode($note_attributes));
            $draftOrder['draft_order']['note_attributes'] = $note_attributes;
        } else {
            if ($request->coupon && $request->discount > 0) {

                if(is_array($request->productI) && sizeof($request->productID)>0)
            {

                $draftOrder= $this->applyDiscount($request->productID,$lineItems,$draftOrder,"CAP_COUPON_AND_SHOPIFY_COUPON",$request);
                
            }else{
                $appliedDiscount = array(
                    'description' => 'CAP_COUPON',
                    'value_type' => 'fixed_amount',
                    'value' => $request->discount+$main_level_discount,
                    'amount' => $request->discount+$main_level_discount,
                    'title' => $main_level_discount_name."CAP-Coupon #".$request->coupon
                );
                $draftOrder['draft_order']['applied_discount'] = $appliedDiscount;
            }
                
            } else if ($request->point > 0 && $request->pointValue > 0) {
                $note_attributes = [];
                $appliedDiscount = array(
                    'description' => "CAP_POINT",
                    'value_type' => "fixed_amount",
                    'value' => $request->pointValue+$main_level_discount,
                    'amount' => $request->pointValue+$main_level_discount,
                    'title' => $main_level_discount_name."Redeemed Point"
                );
                $draftOrder['draft_order']['applied_discount'] = $appliedDiscount;
                $noteDataPoint = array(
                    "name" => "Redeemed Point",
                    "value" => $request->point
                );
                array_push($note_attributes, $noteDataPoint);
                $noteDataPointValue = array(
                    "name" => "Redeemed Point Value",
                    "value" => $request->pointValue
                );
                array_push($note_attributes, $noteDataPointValue);
                $noteTokenInfo = array(
                    "name" => "Token Value",
                    "value" => $request->token
                );
                array_push($note_attributes, $noteTokenInfo);

                \Log::info('note_attributes : ' . json_encode($note_attributes));
                $draftOrder['draft_order']['note_attributes'] = $note_attributes;
            }
        }

        if($main_level_discount>0 && !array_key_exists("applied_discount",$draftOrder['draft_order']))
        {
            $appliedDiscount = array(
                'description' => 'CAP_COUPON',
                'value_type' => 'fixed_amount',
                'value' => $main_level_discount,
                'amount' => $main_level_discount,
                'title' => $main_level_discount_name,
            );
            $draftOrder['draft_order']['applied_discount'] = $appliedDiscount;
        }
        \Log::info('Draft Order : ' . json_encode($draftOrder));
        $response = $user->api()->rest('POST', '/admin/api/' . config('global.shopify-version') . '/draft_orders.json', ['json' => $draftOrder]);
        // return $response['body']['draft_order'];
        return $data['response'] = array('draft_order' => $response['body']['draft_order'], 'test' => $draftOrder);
    }

    function getAllTickets($email, $domain)
    {
        $user = User::where('name', $domain)->orWhere('alternate_name', $domain)->first();
        $customer = Customer::where('user_id', $user->id)->first();
        $auth = CapilleryAuthenticate::where('user_id', $user->id)->first();
        $client = new Client(commonFunction::getClientParams($auth));

        if ($customer->ccms_enabled) {
            $searchTerm = array();
            $customerAvl = $user->api()->rest('GET', '/' . config('global.shopify-admin') . '/' . config('global.shopify-version') . '/' . config('global.shopify-customers') . '/' . $email . '.json', ['query' => $searchTerm]);
            if ($customerAvl['errors']) {
                Log::channel('error')->info('customer Details from Shopify : ' . json_encode($customerAvl));
            } else
                Log::channel('info')->info('Request Item : ' . json_encode($customerAvl));

            $user_phone = $customerAvl['body']['customer']['phone'];
            if ($user_phone == null) {
                $note_attribute = explode("\n", $customerAvl['body']['customer']['note']);
                for ($i = 0; $i < count($note_attribute); $i++) {
                    if (explode(":", $note_attribute[$i])[0] == "phone_number_full") {
                        $user_phone = str_replace(" ", "", explode(":", $note_attribute[$i])[1]);
                    }
                }
            }

            $identifierColumn = "email";
            if ($user_phone != null) {
                $identifierColumn = "mobile";
            } else {
                $user_phone = $customerAvl['body']['customer']['email'];
            }

            $url = config('global.api-version') . '/' . config('global.get-tickets') . '?format=json&' . $identifierColumn . '=' . $user_phone;
            try {
                $result = $client->request('GET', $url);
                return json_decode($result->getBody(), true);
            } catch (Throwable $e) {
                \Log::channel('error')->info('Get all tickets Error: ' . $e->getMessage());
                return $e->getMessage();
            }
        }
        return false;
    }

    function createTicket(Request $request)
    {
        $user = User::where('name', $request->domain)->orWhere('alternate_name', $request->domain)->first();
        $customer = Customer::where('user_id', $user->id)->first();
        $auth = CapilleryAuthenticate::where('user_id', $user->id)->first();
        $client = new Client(commonFunction::getClientParams($auth));

        if ($customer->ccms_enabled) {
            $searchTerm = array();
            $customerAvl = $user->api()->rest('GET', '/' . config('global.shopify-admin') . '/' . config('global.shopify-version') . '/' . config('global.shopify-customers') . '/' . $request->email . '.json', ['query' => $searchTerm]);
            if ($customerAvl['errors']) {
                Log::channel('error')->info('customer Details from Shopify : ' . json_encode($customerAvl));
            } else
                Log::channel('info')->info('Request Item : ' . json_encode($customerAvl));

            $user_phone = $customerAvl['body']['customer']['phone'];
            if ($user_phone == null) {
                $note_attribute = explode("\n", $customerAvl['body']['customer']['note']);
                for ($i = 0; $i < count($note_attribute); $i++) {
                    if (explode(":", $note_attribute[$i])[0] == "phone_number_full") {
                        $user_phone = str_replace(" ", "", explode(":", $note_attribute[$i])[1]);
                    }
                }
            }

            $identifierColumn = "email";
            if ($user_phone != null) {
                $identifierColumn = "mobile";
            } else {
                $user_phone = $customerAvl['body']['customer']['email'];
            }

            $customFieldsInfo = array();
            if ($request->custom_fields) {
                foreach ($request->custom_fields as $customField) {
                    $customFieldInfo = $customField;
                    array_push($customFieldsInfo, $customFieldInfo);
                }
            }

            $ticketInfo = [
                "code" => $request->code,
                "status" => $request->status,
                "subject" => $request->subject,
                "priority" => $request->priority,
                "department" => $request->department,
                "message" => $request->message,
                "assigned_to" => $request->assigned_to,
                "custom_fields" => [
                    "field" => $customFieldsInfo
                ]
            ];

            $customerInfo = [
                [
                    $identifierColumn => $user_phone,
                    "ticket" => $ticketInfo
                ]
            ];

            $postBody = [
                "root" => [
                    "customer" => $customerInfo
                ]
            ];

            $url = config('global.api-version') . '/' . config('global.get-tickets') . '?format=json&' . $identifierColumn . '=' . $user_phone;
            try {
                Log::channel('info')->info('Request Item for createTicket : ' . json_encode($postBody));
                $result = $client->request('POST', $url, ['json' => $postBody]);
                Log::channel('info')->info('resp Item for createTicket : ' . $result->getBody());
                return json_decode($result->getBody(), true);
            } catch (Throwable $e) {
                \Log::channel('error')->info('Get all tickets Error: ' . $e->getMessage());
                return $e->getMessage();
            }
        }
        return false;
    }
    function healthCheck()
    {
        return ["Status" => 200];
    }
}