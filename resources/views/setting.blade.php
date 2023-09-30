@extends('shopify-app::layouts.default')
@extends('layouts.app')
@section('content')
    <div class="content">
        <div id="loading-image">
            <img src="{{ URL::to('assets/images/loader.gif') }}" />
        </div>
        @include('layouts.sidebar')
        <div class="settings">
            <div class="alert alert-success">
                <strong>Success!</strong> Record has beed updated!
            </div>
            <div class="alert alert-danger">
                <strong>Faild!</strong> Something went wrong!
            </div>
            <form action="{{ route('settings.save') }}" method="post" id="customerForm">
                @csrf
                <div class="row">
                    <div class="col">
                        <div class="tabs">
                            <div class="tab">
                                <input type="checkbox" class="tab_control" id="t_customer" checked>
                                <label class="tab-label" for="t_customer">Customer</label>
                                <div class="tab-content">
                                    <div class="form-group">
                                        <label for="creation">Enabled Customer Creation</label>
                                        <select class="form-control" class="form-control" name="creation" id="creation">
                                            <option value="1">Yes</option>
                                            <option value="0" <?php if($data['creation'] == 0) { echo "selected=selected"; } ?>>No</option>
                                        </select>
                                        @error('creation')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('creation') }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="updation">Enabled Customer Updation</label>
                                        <select class="form-control" class="form-control" name="updation" id="updation">
                                            <option value="1">Yes</option>
                                            <option value="0" <?php if($data['updation'] == 0) { echo "selected=selected"; } ?>>No</option>
                                        </select>
                                        @error('updation')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('updation') }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="fetch">Enabled Customer Fetch</label>
                                        <select class="form-control" class="form-control" name="fetch" id="fetch">
                                            <option value="1">Yes</option>
                                            <option value="0" <?php if($data['fetch'] == 0) { echo "selected=selected"; } ?>>No</option>
                                        </select>
                                        @error('fetch')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('fetch') }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="fetch">Enabled Customer Grouping</label>
                                        <select class="form-control" class="form-control" name="cgrouping" id="cgrouping">
                                            <option value="1">Yes</option>
                                            <option value="0" <?php if($data['grouping'] == 0) { echo "selected=selected"; } ?>>No</option>
                                        </select>
                                        <p class="note"><span>Enabling this will redeem points, show points and transactions at group level</span></p>
                                    </div>
                                    <div class="form-group">
                                        <label for="fetch">MLP Type</label>
                                        <select class="form-control" class="form-control" name="mlp" id="mlp">
                                            <option value="default">Default</option>
                                            <option value="brand" <?php if($data['mlp'] == "brand") { echo "selected=selected"; } ?>>Brand Level</option>
                                            <option value="card" <?php if($data['mlp'] == "card") { echo "selected=selected"; } ?>>Card Level</option>
                                        </select>
                                    </div>
                                    <div class ="form-group">
                                        <label> Show MLP type on Frontend</label>
                                        <select class="form-control" name="show_mlp" id="show_mlp">
                                        <option value="0" <?php if($data['show_mlp'] == 0) { echo "selected=selected"; } ?>>No</option>
                                            <option value="1" <?php if($data['show_mlp'] == 1) { echo "selected=selected"; } ?>>yes</option> 
                                        </select>
                                    </div>
                                    <div class ="form-group">
                                        <label> Show Cumulative Points</label>
                                        <select class="form-control" name="show_cumulative_points" id="show_cumulative_points">
                                        <option value="0" <?php if($data['show_cumulative_points'] == 0) { echo "selected=selected"; } ?>>No</option>
                                            <option value="1" <?php if($data['show_cumulative_points'] == 1) { echo "selected=selected"; } ?>>yes</option> 
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="tab">
                                <input type="checkbox" class="tab_control" id="t_coupon" checked>
                                <label class="tab-label" for="t_coupon">Coupons</label>
                                <div class="tab-content">
                                    <div class="form-group">
                                        <label for="enable_coupon">Coupon Redemption Enabled</label>
                                        <select class="form-control" class="form-control" name="enable_coupon" id="enable_coupon">
                                            <option value="1">Yes</option>
                                            <option value="0" <?php if($data['enable_coupon'] == 0) { echo "selected=selected"; } ?>>No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="group_coupon">Group Redemption Enabled</label>
                                        <select class="form-control" class="form-control" name="group_coupon" id="group_coupon">
                                            <option value="1">Yes</option>
                                            <option value="0" <?php if($data['group_coupon'] == 0) { echo "selected=selected"; } ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="tab">
                                <input type="checkbox" class="tab_control" id="t_points" checked>
                                <label class="tab-label" for="t_points">Points</label>
                                <div class="tab-content">
                                    <div class="form-group">
                                        <label for="enable_points">Enabled</label>
                                        <select class="form-control" class="form-control" name="enable_points" id="enable_points">
                                            <option value="1">Yes</option>
                                            <option value="0" <?php if($data['enable_points'] == 0) { echo "selected=selected"; } ?>>No</option>
                                        </select>
                                        @error('enable_points')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('enable_points') }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="min_redeem_point">Minimum Redeemable Points</label>
                                        <input class="form-control" type="text" name="min_redeem_point" id="min_redeem_point" value="@if(isset($data)){{ $data->min_redeem_point }}@endif">
                                        @error('min_redeem_point')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('min_redeem_point') }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="max_redeem_point">Maximum Redeemable Points</label>
                                        <input class="form-control" type="text" name="max_redeem_point" id="max_redeem_point" value="@if(isset($data)){{ $data->max_redeem_point }}@endif">
                                        @error('max_redeem_point')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('max_redeem_point') }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="multi_redeem_point_claim">Multiples of Redeemable Points can be claimed</label>
                                        <input class="form-control" type="text" name="multi_redeem_point_claim" id="multi_redeem_point_claim" value="@if(isset($data)){{ $data->multi_redeem_point_claim }}@endif">
                                        <p class="note"><span>Points can be claimed only on the multiples of the mentioned value</span></p>
                                        @error('multi_redeem_point_claim')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('multi_redeem_point_claim') }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="multi_redeem_point_claim">Check Cart Value for Point Redeemation</label>

                                        <select class="form-control" class="form-control" name="check_cart_value" id="check_cart_value">
                                            <option value="1">Yes</option>
                                            <option value="0" <?php if($data['check_cart_value'] == 0) { echo "selected=selected"; } ?>>No</option>
                                        </select>
                                        @error('enable_points')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('check_cart_value') }}</strong>
                                    </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="multi_redeem_point_claim">Includes Shipping Charges in Point Earning</label>

                                        <select class="form-control" class="form-control" name="shipping_charges_included" id="shipping_charges_included">
                                            <option value="1" <?php if($data['shipping_charges_included'] == 1) { echo "selected=selected"; } ?>>Yes</option>
                                            <option value="0" <?php if($data['shipping_charges_included'] == 0) { echo "selected=selected"; } ?>>No</option>
                                        </select>
                                        @error('enable_points')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('shipping_charges_included') }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="tab">
                                <input type="checkbox" class="tab_control" id="t_otp" checked>
                                <label class="tab-label" for="t_otp">OTP Verification</label>
                                <div class="tab-content">
                                    <div class="form-group">
                                        <label for="mobile_otp">Mobile OTP Enabled</label>
                                        <select class="form-control" class="form-control" name="mobile_otp" id="mobile_otp">
                                            <option value="1">Yes</option>
                                            <option value="0" <?php if($data['mobile_otp'] == 0) { echo "selected=selected"; } ?>>No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="mobile_otp_attribute">Mobile OTP Attribute</label>
                                        <input class="form-control" type="text" name="mobile_otp_attribute" id="mobile_otp_attribute" value="@if(isset($data)){{ $data->mobile_otp_attribute }}@endif">
                                        @error('mobile_otp_attribute')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('mobile_otp_attribute') }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="mobile_otp">Country Code Enabled</label>
                                        <select class="form-control" class="form-control" name="country_code" id="country_code">
                                            <option value="1">Yes</option>
                                            <option value="0" <?php if($data['country_code'] == 0) { echo "selected=selected"; } ?>>No</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="mobile_otp_attribute">Country Code Attribute</label>
                                        <input class="form-control" type="text" name="country_code_attribute" id="country_code_attribute" value="@if(isset($data)){{ $data->country_code_attribute }}@endif">
                                        @error('country_code_attribute')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('country_code_attribute') }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="email_otp">Email OTP Enabled</label>
                                        <select class="form-control" class="form-control" name="email_otp" id="email_otp">
                                            <option value="1">Yes</option>
                                            <option value="0" <?php if($data['email_otp'] == 0) { echo "selected=selected"; } ?>>No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="email_otp_attribute">Email OTP Attribute</label>
                                        <input class="form-control" type="text" name="email_otp_attribute" id="email_otp_attribute" value="@if(isset($data)){{ $data->email_otp_attribute }}@endif">
                                        @error('email_otp_attribute')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email_otp_attribute') }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="tab">
                                <input type="checkbox" class="tab_control" id="t_transction" checked>
                                <label class="tab-label" for="t_transction">Transactions</label>
                                <div class="tab-content">
                                    <div class="form-group">
                                        <label for="add_transaction">Enabled Add Transactions</label>
                                        <select class="form-control" class="form-control" name="add_transaction" id="add_transaction">
                                            <option value="1">Yes</option>
                                            <option value="0" <?php if($data['add_transaction'] == 0) { echo "selected=selected"; } ?>>No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="cancel_transaction">Enabled Cancel Transactions</label>
                                        <select class="form-control" class="form-control" name="cancel_transaction" id="cancel_transaction">
                                            <option value="1">Yes</option>
                                            <option value="0" <?php if($data['cancel_transaction'] == 0) { echo "selected=selected"; } ?>>No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="transaction_mode">Send Points redeemed as Payment method</label>
                                        <select class="form-control" class="form-control" name="transaction_mode" id="transaction_mode">
                                            <option value="1">Yes</option>
                                            <option value="0" <?php if($data['transaction_mode'] == 0) { echo "selected=selected"; } ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="tab">
                                <input type="checkbox" class="tab_control" id="t_progress" checked>
                                <label class="tab-label" for="t_progress">Progress Tracker Data</label>
                                <div class="tab-content">
                                    <div class="form-group">
                                        <label for="min_val_progerss_bar">Minimum Value of the Progress Bar</label>
                                        <input class="form-control" type="text" name="min_val_progerss_bar" id="min_val_progerss_bar" value="@if(isset($data)){{ $data->min_val_progerss_bar }}@endif">
                                        @error('min_val_progerss_bar')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('min_val_progerss_bar') }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="max_val_progerss_bar">Maximum Value of the Progress Bar</label>
                                        <input class="form-control" type="text" name="max_val_progerss_bar" id="max_val_progerss_bar" value="@if(isset($data)){{ $data->max_val_progerss_bar }}@endif">
                                        @error('max_val_progerss_bar')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('max_val_progerss_bar') }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="total_num_tier">Total Number of Tiers</label>
                                        <input class="form-control" type="text" name="total_num_tier" id="total_num_tier" value="@if(isset($data)){{ $data->total_num_tier }}@endif">
                                        @error('total_num_tier')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('total_num_tier') }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="tier_data">Tiers Data</label>
                                        <input class="form-control" type="text" name="tier_data" id="tier_data" value="@if(isset($data)){{ $data->tier_data }}@endif">
                                        <p class="note"><span>Enter the data in the json or array format, as this will be returned in API response. Example: ({1,0,100,Free Tier},{2,101,300,Bronze},{3,301,700,Silver},{4,701,100,Gold})</span></p>
                                        @error('tier_data')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('tier_data') }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="tab">
                                <input type="checkbox" class="tab_control" id="t_pilot" checked>
                                <label class="tab-label" for="t_pilot">Pilot Program</label>
                                <div class="tab-content">
                                    <div class="form-group">
                                        <label for="pilot_program">Enabled</label>
                                        <select class="form-control" class="form-control" name="pilot_program" id="pilot_program">
                                            <option value="1">Yes</option>
                                            <option value="0" <?php if($data['pilot_program'] == 0) { echo "selected=selected"; } ?>>No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="pilot_custom_field">Custom Field in Capillary</label>
                                        <input class="form-control" type="text" name="pilot_custom_field" id="pilot_custom_field" value="@if(isset($data)){{ $data->pilot_custom_field }}@endif">
                                        @error('pilot_custom_field')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('pilot_custom_field') }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="pilot_custom_field_value">Custom Field Value in Capillary</label>
                                        <input class="form-control" type="text" name="pilot_custom_field_value" id="pilot_custom_field_value" value="@if(isset($data)){{ $data->pilot_custom_field_value }}@endif">
                                        @error('pilot_custom_field_value')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('pilot_custom_field_value') }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="tab">
                                <input type="checkbox" class="tab_control" id="t_reward" checked>
                                <label class="tab-label" for="t_reward">Reward Catalog and Gamification</label>
                                <div class="tab-content">
                                    <div class="form-group">
                                        <label for="reward_catalog_enabled">Reward Catalog Enabled</label>
                                        <select class="form-control" class="form-control" name="reward_catalog_enabled" id="reward_catalog_enabled">
                                            <option value="1">Yes</option>
                                            <option value="0" <?php if($data['reward_catalog_enabled'] == 0) { echo "selected=selected"; } ?>>No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="gamification_enabled">Gamification Enabled</label>
                                        <select class="form-control" class="form-control" name="gamification_enabled" id="gamification_enabled">
                                            <option value="1">Yes</option>
                                            <option value="0" <?php if($data['gamification_enabled'] == 0) { echo "selected=selected"; } ?>>No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="brand_name">Brand Name</label>
                                        <input class="form-control" type="text" name="brand_name" id="brand_name" value="@if(isset($data)){{ $data->brand_name }}@endif">
                                        @error('brand_name')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('brand_name') }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="brand_username">Brand Username</label>
                                        <input class="form-control" type="text" name="brand_username" id="brand_username" value="@if(isset($data)){{ $data->brand_username }}@endif">
                                        @error('brand_username')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('brand_username') }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="tab">
                                <input type="checkbox" class="tab_control" id="t_ccms" checked>
                                <label class="tab-label" for="t_ccms">CCMS</label>
                                <div class="tab-content">
                                    <div class="form-group">
                                        <label for="ccms_enabled">CCMS Enabled</label>
                                        <select class="form-control" class="form-control" name="ccms_enabled" id="ccms_enabled">
                                            <option value="1">Yes</option>
                                            <option value="0" <?php if($data['ccms_enabled'] == 0) { echo "selected=selected"; } ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mb-2" name="submit">Submit</button>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    @parent

    <script>
        $('#customerForm').on('submit',function(e){
            $('#loading-image').css('display','flex');
            e.preventDefault();
            let creation = $('#creation').val();
            let updation = $('#updation').val();
            let fetch = $('#fetch').val();
            let grouping = $('#cgrouping').val();
            let add_transaction = $('#add_transaction').val();
            let enable_points = $('#enable_points').val();
            let min_redeem_point = $('#min_redeem_point').val();
            let max_redeem_point = $('#max_redeem_point').val();
            let multi_redeem_point_claim = $('#multi_redeem_point_claim').val();
            let cancel_transaction = $('#cancel_transaction').val();
            let transaction_mode = $("#transaction_mode").val();
            let mobile_otp = $('#mobile_otp').val();
            let mobile_otp_attribute = $('#mobile_otp_attribute').val();
            let email_otp = $('#email_otp').val();
            let email_otp_attribute = $("#email_otp_attribute").val();
            let min_val_progerss_bar = $("#min_val_progerss_bar").val();
            let max_val_progerss_bar = $("#max_val_progerss_bar").val();
            let total_num_tier = $("#total_num_tier").val();
            let tier_data = $("#tier_data").val();
            let pilot_program = $("#pilot_program").val();
            let pilot_custom_field = $("#pilot_custom_field").val();
            let pilot_custom_field_value = $("#pilot_custom_field_value").val();
            let enable_coupon = $("#enable_coupon").val();
            let group_coupon = $("#group_coupon").val();
            let check_cart_value=$('#check_cart_value').val();
            let country_code=$('#country_code').val();
            let country_code_attribute=$('#country_code_attribute').val();
            let shipping_charges_included=$('#shipping_charges_included').val();
            let mlp = $('#mlp').val();
            let reward_catalog_enabled = $('#reward_catalog_enabled').val();
            let gamification_enabled = $('#gamification_enabled').val();
            let brand_name = $('#brand_name').val();
            let brand_username = $('#brand_username').val();
            let ccms_enabled = $('#ccms_enabled').val();
            let show_mlp= $('#show_mlp').val();
            let show_cumulative_points=$('#show_cumulative_points').val();


            $.ajax({
                url: "{{ route('settings.save') }}",
                type:"POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    creation:creation,
                    updation:updation,
                    fetch:fetch,
                    grouping:grouping,
                    add_transaction:add_transaction,
                    enable_points:enable_points,
                    min_redeem_point:min_redeem_point,
                    max_redeem_point:max_redeem_point,
                    multi_redeem_point_claim:multi_redeem_point_claim,
                    cancel_transaction:cancel_transaction,
                    transaction_mode:transaction_mode,
                    mobile_otp:mobile_otp,
                    mobile_otp_attribute:mobile_otp_attribute,
                    email_otp:email_otp,
                    email_otp_attribute:email_otp_attribute,
                    min_val_progerss_bar:min_val_progerss_bar,
                    max_val_progerss_bar:max_val_progerss_bar,
                    total_num_tier:total_num_tier,
                    tier_data:tier_data,
                    pilot_program:pilot_program,
                    pilot_custom_field:pilot_custom_field,
                    pilot_custom_field_value:pilot_custom_field_value,
                    enable_coupon:enable_coupon,
                    group_coupon:group_coupon,
                    country_code:country_code,
                    country_code_attribute:country_code_attribute,
                    check_cart_value:check_cart_value,
                    shipping_charges_included:shipping_charges_included,
                    mlp: mlp,
                    reward_catalog_enabled: reward_catalog_enabled,
                    gamification_enabled: gamification_enabled,
                    brand_name: brand_name,
                    brand_username: brand_username,
                    ccms_enabled: ccms_enabled,
                    show_mlp:show_mlp,
                    show_cumulative_points:show_cumulative_points

                },
                success:function(response){
                    $('.alert-success').css({'right':'5%','opacity':'1'});
                    setTimeout(function() {
                        $('.alert-success').css({'right':'-100%','opacity':'0'});
                    }, 2000);
                },
                error:function(){
                    $('.alert-danger').css({'right':'5%','opacity':'1'});
                    setTimeout(function() {
                        $('.alert-danger').css({'right':'-100%','opacity':'0'});
                    }, 2000);
                },
                complete:function(){
                    $('#loading-image').hide();
                }
            });
        });
        actions.TitleBar.create(app, { title: 'Customers' });
    </script>
@endsection

@extends('layouts.footer')
