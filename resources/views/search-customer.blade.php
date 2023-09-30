@extends('shopify-app::layouts.default')
@extends('layouts.app')
@section('content')
    <div class="content">
        <div id="loading-image">
            <img src="{{ URL::to('assets/images/loader.gif') }}"/>
        </div>
        @include('layouts.sidebar')
        <div class="settings">
            <div class="alert alert-success">
                <strong>Success!</strong> Record has beed updated!
            </div>
            <div class="alert alert-danger">
                <strong>Faild!</strong> Something went wrong!
            </div>
            <h6>Enter Customer Phone or EMail to Get the Customer Details</h6>
            <form action="{{ route('update-customer') }}" method="post" id="customerForm">
                <input type="text" style="width: 50%;" id="search-input" name="search-input">
                <button type="submit" class="btn btn-primary mb-2" name="submit">Get Details</button>
            </form>
            <div class="row">
                <div class="col-md-12" style="display: none" id="info-div">
                    <form action="{{ route('update-customer') }}" method="post" id="customerUpdateForm">
                    <input type="hidden" required style="width: 100%" id="customer-id" name="customer-id">
                    <div class="col-md-6">
                        <label>First Name</label>
                        <input type="text" readonly required style="width: 100%" id="customer-firstname" name="customer-firstname">
                    </div>
                    <div class="col-md-6">
                        <label>Last Name</label>
                        <input type="text" required readonly style="width: 100%" id="customer-lastname" name="customer-lastname">
                    </div>
                    <div class="col-md-6">
                        <label>Email</label>
                        <input type="text"  required style="width: 100%" id="customer-email" name="customer-email">
                        <span class="invalid-feedback" id="email-error"></span>
                    </div>
                    <div class="col-md-6">
                        <label>Phone</label>
                        <input type="text" style="width: 100%" id="customer-phone" name="customer-phone">
                        <span class="invalid-feedback" id="phone-error"></span>
                    </div>
                        <div class="col-md-6"><button type="submit" class="btn btn-primary mb-2" name="submit">Update Details</button></div>
                    </form>
                </div>
                <div class="col-md-6" style="display: none" id="error-div"><h6>Sorry No record found</h6></div>
            </div>

        </div>

    </div>
@endsection
@section('scripts')
    @parent

    <script>
        $('#customerForm').on('submit', function (e) {
            $('#loading-image').css('display', 'flex');
            e.preventDefault();


            $.ajax({
                url: "{{ route('get-customer') }}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    searchinput: $("#search-input").val(),


                },
                success: function (response) {
                    var responseArray = JSON.parse(response);
                    if (responseArray.body.customers.length > 0) {
                        $('#info-div').css('display', 'block');
                        $('#error-div').css('display', 'none');
                        console.log(responseArray.body.customers[0].id);
                        $("#customer-id").val(responseArray.body.customers[0].id);
                        $("#customer-firstname").val(responseArray.body.customers[0].first_name);
                        $("#customer-lastname").val(responseArray.body.customers[0].last_name);
                        $("#customer-email").val(responseArray.body.customers[0].email);
                        $("#customer-phone").val(responseArray.body.customers[0].phone);
                    } else {
                        $('#info-div').css('display', 'none');
                        $('#error-div').css('display', 'block');
                    }

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
        $('#customerUpdateForm').on('submit', function (e) {
            $('#loading-image').css('display', 'flex');
            e.preventDefault();


            $.ajax({
                url: "{{ route('update-customer') }}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    customer_id: $("#customer-id").val(),
                    customer_firstname: $("#customer-firstname").val(),
                    customer_lastname: $("#customer-lastname").val(),
                    customer_email: $("#customer-email").val(),
                    customer_phone: $("#customer-phone").val(),



                },
                success: function (response) {
                    console.log(response);
                    responseArray=JSON.parse(response);
                    if(responseArray.code==200)
                    {
                        $('#email-error').css('display', 'none');
                        $('#phone-error').css('display', 'none');
                        $('.alert-success').css({'right':'5%','opacity':'1'});
                        setTimeout(function() {
                            $('.alert-success').css({'right':'-100%','opacity':'0'});
                        }, 2000);
                    }else if(responseArray.code==101)
                    {
                        $("#email-error").html("<strong>"+responseArray.msg+"</strong>");
                        $('#email-error').css('display', 'block');
                    }
                    else if(responseArray.code==102)
                    {
                        $("#phone-error").html("<strong>"+responseArray.msg+"</strong>");
                        $('#phone-error').css('display', 'block');
                    }

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
        actions.TitleBar.create(app, {title: 'Customers'});
    </script>
@endsection

@extends('layouts.footer')
