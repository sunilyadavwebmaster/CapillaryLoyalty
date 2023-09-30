<?php

namespace App\Http\Middleware;

use Assert\AssertionFailedException;
use Closure;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Osiset\ShopifyApp\Contracts\ApiHelper as IApiHelper;
use Osiset\ShopifyApp\Contracts\Objects\Values\ShopDomain as ShopDomainValue;
use Osiset\ShopifyApp\Contracts\Queries\Shop as IShopQuery;
use Osiset\ShopifyApp\Contracts\ShopModel;
use Osiset\ShopifyApp\Exceptions\HttpException;
use Osiset\ShopifyApp\Exceptions\SignatureVerificationException;
use Osiset\ShopifyApp\Objects\Enums\DataSource;
use Osiset\ShopifyApp\Objects\Values\NullableSessionId;
use Osiset\ShopifyApp\Objects\Values\SessionContext;
use Osiset\ShopifyApp\Objects\Values\SessionToken;
use Osiset\ShopifyApp\Objects\Values\ShopDomain;
use Osiset\ShopifyApp\Util;
use Osiset\ShopifyApp\Http\Middleware\VerifyShopify;

class RewardAuth extends VerifyShopify
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Verify the HMAC (if available)
        $hmacResult = $this->verifyHmac($request);
        if ($hmacResult === false) {
            // Invalid HMAC
            throw new SignatureVerificationException('Unable to verify signature.');
        }

        // Continue if current route is an auth or billing route
        if (Str::contains($request->getRequestUri(), ['/authenticate', '/billing'])) {
            return $next($request);
        }

        $tokenSource = $this->getAccessTokenFromRequest($request);
        if ($tokenSource === null) {
            //Check if there is a store record in the database
            return $this->checkPreviousInstallation($request)
                // Shop exists, token not available, we need to get one
                ? $this->handleMissingToken($request)
                // Shop does not exist
                : $this->handleInvalidShop($request);
        }

        try {
            // Try and process the token
            $token = SessionToken::fromNative($tokenSource);
        } catch (AssertionFailedException $e) {
            // Invalid or expired token, we need a new one
            return $this->handleInvalidToken($request, $e);
        }

        // Set the previous shop (if available)
        if ($request->user()) {
            $this->previousShop = $request->user();
        }

        // Login the shop
        $loginResult = $this->loginShopFromToken(
            $token,
            NullableSessionId::fromNative($request->query('session'))
        );
        if (! $loginResult) {
            // Shop is not installed or something is missing from it's data
            return $this->handleInvalidShop($request);
        } else {
            // $shop = Auth::user();

            $themes = $this->auth->api()->rest('GET', '/admin/themes.json');
            // get active theme id
            $activeThemeId = "";
            foreach($themes['body']->container['themes'] as $theme){
                if($theme['role'] == "main"){
                    $activeThemeId = $theme['id'];
                }
            }

            print_r($themes); die;
        }

        return $next($request);
    }
}
