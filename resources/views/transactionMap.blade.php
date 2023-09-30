@extends('shopify-app::layouts.default')
@extends('layouts.app')
@section('content')
<div class="content">
  @include('layouts.sidebar')
  <div class="mapContent p-3">
    <div class="action"><a href="{{ route('capillary.attributeForm',['transaction','0']) }}" class="btn btn-primary">Assign Attributes</a></div>
    <table class="table">
      <thead>
        <tr>
          <th scole="col">Status</th>
          <th scope="col">Attribute Type</th>
          <th scope="col">Attribute Code</th>
          <th scope="col">Capillary Attribute Code</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>
        @if(count($data) > 0)
          @foreach ($data as $row)
          <tr>
            <td>@if($row->status == 1) {{ 'Enable' }} @else {{ 'Disable' }} @endif</td>
            <td>{{ $row->field_type }}</td>
            <td>{{ $row->shopify_field }}</td>
            <td>{{ $row->capillary_field }}</td>
            <td>
              <a href="{{ route('capillary.attributeForm', ['customer',$row->id] )}}">Edit</a>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="5">No record found..</td></tr>
        @endif
      </tbody>
    </table>
</div>
</div>
@endsection

@section('scripts')
    @parent
  @endsection

@extends('layouts.footer')