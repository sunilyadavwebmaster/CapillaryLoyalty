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
        <div><h3>{{ (isset($data)) ? 'Update Payment Method' : 'Assign Payment Method' }}</h3></div>
        <form action="#" id="paymentForm" method="post">
            @csrf
            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" name="status" id="status">
                    <option value="1">Enable</option>
                    <option value="0" {{ (isset($data->status) && $data->status == 0)  ? 'selected' : ''}}>Disable</option>
                </select>
            </div>
            <div class="form-group">
                <label for="shopify_payment_method">Shopify Payment Method</label>
                <input type="text" name="shopify_payment_method" id="shopify_payment_method" class="form-control" value="{{ $data->shopify_payment_method ?? ''}}">
                @error('shopify_payment_method')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('shopify_payment_method') }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="cap_payment_method">Capillary Payment Method</label>
                <input type="text" name="cap_payment_method" id="cap_payment_method" class="form-control"  value="{{ $data->cap_payment_method ?? ''}}">
                @error('cap_payment_method')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('cap_payment_method') }}</strong>
                    </span>
                @enderror
            </div>
            @if(isset($data))<a href="{{ route('capillary.deletePayment', $data->id) }}" class="btn btn-primary mb-2" name="submit">Delete</a>@endif
            <button type="submit" class="btn btn-primary mb-2" name="submit">Save</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    @parent

    <script>
        $('#paymentForm').on('submit',function(e){
            $('#loading-image').css('display','flex');
            e.preventDefault();
            let status = $('#status').val();
            let shopify_payment_method = $('#shopify_payment_method').val();
            let cap_payment_method = $('#cap_payment_method').val();

            $.ajax({
                url: "{{ route('capillary.paymentSave') }}",
                type:"POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    id:"{{(isset($data->id)) ? $data->id : ''}}",
                    status:status,
                    shopify_payment_method:shopify_payment_method,
                    cap_payment_method:cap_payment_method
                },
                success:function(response){
                    $('.alert-success').css({'right':'5%','opacity':'1'});
                    setTimeout(function() {
                        $('.alert-success').css({'right':'-100%','opacity':'0'});
                    }, 2000);
                    location.href = "{{ route('capillary.payments') }}";
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

        actions.TitleBar.create(app, { title: 'Capillary Payment Methods' });
    </script>
@endsection

@extends('layouts.footer')