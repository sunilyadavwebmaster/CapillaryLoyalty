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
        <div><h3>{{ (isset($data)) ? 'Update Attribute' : 'Assign Attribute' }}</h3></div>
        <form action="{{ route('capillary.attributeEdit') }}" id="attributeForm" method="post">
            @csrf
            <input type="hidden" name="data_type" id="data_type" value="{{ (isset($data_type)) ? $data_type : '' }}">
            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" name="status" id="status">
                    <option value="1">Enable</option>
                    <option value="0" {{ (isset($data->status) && $data->status == 0)  ? 'selected' : ''}}>Disable</option>
                </select>
            </div>
            <div class="form-group">
                <label for="field_type">Attribute type</label>
                <select class="form-control" name="field_type" id="field_type">
                    <option value="custom">Custom</option>
                    <option value="extended" {{ (isset($data->field_type) && $data->field_type == 'extended')  ? 'selected' : ''}}>Extended</option>
                    <option value="core" {{ (isset($data->field_type) && $data->field_type == 'core')  ? 'selected' : ''}}>Core</option>
                </select>
            </div>
            <div class="form-group">
                <label for="shopify_field">Attribute Code</label>
                <input type="text" name="shopify_field" id="shopify_field" class="form-control" value="{{ $data->shopify_field ?? ''}}">
                @error('shopify_field')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('shopify_field') }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="capillary_field">Capillary Attribute Code</label>
                <input type="text" name="capillary_field" id="capillary_field" class="form-control"  value="{{ $data->capillary_field ?? ''}}">
                @error('capillary_field')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('capillary_field') }}</strong>
                    </span>
                @enderror
            </div>
            @if(isset($data))<a href="{{ route('capillary.attributeDelete', $data->id)}}" class="btn btn-primary mb-2" name="submit">Delete</a>@endif
            <button type="submit" class="btn btn-primary mb-2" name="submit">Save</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    @parent

    <script>
        $('#attributeForm').on('submit',function(e){
            $('#loading-image').css('display','flex');
            e.preventDefault();
            let data_type = $('#data_type').val();
            let status = $('#status').val();
            let field_type = $('#field_type').val();
            let shopify_field = $('#shopify_field').val();
            let capillary_field = $('#capillary_field').val();

            $.ajax({
                url: "{{ route('capillary.attributeEdit') }}",
                type:"POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    id:"{{(isset($data->id)) ? $data->id : ''}}",
                    data_type:data_type,
                    status:status,
                    field_type:field_type,
                    shopify_field:shopify_field,
                    capillary_field:capillary_field
                },
                success:function(response){
                    $('.alert-success').css({'right':'5%','opacity':'1'});
                    setTimeout(function() {
                        $('.alert-success').css({'right':'-100%','opacity':'0'});
                    }, 2000);
                    if(data_type == 'customer'){
                        location.href = "{{ route('capillary.customer_attribute') }}";
                    }else{
                        location.href = "{{ route('capillary.transaction_attribute') }}";
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

        actions.TitleBar.create(app, { title: 'Customers' });
    </script>
@endsection

@extends('layouts.footer')
