<nav class="navbar navbar-expand-md navbar-light bg-black bb b--black-10">
    <div class="container-fluid">
        @if (theme_option('logo'))
          <a href="{{ url('/') }}"><img src="{{ Theme::asset()->url('images/gemlogo.png')  }}" alt="{{ theme_option('site_title') }}" height="35"></a>
        @else
          <div class="brand-container tc mr2 br2">
            <a class="navbar-brand b white ma0 pa0 dib w-100" href="{{ url('/') }}" title="{{ theme_option('site_title') }}">{{ ucfirst(mb_substr(theme_option('site_title'), 0, 1, 'utf-8')) }}</a>
          </div>
        @endif

    <button class="navbar-toggler" type="button" data-toggle="collapse"
            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <!-- Right Side Of Navbar -->
      <ul class="navbar-nav ml-auto my-2">
          <li>
              <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db" style="text-decoration: none; line-height: 32px;" href="/projects">
                  <span class="text-white">{{ trans('Projects') }}</span>
              </a>
          </li>
          <li>
              <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db" style="text-decoration: none; line-height: 32px;" href="/properties">
                  <span class="text-white">{{ trans('Properties') }}</span>
              </a>
          </li>
          <li>
              <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db" style="text-decoration: none; line-height: 32px;" href="/agents">
                  <span class="text-white">{{ trans('Agents') }}</span>
              </a>
          </li>
          <li>
              <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db" style="text-decoration: none; line-height: 32px;" href="/Add-Property">
                  <span class="text-white">{{ trans('Add Property') }}</span>
              </a>
          </li>
          <li>
              <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db" style="text-decoration: none; line-height: 32px;" href="/wanted">
                  <span class="text-white">{{ trans('Wanted') }}</span>
              </a>
          </li>
        <!-- Authentication Links -->
        @if (!auth('account')->check())
          <li>
            <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db" style="text-decoration: none; line-height: 32px;" href="{{ route('public.account.login') }}">
                <i class="fas fa-sign-in-alt"></i> {{ trans('plugins/real-estate::dashboard.login-cta') }}
            </a>
          </li>
          <li>
            <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db" style="text-decoration: none; line-height: 32px;" href="{{ route('public.account.register') }}">
                <i class="fas fa-user-plus"></i> {{ trans('plugins/real-estate::dashboard.register-cta') }}
            </a>
          </li>
        @else
          <li>
            <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db mr2" style="text-decoration: none; line-height: 32px;" href="{{ route('public.account.dashboard') }}" title="{{ trans('plugins/real-estate::dashboard.header_profile_link') }}">
              <span>
                <img src="{{ auth('account')->user()->image_path ? '/storage/' . auth('account')->user()->image_path : auth('account')->user()->avatar_url }}" class="br-100 v-mid mr1" style="width: 30px;">
                <span>{{ auth('account')->user()->getFullName() }}</span>
              </span>
            </a>
          </li>
         <!-- <li>
              <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db mr2" style="text-decoration: none; line-height: 32px;" href="{{ route('public.account.settings') }}" title="{{ trans('plugins/real-estate::dashboard.header_settings_link') }}">
                  <i class="fas fa-cogs mr1"></i>{{ trans('plugins/real-estate::dashboard.header_settings_link') }}
              </a>
          </li>
          <li>
              <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db mr2" style="text-decoration: none; line-height: 32px;" href="{{ route('public.account.packages') }}" title="{{ trans('plugins/real-estate::account.credits') }}">
                  <i class="far fa-credit-card mr1"></i>{{ trans('plugins/real-estate::account.buy_credits') }} <span class="badge badge-info">{{ auth('account')->user()->credits }} {{ trans('plugins/real-estate::account.credits') }}</span>
              </a>
          </li>-->
        <!--  {!! apply_filters(ACCOUNT_TOP_MENU_FILTER, null) !!}
              <li>
                  <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db mr2" style="text-decoration: none; line-height: 32px;" href="{{ route('public.account.properties.index') }}" title="{{ trans('plugins/real-estate::account-property.properties') }}">
                      <i class="far fa-newspaper mr1"></i>{{ trans('plugins/real-estate::account-property.properties') }}
                  </a>
              </li>
          @if (auth('account')->user()->canPost())
              <li>
                  <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db mr2" style="text-decoration: none; line-height: 32px;" href="{{ route('public.account.properties.create') }}" title="{{ trans('plugins/real-estate::account-property.write_property') }}">
                      <i class="far fa-edit mr1"></i>{{ trans('plugins/real-estate::account-property.write_property') }}
                  </a>
              </li>
          @endif-->
          <li>
            <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db" style="text-decoration: none; line-height: 32px;" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="{{ trans('plugins/real-estate::dashboard.header_logout_link') }}">
              <i class="fas fa-sign-out-alt mr1"></i><span class="dn-ns">{{ trans('plugins/real-estate::dashboard.header_logout_link') }}</span>
            </a>
            <form id="logout-form" action="{{ route('public.account.logout') }}" method="POST" style="display: none;">
              @csrf
            </form>
          </li>
        @endguest
      </ul>
    </div>
  </div>
</nav>
<script src="{{ asset('/themes/real-scout/js/jquery.min.js')}}"></script>
