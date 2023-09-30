@extends('shopify-app::layouts.default')
@extends('layouts.app')
@section('content')
    <div class="content">
        <div id="loading-image">
            <img src="{{ URL::to('assets/images/loader.gif') }}"/>
        </div>
        @include('layouts.sidebar')
        <div class="welcome-content settings">
            <div class="alert alert-success">
                <strong>Success!</strong> Configured!
            </div>
            <div class="alert alert-danger">
                <strong>Faild!</strong> Something went wrong!
            </div>
            {{--<div class="configure-button">
                <a href="#" class="primary-btn" id="configure-theme">Configure Theme</a>
            </div>--}}
            <div class="instruction">

                <h4>1. SHOPIFY USER FLOWS</h4>
                <div style="padding-left: 30px;">
                    <h6>1.1 Authentication</h6>
                    <div style="padding-left: 30px;">
                        <li>Click on Apps -> Capillary CRM Integration</li>
                        <p><img src="{{ URL::to('assets/images/8-1.png') }}" style="width: 50%;height: 80%"></p>
                        <p><img src="{{ URL::to('assets/images/home.png') }}" style="width: 50%;height: 80%"></p>
                        <li>Click on General Authenticate</li>
                        <li>Provide basic Authentication
                            <ul>
                                <li>Client ID: ************************
                                </li>
                                <li>Client Secret Key: **********************</li>
                                <li>Base URL of the API: Please contact to the Capillary Team</li>
                                <li>Submit CONFIG</li>
                            </ul>
                        </li>
                        <p><img src="{{ URL::to('assets/images/10.png') }}" style="width: 50%;height: 80%"></p>
                        </ul>
                        </p>
                        <h5>View Online store on the front-end:</h5>
                        <ul>
                            <li>Click on the Online stores</li>
                            <li>Click on the View your store</li>
                        </ul>
                        <p><img src="{{ URL::to('assets/images/11.png') }}" style="width: 50%;height: 80%"></p>
                    </div>
                    <br><br><br>
                    <h6>1.2 Steps to Configure Themes</h6>
                    <div style="padding-left: 30px;">
                        <p><b>1.2.1 Turn on the App embeds</b></p>
                        <ul>
                            <li>Click on the Online Store >> Customize</li>
                            <p><img src="{{ URL::to('assets/images/200.png') }}" style="width: 50%;height: 80%"></p>
                            <li>Select “Theme settings” and click on “App embeds”.</li>
                            <p><img src="{{ URL::to('assets/images/201.png') }}" style="width: 50%;height: 80%"></p>
                            <li>Turn on the toggle buttons “Capillary - Setting” and “Capillary - Includes ”. If you are not using Jquery in your code then please turn on the "Capillary - Jquery Files".
                            </li>
                            <p><img src="{{ URL::to('assets/images/202.png') }}" style="width: 50%;height: 80%"></p></ul>
                        <p><b>1.2.2 To Create Capillary CRM integration CART page With blocks, please follow the below
                                steps:</b></p>
                        <ul>
                            <li>Click on the Online Store >> Customize</li>
                            <p><img src="{{ URL::to('assets/images/200.png') }}" style="width: 50%;height: 80%"></p>
                            <li> For Using capillary Coupon and Redeem Points, it is required to add our App block and
                                also our checkout button.
                            </li>
                            <li>Search for Cart in search box.</li>
                            <p><img src="{{ URL::to('assets/images/204.png') }}" style="width: 50%;height: 80%"></p>
                            <li>Click on “Add block” and you can configure the blocks you need in your Cart Page.</li>
                            <p><img src="{{ URL::to('assets/images/205.png') }}" style="width: 50%;height: 80%"></p>
                            <p><img src="{{ URL::to('assets/images/206.png') }}" style="width: 50%;height: 80%"></p>

                        </ul>
                        <p><b>1.2.3 To Create Capillary CRM integration CART page Without blocks, please follow the
                                below steps:</b></p>
                        <ul>
                            <li>For Redeem point feature: To add the redeem point feature please add the following div in your CART page “< div id='cap-cart-point'>< /div>”.
                            </li>
                            <li>For Coupon feature: To add the coupon feature please add the following div in your CART page “ < div id='cap-cart-coupon'>< /div> ”.
                            </li>
                            <li>For showing the Redeem points discount block feature: To add the Redeem points discount block feature add the following div in your CART page “ < div id='cap-redeem-point-discount-value'>< /div> ”.
                            </li>
                            <li>For showing coupon discount feature: To add the coupon discount block feature add the following div in your CART page “< div id='cap-coupon-discount'>< /div>”.
                            </li>
                            <li>For Checkout Button: To add the our checkout button please add the following div in your cart page "< div id="cap-check-out-btn">< /div>"</li>

                        </ul>

                       <p>Note:-</p>
                        <ul><li>For using the Redeem Points features  and Capillary coupons features  you need to use our checkout button</li>
                        <li>And above setting will only work in Cart page</li></ul>



                        <p><b>1.2.4 To add email and mobile OTP field in the Registration page, please follow the below
                                steps:</b></p>
                        <ul>
                            <li>Go to App >> CRM Capillary integration >> Config Capillary Settings</li>
                            <p><img src="{{ URL::to('assets/images/207.png') }}" style="width: 50%;height: 80%"></p>
                            <li>Mobile OTP:
                                <ul>
                                    <li>Navigate to OTP verification subtab, wherein we need enable the “Mobile OTP
                                        Enable”.
                                    </li>
                                    <li>Enter Phone No. input field ID name in Phone otp Attribute field for fetching the phone number.</li>
                                    <p><img src="{{ URL::to('assets/images/208.png') }}" style="width: 50%;height: 80%">
                                    </p>
                                    <li>Click on Save</li>
                                    <li>Add the “Send Otp” button for mobile, we need to add this class name "send-mobile-otp-btn" in your html anchor where you want to add the button in the registration page.</li>
                                    <li>For “adding enter otp” field and “send verify otp” button please add this class name "mobile-otp-box" in your html tag where you want to show these block in the registration page.</li>
                                </ul>

                            </li>
                            <li>Email OTP:
                                <ul>
                                    <li>Navigate to OTP verification subtab, wherein we need to enable the “Email OTP
                                        Enabled”.
                                    </li>
                                    <li>Enter Email input field ID name in Email otp Attribute field for feteching the email.</li>
                                    <p><img src="{{ URL::to('assets/images/209.png') }}" style="width: 50%;height: 80%">
                                    </p>
                                    <li>Click on Save</li>
                                    <li>Add the “Send Otp” button for mobile, we need to add this class name "send-email-otp-btn" in your html anchor where you want to add the button in the registration page.</li>
                                    <li>For “adding enter otp” field and “send verify otp” button please add this class name "email-otp-box" in your html tag where you want to show these block in the registration page.</li>
                                </ul>

                            </li>


                        </ul>
                        <p><b>1.2.5 Show “Point History” block</b></p>
                        <ul>
                            <li>To show the point history block we need to add “< div id="point-history-div">< /div>” in any pages where you would like to show the point history slab.
                            </li>
                        </ul>
                        <p><b>1.2.6 Show “Coupon History” block</b></p>
                        <ul>
                            <li>To show the coupon history block we need to add “< div id="cap-coupon-div">< /div>” in any pages where you would like to show the point history slab.
                            </li>
                        </ul>
                        <p><b>1.2.7 Show “Transaction History” block</b></p>
                        <ul>
                            <li>To show the Transaction history block we need to add “< div id="cap-trans">< /div>” in any pages where you would like to show the transaction history slab.
                            </li>

                        </ul>
                        <p><b>1.2.8 Show “Available Points” block</b></p>
                        <ul>
                            <li>To show the Available points block we need to add "< div id="cap-point-id">< /div>" in any pages where you would like to show the Available Points slab.
                            </li>

                        </ul>
                        <p><b>1.2.9 Show “Slab Name” block</b></p>
                        <ul>
                            <li>To show the Slab name block we need to add “< div id="cap-tier-id">< /div>” in any pages where you would like to show the Slab name.
                            </li>
                        </ul>

                    </div>

                    <h6>1.3 Mapping of fields to custom or extended fields</h6>
                    <div style="padding-left: 30px;">
                        <p><b>1.3.1 Creation of Attributes from Shopify side</b>
                        </p>
                        <p>This configuration allows the admin to create a custom attribute for the existing fields from
                            the shopify panel.</p>
                        <ul>
                            <li>Follow the below configuration
                                <ul>
                                    <li>User should be navigated to Apps >> Capillary CRM Integration >></li>
                                    <li>Click on Capillary Tech→ Assign Attributes</li>
                                </ul>
                                <p><img src="{{ URL::to('assets/images/12.png') }}" style="width: 50%;height: 80%"></p>
                            </li>

                            <li>Click on Assign Attribute: Attribute type for Extended field
                                <p><img src="{{ URL::to('assets/images/13.png') }}" style="width: 50%;height: 80%"></p>
                            </li>
                            <li>Status: Enabled (drop down)
                            </li>
                            <li>Attribute type (drop down) : Consists of values custom field and extended field
                            </li>
                            <li>Attribute Code: Give any attribute code. Ex: Middle Name
                            </li>
                            <li>Capillary Attribute Code: Give any attribute code. Ex: state
                            </li>
                            <li>"state" attribute should be added on the capillary end as well.
                            </li>
                            <li>SAVE</li>
                        </ul>
                        <p>Attribute type for Custom field:</p>
                        <p><img src="{{ URL::to('assets/images/14.png') }}" style="width: 50%;height: 80%"></p>
                        <ul>
                            <li>Register a user on the front end.</li>
                            <li>Give a last name and first name and save the user</li>
                            <p><img src="{{ URL::to('assets/images/16.png') }}" style="width: 50%;height: 80%"></p>
                            <li>Go to capillary dashboard for the same user and check if the gender and state are
                                updated
                            </li>
                        </ul>


                    </div>
                    <br><br><br>
                    <h6>1.4 Customer Creation configuration</h6>
                    <div style="padding-left: 30px">
                        <p><b>1.4.1 Enabled Customer Creation</b></p>
                        <p>When user registers to the site, User info should be updated on capillary system </p>
                        <ul>
                            <li>Follow the below configuration
                                <ul>
                                    <li>User should be navigated to Apps >> Capillary CRM Integration >> Config
                                        Capillary settings >>
                                        Customer
                                    </li>
                                    <li>Enabled Customer Creation: YES (dropdown)</li>
                                    <li>Submit CONFIG</li>
                                    <li>Create a user on store front end</li>
                                </ul>
                                <p><img src="{{ URL::to('assets/images/21.png') }}" style="width: 50%;height: 80%"></p>
                            </li>
                            <li>Creating customer from store front-end:
                                <p><img src="{{ URL::to('assets/images/22.png') }}" style="width: 50%;height: 80%"></p>
                            </li>
                            <li>The created user will be reflected on the Shopify -> All customers
                                <p><img src="{{ URL::to('assets/images/23.png') }}" style="width: 50%;height: 80%"></p>
                            </li>
                            <li>The created user will be reflected on the Capillary Byrne Dev dashboard (search for the
                                created user xxx@domain.com)
                            </li>
                        </ul>
                        <p><b>1.4.2 Disable Customer Creation</b></p>
                        <p>When user is registered to the site, User info should not be updated on capillary system </p>
                        <ul>
                            <li>Follow the below configuration
                                <ul>
                                    <li>User should be navigated to Apps >> Capillary CRM Integration >> Config
                                        Capillary settings >>
                                        Customer
                                    </li>
                                    <li>Enabled Customer Creation: NO (dropdown)</li>
                                    <li>SAVE CONFIG</li>
                                </ul>
                                <p><img src="{{ URL::to('assets/images/25.png') }}" style="width: 50%;height: 80%"></p>
                            </li>
                            <li>Create an user on front end site
                            </li>
                            <li>The created user will be reflected on the Shopify -> All customers</li>
                            <li>The created user will not be reflected on the Capillary Byrne Dev dashboard (search for
                                the created user john@gmail.com)
                            </li>
                        </ul>
                    </div>
                    <br><br><br>
                    <h6>1.5 Customer Updation</h6>
                    <div style="padding-left: 30px;">
                        <p><b>1.5.1 Enabled Customer Updation</b></p>
                        <p>When a user updates the name from the Shopify admin, the name should be updated on the
                            capillary system. Customer edit is available from Shopify admin only</p>
                        <ul>
                            <li>Follow the below configuration
                                <ul>
                                    <li>User should be navigated to Apps >> Capillary CRM Integration >>Config Capillary
                                        settings >>
                                        Customer
                                    </li>
                                    <li>Enabled Customer Updation: YES (dropdown)</li>
                                    <p><img src="{{ URL::to('assets/images/27.png') }}" style="width: 50%;height: 80%"></p>
                                    <li>Submit CONFIG</li>
                                    <l>Navigate to Customers -> Click on the customer, wants to edit</l>
                                    <p><img src="{{ URL::to('assets/images/28.png') }}" style="width: 50%;height: 80%"></p>
                                    <li>Edit Customer from Shopify admin:</li>
                                    <p><img src="{{ URL::to('assets/images/30.png') }}" style="width: 50%;height: 80%"></p>
                                    <li>On updation the customer Details is updated in the Capillary system</li>
                                </ul>
                            </li>

                        </ul>
                        <br>
                        <br>
                        <p><b>1.5.2 Disable Customer Updation</b></p>
                        <p>When a user updates the name from the shopify admin, the name should not be updated on the
                            capillary system.</p>
                        <ul>
                            <li>Follow the below configuration
                                <ul>
                                    <li>User should be navigated to Apps >> Capillary CRM Integration >>Config Capillary
                                        settings >>
                                        Customer
                                    </li>
                                    <li>Enabled Customer Updation: NO (dropdown)</li>
                                    <li>SAVE CONFIG</li>
                                    <li>Navigate to Customers -> click on customer→ Edit Contact Information</li>
                                    <li>Change the name field (first name and last name) and save</li>
                                    <li>The name would be updated on the front end, shopify whereas it won't be updated
                                        on the Capillary dashboard
                                    </li>

                                </ul>
                            </li>
                        </ul>
                    </div>
                    <br><br><br>
                    <h6>1.6 Loyalty Points and Slab</h6>
                    <div style="padding-left: 30px">
                        <p><b>1.6.1 Enable Loyalty Points and Slab</b></p>
                        <p>When this configuration is enabled, User should be able to view the loyalty points and slab
                            name information on the site.</p>
                        <ul>
                            <li>Follow the below configuration
                                <ul>
                                    <li>User should be navigated to stores >> configuration >> Capillary tech >> CMI
                                        Integration
                                    </li>
                                    <li>Scroll down to Customer</li>
                                    <li>Enabled Customer Fetch: YES (dropdown)</li>
                                    <img src="{{ URL::to('assets/images/36.png') }}" style="width: 50%;height: 80%">
                                    <li>SAVE CONFIG</li>
                                    <li>Now go to Online Store >> Actions >> Edit Code
                                    <ul><li>Please add this div in any page where you want to show the Loyalty Points "< div id="cap-point-id">< /div>"</li>
                                        <li>Please add this div in any page where you want to show the Slab Name "< div id="cap-tier-id">< /div>"</li></ul></li>
                                    <img src="{{ URL::to('assets/images/2000.png') }}" style="width: 50%;height: 80%">
                                    <li>Login to front end site, and navigate to your page where you added the above div</li>
                                    <li>User should be able to view the Loyalty points and Slab Name</li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <br><br>
                    <p><b>1.6.2 Disable Loyalty Points and Slab</b></p>
                    <p>When this configuration is disabled User should not be able to view the loyalty points and slab
                        name information on the site.</p>
                    <ul>
                        <li>Follow the below configuration
                            <ul>
                                <li>User should be navigated to stores >> configuration >> Capillary tech >> CMI
                                    Integration
                                </li>
                                <li>Scroll down to Customer</li>
                                <li>Enabled Customer Fetch: No (dropdown)</li>
                                <img src="{{ URL::to('assets/images/39.png') }}" style="width: 50%;height: 80%">
                                <li>SAVE CONFIG</li>
                                <li>Now go to Online Store >> Actions >> Edit Code
                                    <ul><li>For Loyalty Points Please add this div in any page where you want to show "< div id="cap-point-id">< /div>"</li>
                                        <li>For Slab Name Please add this div in any page where you want to show "< div id="cap-tier-id">< /div>"</li></ul></li>
                                <img src="{{ URL::to('assets/images/2000.png') }}" style="width: 50%;height: 80%">
                                <li>Login to front end site, and navigate to your page where you added the div for Slab Name & Loyalty Points</li>
                                <li>User should not be able to view the Loyalty points and Slab Name</li>
                            </ul>
                        </li>

                    </ul>

                </div>
                <br><br><br>
                <h6>1.7 Success Transaction</h6>
                <div style="padding-left: 30px">
                    <p><b>1.7.1 Enable Success transaction</b></p>
                    <p>When the admin enables this configuration, if the user places any order or does the transactions
                        capillary system should be notified.</p>
                    <ul>
                        <li>Follow the below configuration
                            <ul>
                                <li>User should be navigated to Apps >> Capillary CRM Integration >>Config Capillary
                                    settings >>
                                    Customer
                                </li>
                                <li>Click on Capillary Tech→ CRM Integration</li>
                                <li>Scroll down to Transactions</li>
                                <li>Enabled Add Transactions : YES (dropdown)</li>
                                <li>Enable Customer grouping: Yes (dropdown)</li>
                                <img src="{{ URL::to('assets/images/41.png') }}" style="width: 50%;height: 80%">
                                <li>SAVE CONFIG</li>
                                <ul><li>Please add this div in any page where you want to show the Transaction History "< div id="cap-trans">< /div>"</li>
                                    <li>Transaction History table would look like the below image</li>
                                    <img src="{{ URL::to('assets/images/2001.png') }}" style="width: 50%;height: 80%">
                                </ul></li>
                                <li>Place an order from the frontend and navigate to your page where you added the div for Transaction History</li>
                                <li>User should be able to view the Transaction History table</li>

                            </ul>
                        </li>

                        <li>For Group User success transaction
                            <ul>
                                <li>On the capillary dashboard under group purchases of primary user, should be able to
                                    see the success transaction for the Group User
                                </li>
                                <li>On frontend primary user should be able to view Group User’s transactions</li>
                            </ul>
                        </li>
                    </ul>
                    <br><br>
                    <p><b>1.7.2 Disable Success transaction </b></p>
                    <p>When the admin disables this configuration, if the user places any order or does the transactions
                        capillary system should not be notified.</p>
                    <ul>
                        <li>Follow the below configuration
                            <ul>
                                <li>User should be navigated to Apps >> Capillary CRM Integration >>Config Capillary
                                    settings >>
                                    Customer
                                </li>
                                <li>Click on Capillary Tech→ CRM Integration</li>
                                <li>Scroll down to Transactions</li>
                                <li>Enabled Add Transactions : No (dropdown)</li>
                                <img src="{{ URL::to('assets/images/47.png') }}" style="width: 50%;height: 80%">
                                <li>SAVE CONFIG</li>
                                <li>Place an order from the frontend and navigate to your page where you added the div for Transaction History</li></li>
                                <li>User should not be able to view the transaction history after disabling the "Add Transactions"</li>
                            </ul>
                        </li>


                    </ul>
                </div>
                <br><br><br>
                <h6>1.8 Cancel Transaction (Full)</h6>
                <div style="padding-left: 30px">
                    <p><b>1.8.1 Enable Cancel transaction </b></p>
                    <p>When the admin enables this configuration, if user cancels any transaction or order, Capillary
                        system should be notified. (Order cancellation tested from Shopify admin).</p>
                    <ul>
                        <li>Follow the below configuration
                            <ul>
                                <li>User should be navigated to Apps >> Capillary CRM Integration >>Config Capillary
                                    settings >>
                                    Customer
                                </li>
                                <li>Click on Capillary Tech→ CRM Integration</li>
                                <li>Scroll down to Transactions</li>
                                <li>Enabled Cancel Transactions: YES (dropdown)</li>
                                <li>SAVE CONFIG</li>
                                <li>Enable Customer grouping: Yes (dropdown)</li>
                                <li>Place an order from the frontend</li>
                                <li>Got to Sales >> orders >> View particular order id >> click on cancel</li>
                                <li>User should be able to view the 2 orders with amount on the frontend</li>
                                <li>One with success transaction and another with cancel transaction under transaction history
                                </li>
                            </ul>
                        </li>
                        <img src="{{ URL::to('assets/images/51.png') }}" style="width: 50%;height: 80%">
                        <li>For Group User Cancel transaction
                            <ul>
                                <li>On the capillary dashboard under group purchases of primary user, should be able to
                                    see the cancel transaction for the Group User
                                </li>
                                <li>On frontend primary user should be able to view Group User’s transactions</li>
                            </ul>
                        </li>
                    </ul>
                    <br><br>
                    <p><b>1.8.2 Disable Cancel transaction </b></p>
                    <p>When the admin disables this configuration, if the user cancels any transaction or order,
                        Capillary system should not be notified. (Order cancellation tested from Shopify admin).</p>
                    <ul>
                        <li>Follow the below configuration
                            <ul>
                                <li>User should be navigated to Apps >> Capillary CRM Integration >>Config Capillary
                                    settings >>
                                    Customer
                                </li>
                                <li>Click on Capillary Tech→ CRM Integration</li>
                                <li>Scroll down to Transactions</li>
                                <li>Enabled Cancel Transactions: No (dropdown)</li>
                                <li>SAVE CONFIG</li>
                                <li>Place an order from the frontend</li>
                                <li>Got to Sales >> orders >> View particular order id >> click on cancel</li>
                                <li>User should be able to view the 1 orders with amount on the frontend</li>
                                <li>One with transaction under transaction history and but no cancel transaction will show</li>
                            </ul>
                        </li>

                        <img src="{{ URL::to('assets/images/60.png') }}" style="width: 50%;height: 80%">

                    </ul>
                </div>
                <br><br><br>
                <h6>1.9 Mapping of Transaction fields to custom or extended fields</h6>
                <div style="padding-left: 30px;">
                    <p><b>1.9.1 Creating a Transaction Attributes on Shopify admin</b></p>
                    <p>Admin will be able to create transaction attributes to map with custom/extended attributes of
                        capillary</p>
                    <ul>
                        <li>Follow the below configuration
                            <ul>
                                <li>Click on Capillary Tech→ Assign Transaction Attributes
                                    <p><img src="{{ URL::to('assets/images/62.png') }}" style="width: 50%;height: 80%">
                                    </p>
                                </li>
                                <li>Click on Assign Transaction Attributes button
                                    <p><img src="{{ URL::to('assets/images/63.png') }}" style="width: 50%;height: 80%">
                                    </p>
                                </li>
                                <li>Status: Enabled (drop down)
                                </li>
                                <li>Attribute type (drop down) : Consists of values custom field and extended field</li>
                                <li>Attribute Code: Give any transaction attribute code. Ex:
                                    total_line_items_price_set,shop_money,amount
                                </li>
                                <li>Capillary Attribute Code: Give any Capillary Transaction attribute code. Ex:
                                    cashier_name
                                </li>
                                <li>SAVE</li>
                                <p><img src="{{ URL::to('assets/images/64.png') }}" style="width: 50%;height: 80%"></p>
                            </ul>
                        </li>
                        <li>Attribute Code: Give any transaction attribute code. Ex:
                            customer,default_address,country_name
                        </li>
                        <li>Capillary Attribute Code: Give any Capillary Transaction attribute code. Ex: bill_status
                        </li>
                        <p><img src="{{ URL::to('assets/images/65.png') }}" style="width: 50%;height: 80%"></p>
                    </ul>
                    <br><br>
                    <p>Once attributes are mapped, place an order from Shopify front end.</p>
                    <p>The transaction data will reflect in the Capillary Member Care section - View the transaction of
                        the respective user.</p>
                    <!-- <p>User Name : marry</p> -->
                <!-- <p><img src="{{ URL::to('assets/images/66.png') }}" style="width: 50%;height: 80%"></p> -->
                    <p><img src="{{ URL::to('assets/images/67.png') }}" style="width: 50%;height: 80%"></p>
                    <br><br>
                    <p>Below are the configuration details to find out the capillary custom/extended fields</p>
                    <ul>
                        <li>Click on Profile icon>> Organization settings
                        </li>
                        <li>Master Data Management>> Data Model</li>
                        <li>Custom Fields >> View custom fields for loyalty transactions OR</li>
                        <br>
                        <li>Extended Fields</li>
                    </ul>
                </div>
                <br><br><br>
                <h6>1.10 Tracker API</h6>
                <div style="padding-left: 30px">
                    <p>When a user is part of a group, tracker data will be displayed and if the user is not part of the
                        group “Tracker Data” will be displayed as “False” in the API response.</p>
                    <ul>
                        <li>Follow the below configuration for Progress Tracker Data
                            <ul>
                                <li>User should be navigated to Apps >> Capillary CRM Integration >>Config Capillary
                                    settings >>
                                    Customer
                                </li>
                                <li>Click on Capillary Tech→ CRM Integration</li>
                                <li>Scroll down to Progress Tracker Data</li>
                                <li>Fill the following fields
                                    <ul>
                                        <li>Minimum value of the progress Bar</li>
                                        <li>Maximum value of the progress Bar</li>
                                        <li>Total Number of Tier</li>
                                        <li>Tiers of Data >> Enter the data in the json or array format, as this will be
                                            returned in API response
                                            ({1,0,100,FreeTier},{2,101,300,Bronze},{3,301,700,Silver},{4,701,100,Gold})
                                        </li>
                                    </ul>
                                </li>
                                <li>Save the config</li>
                            </ul>
                        </li>
                    </ul>
                    <!-- <p>
                        Find the below API Response
                        <br>Url:
                        https://cap-shopify-int-dev.spurtreetech.com/customer/tracker/{customer-email}/stt-test.myshopify.com
                        <br>Marry is part of group
                    </p> -->
                    <p><img src="{{ URL::to('assets/images/72.png') }}" style="width: 50%;height: 80%"></p>
                <!-- <p>Akash is not part of group</p>
                    <p><img src="{{ URL::to('assets/images/73.png') }}" style="width: 50%;height: 80%"></p> -->
                </div>
                <br><br><br>
                <h6>1.11 OTP Validation during Registration</h6>
                <div style="padding-left: 30px">
                    <p><b>1.11.1 Enable OTP Validation</b></p>
                    <p>When the admin enables this configuration the OTP field should be displayed on the registration
                        form
                        ( for Mobile number and Email) based on the attributes given in the admin</p>
                    <ul>
                        <li>Follow the below configuration
                            <ul>
                                <li>User should be navigated to Apps >> Capillary CRM Integration >>Config Capillary
                                    settings >>
                                    Customer
                                </li>
                                <li>Click on Capillary Tech→ CRM Integration</li>
                                <li>Scroll down to OTP verification</li>
                                <li>Mobile OTP Enabled: YES (dropdown)</li>
                                <li>Country Code Enabled(if you are using country code selection in registration page): YES (dropdown)</li>
                                <li>Email OTP Enabled: YES (dropdown)</li>
                                <li>Mobile OTP Attribute: your Phone input field id</li>
                                <li>Email OTP Attribute: your Email input field id</li>
                                <li>Country Code Attribute: your country code input/selection field id</li>
                                <li>SAVE CONFIG</li>
                                <li>The user should be able to view mobile number and OTP field with Generate and
                                    Validate
                                    buttons
                                </li>
                                <li>The user should be able to view email and OTP field with Generate and Validate
                                    buttons
                                </li>
                                <li>Enter mobile number  on the mobile field</li>
                                <li>Enter email on the email field</li>
                                <li>Click on generate OTP then OTP to be checked on capillary dashboard -> workbench ->
                                    Communication logs -> Search Messages By mobile.
                                </li>
                                <li>On entering valid OTP click on Validate button user should be able to register</li>
                                <li>On entering invalid OTP and click on Validate button error message to be displayed
                                </li>

                            </ul>
                        </li>

                    </ul>
                <!-- <p><img src="{{ URL::to('assets/images/74.png') }}" style="width: 50%;height: 80%"></p><br><br> -->
                    <p><img src="{{ URL::to('assets/images/75.png') }}" style="width: 50%;height: 80%"></p><br><br>
                    <p>Mobile Field</p>
                    <p><img src="{{ URL::to('assets/images/76.png') }}" style="width: 50%;height: 80%"></p><br><br>
                    <p>Mobile field Invalid OTP</p>
                    <p><img src="{{ URL::to('assets/images/77.png') }}" style="width: 50%;height: 80%"></p><br><br>
                    <p>Mobile field Valid OTP</p>
                    <p><img src="{{ URL::to('assets/images/78.png') }}" style="width: 50%;height: 80%"></p><br><br>
                    <p><img src="{{ URL::to('assets/images/79.png') }}" style="width: 50%;height: 80%"></p><br><br>
                    <p>Email Field</p>
                    <p>Email field Invalid OTP</p>
                    <p><img src="{{ URL::to('assets/images/81.png') }}" style="width: 50%;height: 80%"></p><br><br>
                    <p>Email field Valid OTP</p>
                    <p><img src="{{ URL::to('assets/images/82.png') }}" style="width: 50%;height: 80%"></p><br><br>

                    <p><b>1.11.2 Disable OTP Validation</b></p>
                    <p>When the admin disables this configuration the OTP field should not be displayed on the
                        registration
                        form ( for Mobile number and Email) based on the attributes given in the admin</p>
                    <ul>
                        <li>Follow the below configuration
                            <ul>
                                <li>User should be navigated to stores >> configuration >> Capillary tech
                                </li>
                                <li>Click on Capillary Tech→ CRM Integration</li>
                                <li>Scroll down to OTP verification</li>
                                <li>Mobile OTP Enabled: NO (dropdown)</li>
                                <li>Email OTP Enabled: NO (dropdown)</li>
                                <li>Mobile OTP Attribute: your Phone input field id</li>
                                <li>Email OTP Attribute: your email input field id</li>
                                <li>SAVE CONFIG</li>
                                <li>User should not be displayed with OTP field, and they can register without mobile
                                    number
                                    and OTP
                                </li>
                            </ul>
                        </li>
                        <p><img src="{{ URL::to('assets/images/83.png') }}" style="width: 50%;height: 80%"></p><br><br>
                        <p><img src="{{ URL::to('assets/images/84.png') }}" style="width: 50%;height: 80%"></p><br><br>
                    </ul>
                </div>
                <br><br><br>
                <h6>1.12 Group Coupon Redemption</h6>
                <div style="padding-left: 30px">
                    <p><b>1.12.1 Enable group Coupon redemption</b></p>
                    <p>When the admin enables this configuration the Group User should be able to use the coupon of
                        the
                        Primary user and coupon history should be displayed based on the group on the frontend.(i.e
                        Primary
                        user and Group User should be able to see the same data on the coupon history page)</p>
                    <ul>
                        <li>Follow the below configuration
                            <ul>
                                <li>User should be navigated to stores >> Apps >> Capillary CRM Integration
                                </li>
                                <li>Click on Config Capillary Settings</li>
                                <li>Scroll down to Coupon</li>
                                <li>Group Redemption enabled : YES (dropdown)</li>
                                <li>SAVE CONFIG</li>
                                <li>On the frontend Login with your credentials</li>
                                <li>Check the coupon history of the primary user</li>
                                <li>Now Login with Group User</li>
                                <li>Check the coupon history</li>
                                <li>On the checkout page the coupon of the primary user should be applied successfully
                                    by the Group User.
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <p>Primary User (For Example:- Marry)</p>
                    <p><img src="{{ URL::to('assets/images/85.png') }}" style="width: 50%;height: 80%"></p><br><br>

                    <p>Group User (For Example:- Abi)</p>
                    <p><img src="{{ URL::to('assets/images/86.png') }}" style="width: 50%;height: 80%"></p><br><br>


                    <p><img src="{{ URL::to('assets/images/87.png') }}" style="width: 50%;height: 80%"></p><br><br><br>

                    <p>Capillary Dashboard Screenshot</p>
                    <p><img src="{{ URL::to('assets/images/88.png') }}" style="width: 50%;height: 80%"></p><br><br>

                    <br>
                    <br>
                    <p><b>1.12.2 Disable group Coupon redemption</b></p>
                    <p>When the admin disables this configuration, the Group User should not be able to use the
                        coupon of the Primary user and coupon history should not be displayed based on the group on the
                        frontend. (i.e Primary user and Group User should not be able to see the same data on the
                        coupon history page).</p>
                    <ul>
                        <li>Follow the below configuration
                            <ul>
                                <li>User should be navigated to stores >> Apps >> Capillary CRM Integration
                                </li>
                                <li>Click on Config Capillary Settings</li>
                                <li>Scroll down to Coupon</li>
                                <li>Group Redemption enabled : No (dropdown)</li>
                                <li>SAVE CONFIG</li>
                                <li>On the frontend Login with your login credentials</li>
                                <li>Check the coupon history of the primary user</li>
                                <li>Now Login with Group User login credentials</li>
                                <li>Check the coupon history</li>
                                <li>On the checkout page the coupon of the primary user should be applied successfully
                                    by the Group User.
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <p><img src="{{ URL::to('assets/images/92.png') }}" style="width: 50%;height: 80%"></p><br><br>
                    <br>
                    <p>Primary user</p>
                    <p><img src="{{ URL::to('assets/images/93.png') }}" style="width: 50%;height: 80%"></p><br><br>
                    <br>
                    <p>Group User</p>
                    <p><img src="{{ URL::to('assets/images/94.png') }}" style="width: 50%;height: 80%"></p><br><br>
                    <p><img src="{{ URL::to('assets/images/95.png') }}" style="width: 50%;height: 80%"></p><br><br>
                </div>
                <br><br><br>
                <h6>1.13 Redeem the coupons at SKU Level</h6>
                <div style="padding-left: 30px">
                    <p>When the user places an order for the SKU, a coupon will be issued to the user and the user will
                        be able to redeem the coupon for the respective SKU.</p>
                    <ul>
                        <li>Follow the below steps
                            <ul>
                                <li>Login as user
                                </li>
                                <li>Place order for test2</li>
                                <li>Check the coupon in the coupon history</li>
                                <li>Add test2 product to the cart</li>
                                <li>Apply the coupon and place order</li>
                                <li>User should be able to apply coupon successfully</li>
                                <li>If a user adds other SKU product to cart and try to apply coupon error should be
                                    displayed.
                                </li>


                            </ul>
                        </li>
                    </ul>
                    <p><img src="{{ URL::to('assets/images/96.png') }}" style="width: 50%;height: 80%"></p><br><br>
                    <p><img src="{{ URL::to('assets/images/97.png') }}" style="width: 50%;height: 80%"></p><br><br>
                    <p><img src="{{ URL::to('assets/images/98.png') }}" style="width: 50%;height: 80%"></p><br><br>
                    <p><img src="{{ URL::to('assets/images/99.png') }}" style="width: 50%;height: 80%"></p><br><br>
                </div>
                <br><br><br>
                <h6>1.14 Pilot Program</h6>
                <div style="padding-left: 30px">
                    <p><b>1.14.1 Enable Pilot program</b></p>
                    <p>When the admin enables this configuration, if the pilot program attribute value is set to
                        “yes” then that user will be able to view all the Coupon history, Transaction history, Point
                        history, slab information, and the user should be able to redeem coupons and points
                        successfully.</p>
                    <ul>
                        <li>Follow the below configuration
                            <ul>
                                <li>User should be navigated to stores >> Capillary CRM Integration
                                </li>
                                <li>Click on Configure capillary</li>
                                <li>Scroll down to Pilot program</li>
                                <li>Enabled : YES (dropdown)</li>
                                <li>Custom Field in Capillary: pilot</li>
                                <li>Custom Field Value in Capillary: yes</li>
                                <li>SAVE CONFIG</li>
                                <li>User with pilot : yes field will be able to view all the details and apply coupon
                                    successfully
                                </li>
                                <li>User with pilot : no field will not be able to view all the details

                                </li>

                            </ul>
                        </li>
                    </ul>

                    <p><img src="{{ URL::to('assets/images/100.png') }}" style="width: 50%;height: 80%"></p><br><br>
                    <p>
                        Pilot value: ”yes”
                        <br>User: Marry
                    </p>
                    <p><img src="{{ URL::to('assets/images/103.png') }}" style="width: 50%;height: 80%"></p><br><br>
                    <p>Coupon Redemption</p>
                    <p><img src="{{ URL::to('assets/images/104.png') }}" style="width: 50%;height: 80%"></p><br><br>
                    <p>
                        Pilot value: ”No”
                        <br>User: Abi
                    </p>
                    <p><img src="{{ URL::to('assets/images/105.png') }}" style="width: 50%;height: 80%"></p><br><br>
                    <br>
                    <p><b>1.14.2 Disable Pilot program</b></p>
                    <p>When the admin disables this configuration, both the users will not be able to view all the Coupon
                        history,Transaction history, Point history, slab information, and the user should not be able to
                        redeem coupons and points.</p>
                    <ul>
                        <li>Follow the below configuration
                            <ul>
                                <li>o User should be navigated to stores >> configuration >> Capillary tech
                                </li>
                                <li>Click on Stores→ Capillary CRM Integration</li>
                                <li>Click on Configure capillary</li>
                                <li>Scroll down to Pilot program</li>
                                <li>Enabled : No (dropdown)</li>
                                <li>Custom Field in Capillary: pilot</li>
                                <li>Custom Field Value in Capillary: no</li>
                                <li>SAVE CONFIG</li>
                                <li>User with pilot : yes field will be able to view all the details and apply coupon
                                    successfully
                                </li>
                                <li>User with pilot : no field will not be able to view all the details

                                </li>

                            </ul>
                        </li>
                    </ul>
                    <p><img src="{{ URL::to('assets/images/106.png') }}" style="width: 50%;height: 80%"></p><br><br>
                    <p><img src="{{ URL::to('assets/images/107.png') }}" style="width: 50%;height: 80%"></p><br><br>
                    <p><img src="{{ URL::to('assets/images/108.png') }}" style="width: 50%;height: 80%"></p><br><br>
                </div>
                <br><br><br>
                <h6>1.15 Points Redemption</h6>
                <div style="padding-left: 30px">
                    <p><b>1.15.1 Enable Points redemption</b></p>
                    <p>When the admin enables this configuration the Points field should be displayed on the cart</p>
                    <ul>
                        <li>Follow the below configuration
                            <ul>
                                <li>User should be navigated to stores >> Capillary CRM Integration
                                </li>
                                <li>Click on Configure capillary</li>
                                <li>Scroll down to Point</li>
                                <li>Enabled : YES (dropdown)</li>
                                <li>Set Minimum redeemable points</li>
                                <li>set Maximum redeemable</li>
                                <li>Set Multiples of Redeemable Points can be claimed</li>
                                <li>SAVE CONFIG</li>
                            </ul>
                        </li>
                    </ul>
                    <p><img src="{{ URL::to('assets/images/109.png') }}" style="width: 50%;height: 80%"></p><br><br>
                    <p>Place an order in the front end, add a product to cart and navigate to cart</p>
                    <p><img src="{{ URL::to('assets/images/110.png') }}" style="width: 50%;height: 80%"></p><br><br>
                    <br>
                    <p><b>1.15.2 Disable Points redemption</b></p>
                    <ul>
                        <li>Follow the below configuration
                            <ul>
                                <li>User should be navigated to stores >> Capillary CRM Integration
                                </li>
                                <li>Click on Configure capillary</li>
                                <li>Scroll down to Point</li>
                                <li>Enabled : No (dropdown)</li>
                                <li>SAVE CONFIG</li>
                            </ul>
                        </li>
                    </ul>
                    <p><img src="{{ URL::to('assets/images/111.png') }}" style="width: 50%;height: 80%"></p><br><br>
                    <p>Go to Front End, add an order to the cart and navigate to the cart</p>
                    <p><img src="{{ URL::to('assets/images/112.png') }}" style="width: 50%;height: 80%"></p><br><br>
                </div>

                <h6>1.16 Endpoints</h6>
                <div style="padding-left: 30px">
                    <p><b>1.16.1 Reward Catalog APIs</b></p>
                    <p>Below are the APIs for the reward catalog </p>
                    <ul>
                        <li>Get Brand Rewards
                            <ul>
                                <li>Url: {base_url}/reward/brand/{host_name}?{query_params}
                                </li>
                                <li>Method: GET</li>
                                <li>Returns brand rewards</li>
                            </ul>
                        </li>
                        <li>Get Vouchers Rewards
                            <ul>
                                <li>Url: {base_url}/vouchers/brand/{host_name}?{query_params}
                                </li>
                                <li>Method: GET</li>
                                <li>Returns vouchers brand</li>
                            </ul>
                        </li>
                    </ul>

                    <p><b>1.16.2 Gamification APIs</b></p>
                    <p>Below are the APIs for the Gamification </p>
                    <ul>
                        <li>Get All Games
                            <ul>
                                <li>Url: {base_url}/gamification/getAll/{customer_email}/{host_name}
                                </li>
                                <li>Method: GET</li>
                                <li>Returns All games available for that customer</li>
                            </ul>
                        </li>
                        <li>Get Game by Id
                            <ul>
                                <li>Url: {base_url}/gamification/getById/{customer_email}/{game_id}/{host_name}
                                </li>
                                <li>Method: GET</li>
                                <li>Returns Game information based on Id</li>
                            </ul>
                        </li>
                    </ul>

                    <p><b>1.16.3 CCMS APIs</b></p>
                    <p>Below are the APIs for the CCMS </p>
                    <ul>
                        <li>Get All tickets raised by customer
                            <ul>
                                <li>Url: {base_url}/customer/getAllTickets/{customer_email}/{host_name}
                                </li>
                                <li>Method: GET</li>
                                <li>Returns All tickets raised by that customer</li>
                            </ul>
                        </li>
                        <li>Create ticket
                            <ul>
                                <li>Url: {base_url}/customer/createTicket
                                </li>
                                <li>Method: POST</li>
                                <li>Body:
                                    <pre>
    {
        email: {customer_email},
        domain: {host_name},
        code: {ticket_code},
        status: {ticket_status},
        subject: {ticket_subject},
        priority: {ticket_priority},
        department: {ticket_department},
        message: {ticket_message},
        assigned_to: {org_name},
        custom_fields: {
            0: {
                name: {name_of_field},
                value: {value_of_field}
            }
        }
    }</pre>
                                </li>
                                <li>Returns Created customer ticket information</li>
                            </ul>
                        </li>
                    </ul>

                    <p><b>1.16.4 Check Referral API</b></p>
                    <p>Below are the API for the Check Referral code</p>
                    <ul>
                        <li>Url: {base_url}/customer/checkReferralCode/{referral_code}/{host_name}
                        </li>
                        <li>Method: GET</li>
                        <li>Returns true if code is correct otherwise gives false</li>
                    </ul>
                </div>

                <h6>1.17 Update Primary Identifier (Email/Phone)</h6>
                <div style="padding-left: 30px">
                    <p>Follow the below instruction to update the email or phone or both in shopify as well as capillary end</p>

                    <ul>
                        <li>User should be navigated to Apps >> Capillary CRM Integration
                        </li>
                        <li>Click on Update Customer Details</li>
                        <li>Enter shopify customer Email or Phone in search box and click on "Get Detail" button</li>
                        <li>You will get the customer details in customer form</li>
                        <li>Now You can enter the new email or Phone no. which you want to update in both platform Shopify store as well as Capillary</li>
                        <li>After that click on Update detail button</li>
                    </ul>
                </div>

            </div>

        </div>
        <div style="width: 100%;text-align:center"><h4>End of document</h4></div>
    </div>
@endsection

@section('scripts')
    @parent

    <script>
        $('#configure-theme').on('click', function (e) {
            $('#loading-image').css('display', 'flex');
            e.preventDefault();

            $.ajax({
                url: "{{ route('theme.configure') }}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    status: '1'
                },
                success: function (response) {
                    $('.alert-success').css({'right': '5%', 'opacity': '1'});
                    setTimeout(function () {
                        $('.alert-success').css({'right': '-100%', 'opacity': '0'});
                    }, 2000);
                },
                error: function () {
                    $('.alert-danger').css({'right': '5%', 'opacity': '1'});
                    setTimeout(function () {
                        $('.alert-danger').css({'right': '-100%', 'opacity': '0'});
                    }, 2000);
                },
                complete: function () {
                    $('#loading-image').hide();
                }
            });
        });

        $('#showPointSlab').on('click', function (e) {
            $('#loading-image').css('display', 'flex');
            e.preventDefault();

            $.ajax({
                url: "{{ route('theme.showPointSlab') }}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    status: '1'
                },
                success: function (response) {
                    $('.alert-success').css({'right': '5%', 'opacity': '1'});
                    setTimeout(function () {
                        $('.alert-success').css({'right': '-100%', 'opacity': '0'});
                    }, 2000);
                },
                error: function () {
                    $('.alert-danger').css({'right': '5%', 'opacity': '1'});
                    setTimeout(function () {
                        $('.alert-danger').css({'right': '-100%', 'opacity': '0'});
                    }, 2000);
                },
                complete: function () {
                    $('#loading-image').hide();
                }
            });
        });
    </script>
@endsection

@extends('layouts.footer')
