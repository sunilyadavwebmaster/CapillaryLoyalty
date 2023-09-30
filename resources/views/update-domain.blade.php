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
            <strong>Success!</strong> Record has been updated!
        </div>
        <div class="alert alert-danger">
            <strong>Faild!</strong> Something went wrong!
        </div>
        <form action="{{ route('alternative-domain') }}" method="post" id="domainForm">
            @csrf
            <div class="form-group">
                <label for="alternate_name">Alternative Domain</label>
                <input type="text" class="form-control" id="alternate_name" name="domain_name"  value="@if(isset($data)){{ $data->alternate_name }}@endif">

                <span class="invalid-feedback" role="alert">
                </span>

            </div>
            <input type="submit" id="btnSubmit" class="btn btn-primary mb-2" name="btnsubmit" value="Submit">
        </form>
    </div>
</div>
@endsection
@section('scripts')
@parent

<script>
        $('#domainForm').on('submit',function(e){
            $('#loading-image').css('display','flex');
            e.preventDefault();
            console.log('clicked');
            let alternate_name = $('#alternate_name').val();


            $.ajax({
                url: "{{ route('alternative-domain') }}",
                type:"POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    alternate_name:alternate_name
                },

                success:function(response){
                 if(response.success){
                    $('.alert-success').css({'right':'5%','opacity':'1'});
                    setTimeout(function() {
                        $('.alert-success').css({'right':'-100%','opacity':'0'});
                    }, 2000);
                    $('.invalid-feedback').html('');
}

                       if(response.error){
          $('.alert-danger').css({'right':'5%','opacity':'1'});
        setTimeout(function() {
            $('.alert-danger').css({'right':'-100%','opacity':'0'});
        }, 2000);
        $('.invalid-feedback').css({'display':'block'});
         $('.invalid-feedback').html(response.error);
    }
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
        actions.TitleBar.create(app, { title: 'Alternative Domain' });
    </script>
@endsection

@extends('layouts.footer')