<link rel="stylesheet" href="{{ Theme::asset()->url('css/wizard/property-wizard.css') }}">

<section class="wanted-form-section" style="padding: 48px 0;">
    <div class="container" style="max-width: 760px;">
        <div class="wizard-panel">
            <h1 class="wizard-panel__heading">{{ __('Apply to Become an Agent') }}</h1>
            <p class="wizard-panel__description">
                {{ __('Tell us about yourself and attach your resume. Our team reviews every application and will reach out if there\'s a fit.') }}
            </p>

            @if (session()->has('success_msg'))
                <div class="alert alert-success"><span>{{ session('success_msg') }}</span></div>
            @endif

            @if (session()->has('error_msg'))
                <div class="alert alert-danger"><span>{{ session('error_msg') }}</span></div>
            @endif

            <form method="POST" action="{{ route('public.agent-application.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="wizard-field-grid">
                    <div class="wizard-field">
                        <label class="required" for="agent-application-name">{{ __('Full Name') }}</label>
                        <input type="text" id="agent-application-name" name="name" class="wizard-input"
                               value="{{ old('name') }}" placeholder="{{ __('Enter your full name') }}" maxlength="120">
                        @if ($errors->has('name'))
                            <div class="wizard-error">{{ $errors->first('name') }}</div>
                        @endif
                    </div>
                    <div class="wizard-field">
                        <label class="required" for="agent-application-email">{{ __('Email Address') }}</label>
                        <input type="text" id="agent-application-email" name="email" class="wizard-input"
                               value="{{ old('email') }}" placeholder="name@example.com">
                        @if ($errors->has('email'))
                            <div class="wizard-error">{{ $errors->first('email') }}</div>
                        @endif
                    </div>
                    <div class="wizard-field">
                        <label class="required" for="agent-application-phone">{{ __('Mobile Number') }}</label>
                        <input type="text" id="agent-application-phone" name="phone" class="wizard-input"
                               value="{{ old('phone') }}" placeholder="+92300123456">
                        <p class="wizard-hint">{{ __('+ followed by exactly 11 digits, no spaces.') }}</p>
                        @if ($errors->has('phone'))
                            <div class="wizard-error">{{ $errors->first('phone') }}</div>
                        @endif
                    </div>
                    <div class="wizard-field">
                        <label class="required" for="agent-application-experience">{{ __('Years of Experience') }}</label>
                        <input type="number" id="agent-application-experience" name="years_of_experience" class="wizard-input"
                               value="{{ old('years_of_experience') }}" placeholder="{{ __('e.g. 5') }}">
                        @if ($errors->has('years_of_experience'))
                            <div class="wizard-error">{{ $errors->first('years_of_experience') }}</div>
                        @endif
                    </div>
                    <div class="wizard-field wizard-field--span2">
                        <label class="required" for="agent-application-education">{{ __('Highest Education') }}</label>
                        <select id="agent-application-education" name="education_level" class="wizard-select">
                            <option value="">{{ __('Select your highest education level') }}</option>
                            @foreach (['High School', 'Associate Degree', "Bachelor's Degree", "Master's Degree", 'Doctorate (PhD)', 'Other'] as $level)
                                <option value="{{ $level }}" {{ old('education_level') === $level ? 'selected' : '' }}>{{ __($level) }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('education_level'))
                            <div class="wizard-error">{{ $errors->first('education_level') }}</div>
                        @endif
                    </div>

                    <div class="wizard-field wizard-field--span2">
                        <label class="required">{{ __('Expertise in Areas') }}</label>
                        <p class="wizard-hint">{{ __('Select every property category you specialize in.') }}</p>
                        <div class="wizard-checkbox-grid">
                            @foreach ($categories as $category)
                                <label class="wizard-checkbox">
                                    <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                           {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}>
                                    {{ $category->name }}
                                </label>
                            @endforeach
                        </div>
                        @if ($errors->has('category_ids'))
                            <div class="wizard-error">{{ $errors->first('category_ids') }}</div>
                        @endif
                    </div>

                    <div class="wizard-field wizard-field--span2">
                        <label class="required" for="agent-application-resume">{{ __('Resume') }}</label>
                        <input type="file" id="agent-application-resume" name="resume" class="wizard-input"
                               accept=".pdf,.doc,.docx">
                        <p class="wizard-hint">{{ __('PDF or Word document, maximum 5MB.') }}</p>
                        @if ($errors->has('resume'))
                            <div class="wizard-error">{{ $errors->first('resume') }}</div>
                        @endif
                    </div>
                </div>

                <div class="wanted-form-footer" style="margin-top: 24px;">
                    <button type="submit" class="wizard-btn wizard-btn--primary">
                        {{ __('Submit Application') }} <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
