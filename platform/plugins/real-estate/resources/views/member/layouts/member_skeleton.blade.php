<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

    @if (theme_option('favicon'))
        <link rel="shortcut icon" href="{{ RvMedia::getImageUrl(theme_option('favicon')) }}">
    @endif

  {!! SeoHelper::render() !!}

  <!-- Datetime picker -->

  {!! Assets::renderHeader(['core']) !!}

  {!! Html::style('vendor/core/core/base/css/themes/default.css') !!}

  <!-- Styles -->
  <link href="{{ asset('vendor/core/plugins/real-estate/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('custom/css/agent_style.css') }}" rel="stylesheet">

    <script src="{{ asset('/themes/real-scout/js/jquery.min.js')}}"></script>

  <!-- Put translation key to translate in VueJS -->
  <script type="text/javascript">
      window.trans = JSON.parse('{!! addslashes(json_encode(trans('plugins/real-estate::dashboard'))) !!}');
      var BotbleVariables = BotbleVariables || {};
      BotbleVariables.languages = {
        tables: {!! json_encode(trans('core/base::tables'), JSON_HEX_APOS) !!},
        notices_msg: {!! json_encode(trans('core/base::notices'), JSON_HEX_APOS) !!},
        pagination: {!! json_encode(trans('pagination'), JSON_HEX_APOS) !!},
        system: {
          'character_remain': '{{ trans('core/base::forms.character_remain') }}'
        }
      };
  </script>
</head>
<body>
  @include('core/base::layouts.partials.svg-icon')
  <div id="app">
    @include('plugins/real-estate::member.components.header')
    <main class="main_form">
   @yield('content')
    </main>
      @if (count(Assets::getAdminLocales()) > 1)
          <footer>
              <p> {{ trans('core/acl::auth.languages') }}:
                  @foreach (Assets::getAdminLocales() as $key => $value)
                      <span @if (app()->getLocale() == $key) class="active" @endif>
                <a href="{{ route('settings.language', $key) }}">
                    {!! language_flag($value['flag'], $value['name']) !!} <span>{{ $value['name'] }}</span>
                </a>
            </span>
                  @endforeach
              </p>
          </footer>
      @endif
  </div>

  @if (session()->has('status') || session()->has('success_msg') || session()->has('error_msg') || (isset($errors) && $errors->count() > 0) || isset($error_msg))
    <script type="text/javascript">
      window.noticeMessages = [];
      @if (session()->has('success_msg'))
        noticeMessages.push({'type': 'success', 'message': "{!! addslashes(session('success_msg')) !!}"});
      @endif
      @if (session()->has('status'))
      noticeMessages.push({'type': 'success', 'message': "{!! addslashes(session('status')) !!}"});
      @endif
      @if (session()->has('error_msg'))
        noticeMessages.push({'type': 'error', 'message': "{!! addslashes(session('error_msg')) !!}"});
      @endif
      @if (isset($error_msg))
        noticeMessages.push({'type': 'error', 'message': "{!! addslashes($error_msg) !!}"});
      @endif
      @if (isset($errors))
        @foreach ($errors->all() as $error)
          noticeMessages.push({'type': 'error', 'message': "{!! addslashes($error) !!}"});
        @endforeach
      @endif
    </script>
  @endif

  <!-- Scripts -->
  <script src="{{ asset('vendor/core/plugins/real-estate/js/app.js') }}"></script>
  <!-- <script src="{{ asset('/js/app.js') }}"></script> -->


  {!! Assets::renderFooter() !!}
  @stack('scripts')
  @stack('footer')
  <script src="{{ asset('/js/jquery.validate.min.js') }}"></script>
  <script src="{{ asset('/js/additional-methods.min.js') }}"></script>
  <script src="{{ asset('/js/checkout.js') }}"></script>
</body>
</html>
