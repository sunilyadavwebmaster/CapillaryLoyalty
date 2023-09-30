<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('flush', function () {
    request()->session()->flush();
});

Route::middleware(['verify.shopify', 'billable'])->group(function () {

    Route::get('webhooks', function () {
        $shop = Auth::user();
        $data = $shop->api()->rest('GET', '/admin/webhooks.json');
        dd($data);
    });


    Route::get('/', 'HomeController@index')->name('home');


    /** Configure Theme **/
    Route::post('capillary/activate', 'ConfigureThemeController@index')->name('theme.configure');
    Route::post('capillary/showPointSlab', 'ConfigureThemeController@showPointSlab')->name('theme.showPointSlab');

    /** Authenticate Route **/
    Route::get('/capillery-authenticate/{id}', 'AuthenticateController@index')->name('shop.capilleryAuthenticate');
    Route::post('/capillery-authenticate/save', 'AuthenticateController@store')->name('shop.authenticateSave');

    /** Field Map Route **/
    Route::get('/capillary/customer/attribute', 'FieldMapController@CustomerFields')->name('capillary.customer_attribute');
    Route::get('/capillary/transaction/attribute', 'FieldMapController@TransactionFields')->name('capillary.transaction_attribute');
    Route::get('/capillary/attribute/edit/form/{type}/{id}', 'FieldMapController@capillaryFieldsForm')->name('capillary.attributeForm');
    Route::post('/capillary/attribute/edit', 'FieldMapController@capillaryFieldsEdit')->name('capillary.attributeEdit');
    Route::get('/capillary/attribute/delete/{id}', 'FieldMapController@deleteAttribute')->name('capillary.attributeDelete');

    /** Configuration Route **/
    Route::get('/settings', 'SettingsController@index')->name('settings.view');
    Route::post('/settings/save', 'SettingsController@save')->name('settings.save');
    Route::post('/page/select', 'SettingsController@pageSelect')->name('page.save');

    /** Payment Map Route **/
    Route::get('/capillary/payments', 'CapPaymentController@index')->name('capillary.payments');
    Route::get('/capillary/payments/data/form/{id}', 'CapPaymentController@dataForm')->name('capillary.dataForm');
    Route::post('/capillary/payments/save', 'CapPaymentController@paymentSave')->name('capillary.paymentSave');
    Route::get('/capillary/payments/delete/{id}', 'CapPaymentController@deletePayment')->name('capillary.deletePayment');

    /* Update primary identifier */
    Route::get('/search-customer', 'UpdateCustomerDetails@index')->name('search-customer');
    Route::post('/get-customer', 'UpdateCustomerDetails@getCustomer')->name('get-customer');
    Route::post('/update-customer', 'UpdateCustomerDetails@updateCustomer')->name('update-customer');

    /* update domain */
    Route::get('/update-domain/{id}', 'UpdateDomain@index')->name('update-domain');
    Route::post('/alternative-domain', 'UpdateDomain@update')->name('alternative-domain');

});

Route::middleware(['auth.api'])->group(function () {
Route::get('customer/getData/{email}/{domain}', 'CustomerController@getData')->name('customer.point');
Route::get('customer/getMlpInfo/{email}/{domain}', 'CustomerController@getMlpInfo')->name('customer.mlp');
Route::get('customer/shpopify_auth_callback', 'CustomerController@shpopify_auth_callback');
Route::get('customer/getProfile/{email}/{domain}', 'CustomerController@getProfile')->name('customer.profile');
Route::get('customer/getCouponHistory/{id}/{email}/{domain}', 'CustomerController@getCouponHistory')->name('customer.coupon');
Route::get('customer/getTransactionHistory/{email}/{domain}', 'CustomerController@getTransactionHistory')->name('customer.transaction');
Route::get('customer/getSettings/{domain}/{token}', 'CustomerController@getSettings')->name('customer.getSettings');
Route::post('customer/sendOTP', 'CustomerController@sendOTP')->name('customer.sendOTP');
Route::post('customer/validateOTP', 'CustomerController@validateOTP')->name('customer.validateOTP');
Route::get('customer/isRedeemablePoint/{point}/{email}/{domain}/{token}/{mlpId}', 'CustomerController@isRedeemablePoint')->name('customer.isRedeemablePoint');
Route::post('customer/isRedeemableCoupon', 'CustomerController@isRedeemableCoupon')->name('customer.isRedeemableCoupon');
Route::get('customer/tracker/{email}/{domain}', 'CustomerController@tracker')->name('customer.tracker');
Route::get('customer/ispilotprogram/{email}/{domain}', 'CustomerController@isPilotProgram')->name('customer.isPilotProgram');
Route::post('customer/draftOrder/', 'CustomerController@draftOrder')->name('customer.draftOrder');
Route::get('reward/brand/{domain}', 'RewardCatalogController@getBrandRewards')->name('reward.brand');
Route::get('vouchers/brand/{domain}', 'RewardCatalogController@issueBrandRewards')->name('vouchers.brand');
Route::get('gamification/getAll/{email}/{domain}', 'GamificationController@getAllGames')->name('games.getAll');
Route::get('gamification/getById/{email}/{gameId}/{domain}', 'GamificationController@getGameByUserId')->name('games.getById');
Route::get('customer/getAllTickets/{email}/{domain}', 'CustomerController@getAllTickets')->name('customer.getAllTickets');
Route::post('customer/createTicket', 'CustomerController@createTicket')->name('customer.createTicket');
Route::get('customer/cancelPoint/{token}', 'CustomerController@cancelPoint')->name('customer.cancelPoint');
});
Route::get('/healthcheck', 'CustomerController@healthCheck');