{!! Theme::partial('auth-shell-open', [
    'heading' => 'Forgot Your',
    'headingAccent' => 'Password?',
    'description' => "No worries. Enter the email address linked to your account and we'll send you a link to reset it.",
    'cardTitle' => 'Reset Your Password',
    'cardSubtitle' => "Enter your email address and we'll send you a reset link.",
]) !!}

<form method="POST" action="{{ route('public.account.password.email') }}">
    @csrf
    <input type="hidden" name="type" value="{{ $type }}">
    <div class="form-group">
        <div class="gem-auth__field">
            <svg class="gem-auth__field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 6l-10 7L2 6"/><path d="M2 6h20v12H2z"/></svg>
            <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                value="{{ old('email') }}" required
                placeholder="{{ trans('plugins/real-estate::dashboard.email') }}">
        </div>
        @if ($errors->has('email'))
            <span class="invalid-feedback">{{ $errors->first('email') }}</span>
        @endif
    </div>

    <button type="submit" class="gem-auth__submit">
        {{ trans('plugins/real-estate::dashboard.reset-password-cta') }}
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
    </button>

    <p class="gem-auth__footer-link">
        <a class="gem-auth__link" href="{{ $type == 'agent' ? route('public.account.login') : route('member.login') }}">
            {{ trans('plugins/real-estate::dashboard.back-to-login') }}
        </a>
    </p>
</form>

@include(Theme::getThemeNamespace() . '::views.real-estate.account.auth.includes.messages')

{!! Theme::partial('auth-shell-close') !!}
