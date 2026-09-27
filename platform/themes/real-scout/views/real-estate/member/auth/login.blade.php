{!! Theme::partial('auth-shell-open', [
    'heading' => 'Welcome Back to',
    'headingAccent' => 'GEM Real Estate',
    'description' => 'Discover the perfect property, connect with verified agents, and make your real estate journey easier and smarter.',
    'cardTitle' => 'Login to Your Account',
    'cardSubtitle' => 'Welcome back! Please sign in to continue to your dashboard.',
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

@include(Theme::getThemeNamespace() . '::views.real-estate.account.auth.includes.messages')

<form method="POST" action="{{ route('login-save') }}">
    @csrf
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="form-group">
        <div class="gem-auth__field">
            <svg class="gem-auth__field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 6l-10 7L2 6"/><path d="M2 6h20v12H2z"/></svg>
            <input id="email" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                placeholder="{{ trans('plugins/real-estate::dashboard.email') }}"
                name="email" value="{{ old('email') }}" autofocus>
        </div>
        @if ($errors->has('email'))
            <span class="invalid-feedback">{{ $errors->first('email') }}</span>
        @endif
    </div>

    <div class="form-group">
        <div class="gem-auth__field">
            <svg class="gem-auth__field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
            <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                value="{{ old('password') }}"
                placeholder="{{ trans('plugins/real-estate::dashboard.password') }}" name="password">
            <button type="button" class="gem-auth__field-toggle" data-toggle-password="password" aria-label="{{ __('Show password') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
        </div>
        @if ($errors->has('password'))
            <span class="invalid-feedback">{{ $errors->first('password') }}</span>
        @endif
    </div>

    <div class="gem-auth__row">
        <label class="gem-auth__remember">
            <input type="checkbox" name="remember" {{ old('remember', true) ? 'checked' : '' }}>
            {{ trans('plugins/real-estate::dashboard.remember-me') }}
        </label>
        <a class="gem-auth__link" href="{{ route('public.account.password.request') }}">
            {{ trans('plugins/real-estate::dashboard.forgot-password-cta') }}
        </a>
    </div>

    <button type="submit" class="gem-auth__submit">
        {{ trans('plugins/real-estate::dashboard.login-cta') }}
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
    </button>

    <div class="gem-auth__divider">{{ __('Or continue with') }}</div>

    <div class="gem-auth__social">
        @if (setting('social_login_google_enable'))
            <a href="{{ route('auth.social', ['provider' => 'google', 'type' => 'member']) }}" class="gem-auth__social-btn gem-auth__social-btn--google">
                <svg viewBox="0 0 24 24"><path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.5h6.5c-.3 1.5-1.1 2.7-2.4 3.6v3h3.9c2.3-2.1 3.5-5.2 3.5-8.8z"/><path fill="#34A853" d="M12 24c3.2 0 5.9-1.1 7.9-2.9l-3.9-3c-1.1.7-2.4 1.1-4 1.1-3.1 0-5.7-2.1-6.6-4.9H1.4v3.1C3.4 21.3 7.4 24 12 24z"/><path fill="#FBBC05" d="M5.4 14.3c-.2-.7-.4-1.5-.4-2.3s.1-1.6.4-2.3V6.6H1.4C.5 8.3 0 10.1 0 12s.5 3.7 1.4 5.4l4-3.1z"/><path fill="#EA4335" d="M12 4.8c1.7 0 3.3.6 4.5 1.8l3.4-3.4C17.9 1.2 15.2 0 12 0 7.4 0 3.4 2.7 1.4 6.6l4 3.1c.9-2.8 3.5-4.9 6.6-4.9z"/></svg>
                {{ __('Continue with Google') }}
            </a>
        @else
            <a href="#" class="gem-auth__social-btn gem-auth__social-btn--google is-disabled" onclick="return false;" aria-disabled="true">
                <svg viewBox="0 0 24 24"><path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.5h6.5c-.3 1.5-1.1 2.7-2.4 3.6v3h3.9c2.3-2.1 3.5-5.2 3.5-8.8z"/><path fill="#34A853" d="M12 24c3.2 0 5.9-1.1 7.9-2.9l-3.9-3c-1.1.7-2.4 1.1-4 1.1-3.1 0-5.7-2.1-6.6-4.9H1.4v3.1C3.4 21.3 7.4 24 12 24z"/><path fill="#FBBC05" d="M5.4 14.3c-.2-.7-.4-1.5-.4-2.3s.1-1.6.4-2.3V6.6H1.4C.5 8.3 0 10.1 0 12s.5 3.7 1.4 5.4l4-3.1z"/><path fill="#EA4335" d="M12 4.8c1.7 0 3.3.6 4.5 1.8l3.4-3.4C17.9 1.2 15.2 0 12 0 7.4 0 3.4 2.7 1.4 6.6l4 3.1c.9-2.8 3.5-4.9 6.6-4.9z"/></svg>
                {{ __('Continue with Google') }}
            </a>
        @endif
        @if (setting('social_login_facebook_enable'))
            <a href="{{ route('auth.social', ['provider' => 'facebook', 'type' => 'member']) }}" class="gem-auth__social-btn gem-auth__social-btn--facebook">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.4v7A10 10 0 0 0 22 12z"/></svg>
                {{ __('Continue with Facebook') }}
            </a>
        @else
            <a href="#" class="gem-auth__social-btn gem-auth__social-btn--facebook is-disabled" onclick="return false;" aria-disabled="true">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.4v7A10 10 0 0 0 22 12z"/></svg>
                {{ __('Continue with Facebook') }}
            </a>
        @endif
    </div>

    <p class="gem-auth__footer-link">
        {{ __("Don't have an account?") }}
        <a class="gem-auth__link" href="{{ route('member.register') }}">{{ __('Create account') }}</a>
    </p>

    {!! apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, \Botble\RealEstate\Models\Member::class) !!}
</form>

{!! Theme::partial('auth-shell-close') !!}
