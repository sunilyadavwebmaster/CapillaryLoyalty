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
        <form action="{{ route('shop.authenticateSave') }}" method="post" id="authForm">
            @csrf
            <div class="form-group">
                <label for="app_key">Client ID</label>
                <input type="text" class="form-control" id="app_key" name="app_key"  value="@if(isset($data)){{ $data->app_key }}@endif">
                @error('app_key')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('app_key') }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="client_secret_key">Client Secret Key</label>
                <input type="text" class="form-control" id="client_secret_key" name="client_secret_key" value="@if(isset($data)){{ $data->client_secret_key }}@endif">
                @error('client_secret_key')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('client_secret_key') }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="base_url">Base Url of the API</label>
                <input type="text" class="form-control" id="base_url" name="base_url" value="@if(isset($data)){{ $data->base_url }}@endif">
                @error('base_url')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('base_url') }}</strong>
                </span>
                @enderror
            </div>
            <input type="submit" id="btnSubmit" class="btn btn-primary mb-2" name="btnsubmit" value="Submit">
        </form>
    </div>
</div>
@endsection
@section('scripts')
    @parent

    <script>
        $('#authForm').on('submit',function(e){
            $('#loading-image').css('display','flex');
            e.preventDefault();
            console.log('clicked');
            let app_key = $('#app_key').val();
            let client_secret_key = $('#client_secret_key').val();
            let base_url = $('#base_url').val();

            $.ajax({
                url: "{{ route('shop.authenticateSave') }}",
                type:"POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    app_key:app_key,
                    client_secret_key:client_secret_key,
                    base_url:base_url,
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
        actions.TitleBar.create(app, { title: 'Authenticate' });
    </script>
@endsection

@extends('layouts.footer')