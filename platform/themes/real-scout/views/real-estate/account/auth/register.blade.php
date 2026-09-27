{!! Theme::partial('auth-shell-open', [
    'heading' => 'Join',
    'headingAccent' => 'GEM Real Estate',
    'description' => 'Create your agent account to list properties, reach verified buyers, and grow your business with GEM.',
    'cardTitle' => 'Create Your Agent Account',
    'cardSubtitle' => 'Fill in your details to get started.',
    'features' => [
        [
            'title' => 'Verified Properties',
            'subtitle' => '100% genuine listings',
            'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l9-7 9 7"/><path d="M5 10v9a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1v-9"/></svg>',
        ],
        [
            'title' => 'Trusted Agents',
            'subtitle' => 'Work with verified professionals',
            'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11z"/></svg>',
        ],
        [
            'title' => 'Better Decisions',
            'subtitle' => 'Find your dream property, faster',
            'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 1 0-7.8 7.8l1 1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.6z"/></svg>',
        ],
    ],
]) !!}

<form method="POST" action="{{ route('public.account.register') }}">
    @csrf
    <div class="gem-auth__two-col">
        <div class="form-group">
            <input id="first_name" type="text" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}"
                name="first_name" value="{{ old('first_name') }}" required autofocus
                placeholder="{{ trans('plugins/real-estate::dashboard.first_name') }}">
            @if ($errors->has('first_name'))
                <span class="invalid-feedback">{{ $errors->first('first_name') }}</span>
            @endif
        </div>
        <div class="form-group">
            <input id="last_name" type="text" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}"
                name="last_name" value="{{ old('last_name') }}" required
                placeholder="{{ trans('plugins/real-estate::dashboard.last_name') }}">
            @if ($errors->has('last_name'))
                <span class="invalid-feedback">{{ $errors->first('last_name') }}</span>
            @endif
        </div>
    </div>

    <div class="form-group">
        <input id="username" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}"
            name="username" value="{{ old('username') }}" required
            placeholder="{{ trans('plugins/real-estate::dashboard.username') }}">
        @if ($errors->has('username'))
            <span class="invalid-feedback">{{ $errors->first('username') }}</span>
        @endif
    </div>

    <div class="form-group">
        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
            name="email" value="{{ old('email') }}" required
            placeholder="{{ trans('plugins/real-estate::dashboard.email') }}">
        @if ($errors->has('email'))
            <span class="invalid-feedback">{{ $errors->first('email') }}</span>
        @endif
    </div>

    <div class="form-group">
        <div class="gem-auth__field">
            <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                name="password" required
                placeholder="{{ trans('plugins/real-estate::dashboard.password') }}">
            <button type="button" class="gem-auth__field-toggle" data-toggle-password="password" aria-label="{{ __('Show password') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
        </div>
        @if ($errors->has('password'))
            <span class="invalid-feedback">{{ $errors->first('password') }}</span>
        @endif
    </div>

    <div class="form-group">
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required
            placeholder="{{ trans('plugins/real-estate::dashboard.password-confirmation') }}">
    </div>

    <button type="submit" class="gem-auth__submit">
        {{ trans('plugins/real-estate::dashboard.register-cta') }}
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
    </button>

    <p class="gem-auth__footer-link">
        {{ __('Have an account already?') }}
        <a class="gem-auth__link" href="{{ route('public.account.login') }}">{{ __('Login') }}</a>
    </p>

    {!! apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, \Botble\RealEstate\Models\Account::class) !!}
</form>

@include(Theme::getThemeNamespace() . '::views.real-estate.account.auth.includes.messages')

{!! Theme::partial('auth-shell-close') !!}
