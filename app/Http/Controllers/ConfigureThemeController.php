<?php

namespace App\Http\Controllers;

use App\ThemeConfigure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Routing\UrlGenerator;

class ConfigureThemeController extends Controller
{

    protected $url;

    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
    }

    public function index(Request $request) {

        $user = Auth::user();
        $themeConfigStatus = ThemeConfigure::where('user_id', $user->id)->first();

        $themes = $user->api()->rest('GET', '/admin/themes.json');
        // get active theme id
        $activeThemeId = "";
        foreach($themes['body']['container']['themes'] as $theme){
            if($theme['role'] == "main"){
                $activeThemeId = $theme['id'];
            }
        }

        // delete old script and add new configure
        $scriptTags = $user->api()->rest('GET', '/admin/api/'.config('global.shopify-version').'/script_tags.json');
        foreach($scriptTags['body']['script_tags'] as $stag){
            $user->api()->rest('DELETE', '/admin/api/'.config('global.shopify-version').'/script_tags/'.$stag['id'].'.json');
        }

        $scriptParam = array('script_tag' => array('event' => 'onload', 'src' => 'https://code.jquery.com/jquery-3.6.0.min.js'));
        $user->api()->rest('POST', '/admin/api/'.config('global.shopify-version').'/script_tags.json', ['json' =>$scriptParam]);

        $scriptParam = array('script_tag' => array('event' => 'onload', 'src' => $this->url->to('/').'/assets/js/cap-reward-app.js'));
        $user->api()->rest('POST', '/admin/api/'.config('global.shopify-version').'/script_tags.json', ['json' =>$scriptParam]);

        // add snipet to theme
        $accountSnippeet = view('shopify.customerAccount')->render();
        $snippet = "<div class='cap-coupon-point' id='cap-customer-data' cap-data-email='{{customer.email}}' cap-base-url='".$this->url->to('/')."'>".view('shopify.customerAccount')->render()."</div>";
        $snippetParam = array('asset' => array('key' => 'snippets/cap-customer.liquid', 'value' => $snippet));
        $user->api()->rest('PUT', '/admin/api/'.config('global.shopify-version').'/themes/'.$activeThemeId.'/assets.json', ['json' =>$snippetParam]);
        
        $accountTemplate = "<div class='cap-coupon-point' id='cap-customer-data' cap-data-email='{{customer.email}}' cap-base-url='".$this->url->to('/')."'>".view('shopify.customerAccount')->render()."</div>";
        $addsnippetParam = array('asset' => array('key' => 'templates/customers/account.liquid', 'value' => $accountTemplate));
        $user->api()->rest('PUT', "/admin/api/'.config('global.shopify-version').'/themes/".$activeThemeId."/assets.json",$addsnippetParam);

        return $this->showPointSlab();

        // return ['message' => 'Theme setup succesfully'];


        
    }

    public function showPointSlab(){
        $user = Auth::user();
        $themeConfig = ThemeConfigure::where('user_id', $user->id)->first();

        $themes = $user->api()->rest('GET', '/admin/themes.json');
        // get active theme id
        $activeThemeId = "";
        foreach($themes['body']['container']['themes'] as $theme){
            if($theme['role'] == "main"){
                $activeThemeId = $theme['id'];
            }
        }

        $slabSection = "<div class='cap-coupon-point' id='cap-customer-data' cap-data-email='{{customer.email}}' cap-base-url='".$this->url->to('/')."'>".view('shopify.customerSlabSnippet')->render()."</div>";
        $sectionParam = array('asset' => array('key' => 'sections/cap-customer-slab.liquid', 'value' => $slabSection));
        $user->api()->rest('PUT', '/admin/api/'.config('global.shopify-version').'/themes/'.$activeThemeId.'/assets.json', ['json' =>$sectionParam]);

        // Add customer slab snippet
        $slabSnippet = "<div class='cap-coupon-point' id='cap-customer-data' cap-data-email='{{customer.email}}' cap-base-url='".$this->url->to('/')."'>{% if customer %}
<div class='cap-slab-wrap page-width' id='cap-slab-wrap'></div>{% endif %}</div>";
        $snippetParam = array('asset' => array('key' => 'snippets/cap-customer-slab.liquid', 'value' => $slabSnippet));
        $user->api()->rest('PUT', '/admin/api/'.config('global.shopify-version').'/themes/'.$activeThemeId.'/assets.json', ['json' =>$snippetParam]);

        // set transalate
        // return $user->api()->rest('GET', '/admin/api/'.config('global.shopify-version').'/themes/'.$activeThemeId.'/assets.json?assets[key]=config/settings_schema.json');

        return ['message' => 'Section setup succesfully'];
    }
}
