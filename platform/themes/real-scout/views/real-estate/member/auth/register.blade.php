{!! Theme::partial('auth-shell-open', [
    'heading' => 'Join',
    'headingAccent' => 'GEM Real Estate',
    'description' => 'Create your free account to save favorite properties, contact agents, and track your real estate search.',
    'cardTitle' => 'Create Your Account',
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

<div class="modal fade terms-modal" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Terms &amp; Conditions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="term_condition_body"></div>
            <div class="modal-footer">
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="modal_terms" id="modal_terms" value="1" required />
                    </label>
                    <label>&nbsp; I accept</label>
                    <span style="cursor: pointer" class="red">GEM Terms &amp; Conditions</span>
                </div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@include(Theme::getThemeNamespace() . '::views.real-estate.account.auth.includes.messages')

<form method="POST" action="{{ route('member.register.save') }}">
    @csrf
    <div class="form-group">
        <input id="first_name" type="text" class="form-control{{ $errors->has('full_name') ? ' is-invalid' : '' }}"
            name="full_name" value="{{ old('full_name') }}" required autofocus
            placeholder="{{ trans('plugins/real-estate::dashboard.full_name') }}">
        @if ($errors->has('full_name'))
            <span class="invalid-feedback">{{ $errors->first('full_name') }}</span>
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
        <input id="mobile_no" type="number" value="{{ old('mobile_no') }}"
            class="form-control{{ $errors->has('mobile_no') ? ' is-invalid' : '' }}"
            name="mobile_no" required
            placeholder="{{ trans('plugins/real-estate::dashboard.mobile_no') }}">
        @if ($errors->has('mobile_no'))
            <span class="invalid-feedback">{{ $errors->first('mobile_no') }}</span>
        @endif
    </div>

    <div class="form-group">
        <div class="gem-auth__field">
            <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                name="password" required value="{{ old('password') }}"
                placeholder="{{ trans('plugins/real-estate::dashboard.password') }}">
            <button type="button" class="gem-auth__field-toggle" data-toggle-password="password" aria-label="{{ __('Show password') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
        </div>
        @if ($errors->has('password'))
            <span class="invalid-feedback">{{ $errors->first('password') }}</span>
        @endif
    </div>

    <label class="gem-auth__terms">
        <input type="checkbox" name="terms" id="terms" value="1" required>
        <span>
            I accept
            <span data-toggle="modal" data-target="#exampleModal">GEM Terms &amp; Conditions</span>
        </span>
    </label>

    <button type="submit" class="gem-auth__submit">
        {{ trans('plugins/real-estate::dashboard.register-cta') }}
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
    </button>

    <p class="gem-auth__footer-link">
        {{ __('Have an account already?') }}
        <a class="gem-auth__link" href="{{ route('member.login') }}">{{ __('Login') }}</a>
    </p>
</form>

{!! Theme::partial('auth-shell-close') !!}
