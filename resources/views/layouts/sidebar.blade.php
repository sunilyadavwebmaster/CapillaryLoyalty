<div class="d-flex flex-column flex-shrink-0 sidebar" style="width: 280px;">
  <ul class="nav nav-pills flex-column mb-auto">
    <li class="nav-item t">

      <a href="{{ route('home') }}?shop={{Auth::user()->name}}" class="@if(Route::currentRouteName() == 'home') {{ 'opened' }} @else {{ '' }} @endif">
        Home
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('shop.capilleryAuthenticate', Auth::user()->id)}}?shop={{Auth::user()->name}}" class="@if(Route::currentRouteName() == 'shop.capilleryAuthenticate') {{ 'opened' }} @else {{ '' }} @endif">
       General Authenticate
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('settings.view')}}?shop={{Auth::user()->name}}" class="@if(Route::currentRouteName() == 'settings.view') {{ 'opened' }} @else {{ '' }} @endif">
        Config Capillary Settings
      </a>
    </li>
    <li class="nav-item ">
      <a href="#"  class="accordion @if(Route::currentRouteName() == 'capillary.customer_attribute' || Route::currentRouteName() == 'capillary.attributeForm' || Route::currentRouteName() == 'capillary.attributeEdit' || Route::currentRouteName() == 'capillary.transaction_attribute' || Route::currentRouteName() == 'capillary.payments') {{ 'opened' }} @else {{ '' }} @endif">
        Capillary Tech
      </a>
      <ul class="panel
          @if(Route::currentRouteName() == 'capillary.customer_attribute' || Route::currentRouteName() == 'capillary.attributeForm' || Route::currentRouteName() == 'capillary.attributeEdit' || Route::currentRouteName() == 'capillary.transaction_attribute' || Route::currentRouteName() == 'capillary.payments')
              {{ 'active' }}
          @else
              {{ '' }}
          @endif">
        <li>
          <a href="{{ route('capillary.customer_attribute') }}?shop={{Auth::user()->name}}" class="@if(Route::currentRouteName() == 'capillary.customer_attribute') {{ 'active' }} @else {{ '' }} @endif">Assign Attribute</a>
        </li>
        <li>
          <a href="{{ route('capillary.transaction_attribute') }}?shop={{Auth::user()->name}}" class="@if(Route::currentRouteName() == 'capillary.transaction_attribute') {{ 'active' }} @else {{ '' }} @endif">Assign Transaction Attribute</a>
        </li>
        <li>
          <a href="{{ route('capillary.payments') }}?shop={{Auth::user()->name}}" class="@if(Route::currentRouteName() == 'capillary.payments') {{ 'active' }} @else {{ '' }} @endif">Assign Payment Methods</a>
        </li>
      </ul>
    </li>
      <li class="nav-item">

          <a href="{{ route('search-customer')}}?shop={{Auth::user()->name}}" class="@if(Route::currentRouteName() == 'search-customer') {{ 'opened' }} @else {{ '' }} @endif">
              Update Customer Details
          </a>
      </li>
      <li class="nav-item">

          <a href="{{ route('update-domain',Auth::user()->id)}}?shop={{Auth::user()->name}}" class="@if(Route::currentRouteName() == 'update-domain') {{ 'opened' }} @else {{ '' }} @endif">
              Update Alternative Domain
          </a>
      </li>

  </ul>
</div>

<script>

$(".accordion").each(function (index) {
  $(this).click(function(e){
    $(this).next().slideToggle('slow', function(){
      if ($(this).prev().is('.opened')) {
        $(this).prev().removeClass('opened');
      } else {
        $(this).prev().addClass('opened');
      }
    });
  });
})
</script>