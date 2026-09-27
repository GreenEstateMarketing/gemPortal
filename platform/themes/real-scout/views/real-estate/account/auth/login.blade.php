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

<form method="POST" action="{{ route('public.account.login') }}">
    @csrf
    <div class="form-group">
        <div class="gem-auth__field">
            <svg class="gem-auth__field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z" opacity="0"/><path d="M22 6l-10 7L2 6"/><path d="M2 6h20v12H2z"/></svg>
            <input id="email" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                placeholder="{{ trans('plugins/real-estate::dashboard.email_or_username') }}"
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
        <a class="gem-auth__link" href="{{ route('public.account.password.request', ['type' => 'agent']) }}">
            {{ trans('plugins/real-estate::dashboard.forgot-password-cta') }}
        </a>
    </div>

    <button type="submit" class="gem-auth__submit">
        {{ trans('plugins/real-estate::dashboard.login-cta') }}
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
    </button>

    <p class="gem-auth__footer-link">
        {{ __("Don't have an account?") }}
        <a class="gem-auth__link" href="{{ route('public.account.register') }}">{{ __('Create account') }}</a>
    </p>
</form>

{!! Theme::partial('auth-shell-close') !!}
