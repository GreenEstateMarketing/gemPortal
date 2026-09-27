{!! Theme::partial('auth-shell-open', [
    'heading' => 'Reset Your',
    'headingAccent' => 'Password',
    'description' => 'Choose a new password for your GEMlisting account to get back to managing your listings and saved properties.',
    'cardTitle' => 'Choose a New Password',
    'cardSubtitle' => 'Enter and confirm your new password below.',
]) !!}

<form method="POST" action="{{ route('public.account.password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    {!! Form::hidden('email', $email) !!}

    <div class="form-group">
        <div class="gem-auth__field">
            <svg class="gem-auth__field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
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
        <input id="password-confirm" type="password"
            class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}"
            name="password_confirmation" required
            placeholder="{{ trans('plugins/real-estate::dashboard.password-confirmation') }}">
        @if ($errors->has('password_confirmation'))
            <span class="invalid-feedback">{{ $errors->first('password_confirmation') }}</span>
        @endif
    </div>

    <button type="submit" class="gem-auth__submit">
        {{ trans('plugins/real-estate::dashboard.reset-password-cta') }}
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
    </button>
</form>

@include(Theme::getThemeNamespace() . '::views.real-estate.account.auth.includes.messages')

{!! Theme::partial('auth-shell-close') !!}
