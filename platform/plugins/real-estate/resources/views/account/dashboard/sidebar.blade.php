<div class="col-12 col-md-3 col-xl-2 sidebar-layout">
  <div class="list-group sidebar-layout  pl-2" style="background: #3d3d3d">
    <!--<div class="list-group-item fw6 bn @if (Route::currentRouteName() == 'public.account.dashboard') active @endif light-gray-text">
        <i class="fas fa-home mr-2"></i>
        {{ trans('plugins/real-estate::dashboard.dashboard_sidebar_title') }}
    </div>-->
      <a class="list-group-item pl-2 list-group-item-action bn @if (Route::currentRouteName() == 'public.account.dashboard') active @endif"  href="{{ route('public.account.dashboard') }}" title="{{ trans('plugins/real-estate::dashboard.dashboard_sidebar_title') }}" >
          <i class="fas fa-home mr1"></i>{{ trans('plugins/real-estate::dashboard.dashboard_sidebar_title') }}
      </a>
      <a class="list-group-item pl-2 list-group-item-action bn @if (Route::currentRouteName() == 'public.account.properties.index') active @endif"  href="{{ route('public.account.properties.index') }}" title="{{ trans('plugins/real-estate::account-property.properties') }}" >
          <i class="far fa-newspaper mr1"></i>{{ trans('plugins/real-estate::account-property.properties') }}
      </a>
      @if (auth('account')->user()->canPost())

              <a class="list-group-item pl-2 list-group-item-action bn @if (Route::currentRouteName() == 'public.account.properties.create') active @endif"  href="{{ route('public.account.properties.create') }}" title="{{ trans('plugins/real-estate::account-property.write_property') }}">
                  <i class="far fa-edit mr1"></i>{{ trans('plugins/real-estate::account-property.write_property') }}
              </a>

       @endif
        <!-- hiding packages for agent -->
     <a class="list-group-item pl-2 list-group-item-action bn @if (Route::currentRouteName() == 'public.account.packages') active @endif"  href="{{ route('public.account.packages') }}" title="{{ trans('plugins/real-estate::account.credits') }}">
          <i class="far fa-credit-card mr1"></i>{{ trans('plugins/real-estate::account.buy_credits') }} <span class="badge badge-info">{{ auth('account')->user()->credits }} {{ trans('plugins/real-estate::account.credits') }}</span>
      </a>
        <a class="list-group-item pl-2 list-group-item-action bn @if (Route::currentRouteName() == 'public.account.consult.index') active @endif"  href="{{ route('public.account.consult.index') }}" title="{{ trans('plugins/real-estate::account.consult') }}">
            <i class="fas fa-headset mr1"></i>{{ trans('plugins/real-estate::account.consult') }} <span class="badge badge-info" id="consult_count">{{ auth('account')->user()->getConsults()}}</span>
        </a>
    <!-- setting main menu-->

    <!--sub-menu settings-->
    <a href="{{ route('public.account.settings') }}" class="list-group-item pl-2 list-group-item-action bn @if (Route::currentRouteName() == 'public.account.settings') active @endif">
      <i class="fas fa-user-circle mr-2"></i>
      <span>{{ trans('plugins/real-estate::dashboard.sidebar_information') }}</span>
    </a>
    <a href="{{ route('public.account.security') }}" class="list-group-item pl-2 list-group-item-action bn @if (Route::currentRouteName() == 'public.account.security') active @endif">
      <i class="fas fa-user-lock mr-2"></i>
      <span>{{ trans('plugins/real-estate::dashboard.sidebar_security') }}</span>
    </a>
      <!--end -->
  </div>
</div>
