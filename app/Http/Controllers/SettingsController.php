<?php

namespace App\Http\Controllers;

use App\Customer;
use App\shopPages;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{

    protected $url;

    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
    }

    public function index()
    {
        $user = Auth::user();
        $data = Customer::where('user_id', $user->id)->first();
        if (!$data) {
            $customer = new Customer;
            $customer->user_id = $user->id;
            $customer->creation = 1;
            $customer->updation = 1;
            $customer->fetch = 1;
            $customer->grouping = 0;
            $customer->add_transaction = 1;
            $customer->enable_points = 1;
            $customer->min_redeem_point = 5;
            $customer->max_redeem_point = 35;
            $customer->multi_redeem_point_claim = 2;
            $customer->cancel_transaction = 1;
            $customer->transaction_mode = 1;
            $customer->mobile_otp = 0;
            $customer->mobile_otp_attribute = 'RegisterForm-Phone';
            $customer->email_otp = 0;
            $customer->email_otp_attribute = 'RegisterForm-email';
            $customer->min_val_progerss_bar = 0;
            $customer->max_val_progerss_bar = 1000;
            $customer->total_num_tier = 4;
            $customer->tier_data = '({1,0,100,Free Tier},{2,101,300,Bronze},{3,301,700,Silver},{4,701,100,Gold})';
            $customer->pilot_program = 0;
            $customer->pilot_custom_field = 'pilot';
            $customer->pilot_custom_field_value = 'yes';
            $customer->enable_coupon = 1;
            $customer->group_coupon = 0;
            $customer->country_code=0;
            $customer->country_code_attribute="";
            $customer->reward_catalog_enabled = 0;
            $customer->gamification_enabled = 0;
            $customer->brand_name = "";
            $customer->brand_username = "";
            $customer->ccms_enabled = 0;
            $customer->show_mlp=0;
            $customer->show_cumulative_points=0;
            $customer->save();
            $data = Customer::where('user_id', $user->id)->first();
        }
        return view('setting')->with('data', $data);

    }

    public function save(Request $request)
    {
        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();
        if ($customer) {
            $customer = Customer::where('user_id', $user->id)->first();
            $data = array(
                'creation' => $request->creation,
                'updation' => $request->updation,
                'fetch' => $request->fetch,
                'grouping' => $request->grouping,
                'add_transaction' => $request->add_transaction,
                'enable_points' => $request->enable_points,
                'min_redeem_point' => $request->min_redeem_point,
                'max_redeem_point' => $request->max_redeem_point,
                'multi_redeem_point_claim' => $request->multi_redeem_point_claim,
                'cancel_transaction' => $request->cancel_transaction,
                'transaction_mode' => $request->transaction_mode,
                'mobile_otp' => $request->mobile_otp,
                'mobile_otp_attribute' => $request->mobile_otp_attribute,
                'email_otp' => $request->email_otp,
                'email_otp_attribute' => $request->email_otp_attribute,
                'min_val_progerss_bar' => $request->min_val_progerss_bar,
                'max_val_progerss_bar' => $request->max_val_progerss_bar,
                'total_num_tier' => $request->total_num_tier,
                'tier_data' => $request->tier_data,
                'pilot_program' => $request->pilot_program,
                'pilot_custom_field' => $request->pilot_custom_field,
                'pilot_custom_field_value' => $request->pilot_custom_field_value,
                'enable_coupon' => $request->enable_coupon,
                'group_coupon' => $request->group_coupon,
                'check_cart_value'=>$request->check_cart_value,
                'country_code'=>$request->country_code,
                'country_code_attribute'=>$request->country_code_attribute,
                'shipping_charges_included'=>$request->shipping_charges_included,
                'mlp'=> $request->mlp,
                'reward_catalog_enabled' => $request->reward_catalog_enabled,
                'gamification_enabled' => $request->gamification_enabled,
                'brand_name' => $request->brand_name,
                'brand_username' => $request->brand_username,
                'ccms_enabled' => $request->ccms_enabled,
                'show_mlp'=>$request->show_mlp,
                'show_cumulative_points'=>$request->show_cumulative_points
            );
            $customer->update($data);
        } else {
            $customer = new Customer;
            $customer->user_id = $user->id;
            $customer->creation = $request->creation;
            $customer->updation = $request->updation;
            $customer->fetch = $request->fetch;
            $customer->grouping = $request->cgrouping;
            $customer->add_transaction = $request->add_transaction;
            $customer->enable_points = $request->enable_points;
            $customer->min_redeem_point = $request->min_redeem_point;
            $customer->max_redeem_point = $request->max_redeem_point;
            $customer->multi_redeem_point_claim = $request->multi_redeem_point_claim;
            $customer->cancel_transaction = $request->cancel_transaction;
            $customer->transaction_mode = $request->transaction_mode;
            $customer->mobile_otp = $request->mobile_otp;
            $customer->mobile_otp_attribute = $request->mobile_otp_attribute;
            $customer->email_otp = $request->email_otp;
            $customer->email_otp_attribute = $request->email_otp_attribute;
            $customer->min_val_progerss_bar = $request->min_val_progerss_bar;
            $customer->max_val_progerss_bar = $request->max_val_progerss_bar;
            $customer->total_num_tier = $request->total_num_tier;
            $customer->tier_data = $request->tier_data;
            $customer->pilot_program = $request->pilot_program;
            $customer->pilot_custom_field = $request->pilot_custom_field;
            $customer->pilot_custom_field_value = $request->pilot_custom_field_value;
            $customer->enable_coupon = $request->enable_coupon;
            $customer->group_coupon = $request->group_coupon;
            $customer->check_cart_value = $request->check_cart_value;
            $customer->country_code=$request->country_code;
            $customer->country_code_attribute=$request->country_code_attribute;
            $customer->shipping_charges_included=$request->shipping_charges_included;
            $customer->mlp = $request->mlp;
            $customer->reward_catalog_enabled = $request->reward_catalog_enabled;
            $customer->gamification_enabled = $request->gamification_enabled;
            $customer->brand_name = $request->brand_name;
            $customer->brand_username = $request->brand_username;
            $customer->ccms_enabled = $request->ccms_enabled;
            $customer->show_mlp=$request->show_mlp;
            $customer->show_cumulative_points=$request->show_cumulative_points;
            $customer->save();
        }
    }

   /* public function saveRegisterPage($mobile_otp, $email_otp)
    {
        $user = Auth::user();

        $themes = $user->api()->rest('GET', '/admin/themes.json');

        $activeThemeId = "";
        foreach ($themes['body']['container']['themes'] as $theme) {
            if ($theme['role'] == "main") {
                $activeThemeId = $theme['id'];
            }
        }

        $shopPages = shopPages::where('user_id', $user->id)->where('page_name', 'register')->first();
        if (!$shopPages) {
            $getRegisterPage = array('asset' => array('key' => 'templates/customers/register.liquid'));
            $registerPageContent = $user->api()->rest('GET', "/admin/api/'.config('global.shopify-version').'/themes/" . $activeThemeId . "/assets.json", $getRegisterPage);

            $shopPages = new shopPages;
            $shopPages->user_id = $user->id;
            $shopPages->page_name = 'register';
            $shopPages->page_content = $registerPageContent['body']['container']['asset']['value'];
            $shopPages->save();

        }
        $shopPages = shopPages::where('user_id', $user->id)->where('page_name', 'register')->first();

        if ($shopPages && $shopPages->override == 0 && ($mobile_otp == 1 || $email_otp == 1)) {
            $registerTemplate = "<div id='cap-customer-data' cap-base-url='" . $this->url->to('/') . "'>" . view('shopify.customerRegister')->render() . "</div>";
            $registerTemplateParam = array('asset' => array('key' => 'templates/customers/register.liquid', 'value' => $registerTemplate));
            $user->api()->rest('PUT', "/admin/api/'.config('global.shopify-version').'/themes/" . $activeThemeId . "/assets.json", $registerTemplateParam);

            $shopPages = shopPages::where('user_id', $user->id)->first();
            $data = array(
                'override' => 1
            );
            $shopPages->update($data);
        }
    }


    public function saveCartPage($enable_points, $enable_coupon)
    {
        $user = Auth::user();

        $themes = $user->api()->rest('GET', '/admin/themes.json');

        $activeThemeId = "";
        foreach ($themes['body']['container']['themes'] as $theme) {
            if ($theme['role'] == "main") {
                $activeThemeId = $theme['id'];
            }
        }

        $shopPages = shopPages::where('user_id', $user->id)->where('page_name', 'cart')->first();

        if (!$shopPages) {
            $getCartPage = array('asset' => array('key' => 'sections/cart-template.liquid'));
            $cartPageContent = $user->api()->rest('GET', "/admin/api/'.config('global.shopify-version').'/themes/" . $activeThemeId . "/assets.json", $getCartPage);
            $shopPages = new shopPages;
            $shopPages->user_id = $user->id;
            $shopPages->page_name = 'cart';
            $shopPages->page_content = $cartPageContent['body']['container']['asset']['value'];
            $shopPages->override = 0;
            $shopPages->save();
        }

        $shopPages = shopPages::where('user_id', $user->id)->where('page_name', 'cart')->first();

        if ($shopPages && $shopPages->override == 0 && ($enable_points == 1 || $enable_coupon == 1)) {
            $cartTemplate = "<div id='cap-customer-data' cap-customer-email='{{ customer.email}}' cap-base-url='" . $this->url->to('/') . "'>" . view('shopify.customerCart')->render() . "</div>";
            $cartTemplateParam = array('asset' => array('key' => 'sections/cart-template.liquid', 'value' => $cartTemplate));
            $user->api()->rest('PUT', "/admin/api/'.config('global.shopify-version').'/themes/" . $activeThemeId . "/assets.json", $cartTemplateParam);

            $shopPages = shopPages::where('user_id', $user->id)->where('page_name', 'cart')->first();
            $data = array(
                'override' => 1
            );

            $shopPages->update($data);

        } else if ($shopPages && ($enable_points == 0 && $enable_coupon == 0)) {
            $cartTemplateParam = array('asset' => array('key' => 'sections/cart-template.liquid', 'value' => $shopPages->page_content));
            $user->api()->rest('PUT', "/admin/api/'.config('global.shopify-version').'/themes/" . $activeThemeId . "/assets.json", $cartTemplateParam);

            // $updatedCartPageContent = array('asset' => array('key' => 'sections/cart-template.liquid'));

            $shopPages = shopPages::where('user_id', $user->id)->where('page_name', 'cart')->first();
            $data = array(
                'override' => 0
            );
            $shopPages->update($data);
            // \Log::info("saveCartPage hide -- ". $enable_points . " - ". $enable_coupon . " - ". $shopPages);
        }



        $shopPages = shopPages::where('user_id', $user->id)->where('page_name', 'main-cart-footer')->first();
        if (!$shopPages) {
            $getMainCartFooter = array('asset' => array('key' => 'sections/main-cart-footer.liquid'));
            $main_cart_footer_Content = $user->api()->rest('GET', "/admin/api/'.config('global.shopify-version').'/themes/" . $activeThemeId . "/assets.json", $getMainCartFooter);
            $shopPages = new shopPages;
            $shopPages->user_id = $user->id;
            $shopPages->page_name = 'main-cart-footer';
            $shopPages->page_content = $main_cart_footer_Content['body']['container']['asset']['value'];
            $shopPages->override = 0;
            $shopPages->save();
        }

        $shopPages = shopPages::where('user_id', $user->id)->where('page_name', 'main-cart-footer')->first();
        if ($shopPages && $shopPages->override == 0 && ($enable_points == 1 || $enable_coupon == 1)) {
            $cartTemplate = "<div id='cap-customer-data' cap-customer-email='{{ customer.email}}' cap-base-url='" . $this->url->to('/') . "'>" . view('shopify.main-cart-footer')->render() . "</div>";
            $cartTemplateParam = array('asset' => array('key' => 'sections/main-cart-footer.liquid', 'value' => $cartTemplate));
            $user->api()->rest('PUT', "/admin/api/'.config('global.shopify-version').'/themes/" . $activeThemeId . "/assets.json", $cartTemplateParam);

            $shopPages = shopPages::where('user_id', $user->id)->where('page_name', 'main-cart-footer')->first();
            $data = array(
                'override' => 1
            );

            $shopPages->update($data);
        } else if ($shopPages && ($enable_points == 0 && $enable_coupon == 0)) {
            $cartTemplateParam = array('asset' => array('key' => 'sections/main-cart-footer.liquid', 'value' => $shopPages->page_content));
            $user->api()->rest('PUT', "/admin/api/'.config('global.shopify-version').'/themes/" . $activeThemeId . "/assets.json", $cartTemplateParam);

            $shopPages = shopPages::where('user_id', $user->id)->where('page_name', 'main-cart-footer')->first();
            $data = array(
                'override' => 0
            );
            $shopPages->update($data);
        }
    }*/
}
