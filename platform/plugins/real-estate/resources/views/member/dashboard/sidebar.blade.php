<div class="col-12 col-md-3 col-xl-2 sidebar-layout">
  <div class="list-group sidebar-layout  pl-2" style="background: #3d3d3d">
    <!--<div class="list-group-item fw6 bn @if (Route::currentRouteName() == 'public.member.dashboard') active @endif light-gray-text">
        <i class="fas fa-home mr-2"></i>
        {{ trans('plugins/real-estate::dashboard.dashboard_sidebar_title') }}
    </div>-->
    <a class="list-group-item pl-2 list-group-item-action bn @if (Route::currentRouteName() == 'member.dashboard') active @endif"
      href="{{ route('member.dashboard') }}"
      title="{{ trans('plugins/real-estate::dashboard.dashboard_sidebar_title') }}">
      <i class="fas fa-home mr1"></i>{{ trans('plugins/real-estate::dashboard.dashboard_sidebar_title') }}
    </a>
    <a class="list-group-item pl-2 list-group-item-action bn @if (Route::currentRouteName() == 'public.member.properties.index') active @endif"
      href="{{ route('public.member.properties.index') }}"
      title="{{ trans('plugins/real-estate::account-property.properties') }}">
      <i class="far fa-newspaper mr1"></i>{{ trans('plugins/real-estate::account-property.properties') }}
    </a>

    @if (auth('member')->user()->canPost())
      <a class="list-group-item pl-2 list-group-item-action bn @if (Route::currentRouteName() == 'public.member.properties.create') active @endif"
        href="{{ route('public.member.properties.create') }}"
        title="{{ trans('plugins/real-estate::account-property.write_property') }}">
        <i class="far fa-edit mr1"></i>{{ trans('plugins/real-estate::account-property.write_property') }}
      </a>
    @endif
    <!-- hiding packages for agent -->
    <a class="list-group-item pl-2 list-group-item-action bn @if (Route::currentRouteName() == 'public.member.packages') active @endif"
      href="{{ route('public.member.packages') }}" title="{{ trans('plugins/real-estate::account.credits') }}">
      <i class="far fa-credit-card mr1"></i>{{ trans('plugins/real-estate::account.buy_credits') }} <span
        class="badge badge-info">{{ auth('member')->user()->credits }}
        {{ trans('plugins/real-estate::account.credits') }}</span>
    </a>
    <!-- setting main menu-->

    <!--sub-menu settings-->
    <a href="{{route('member.settings')}}"
      class="list-group-item pl-2 list-group-item-action bn @if (Route::currentRouteName() == 'public.member.settings') active @endif">
      <i class="fas fa-user-circle mr-2"></i>
      <span>{{ trans('plugins/real-estate::dashboard.sidebar_information') }}</span>
    </a>
    <a href="{{route('public.member.security')}}"
      class="list-group-item pl-2 list-group-item-action bn @if (Route::currentRouteName() == 'public.member.security') active @endif">
      <i class="fas fa-user-lock mr-2"></i>
      <span>{{ trans('plugins/real-estate::dashboard.sidebar_security') }}</span>
    </a>
    <!--end -->
  </div>
</div>