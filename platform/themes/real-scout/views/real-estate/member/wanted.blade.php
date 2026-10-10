@php
    // Flat {id, parent_id, name} list for the category -> property-type chip
    // cascade in wanted-page.js (same data-category-tree idiom used by the
    // Add Property wizard's basics step / property-wizard.js).
    $categoryTree = [];
    foreach ($categories as $cat) {
        $categoryTree[] = ['id' => $cat->id, 'parent_id' => 0, 'name' => $cat->name];
        foreach ($cat->subcategories as $subcat) {
            $categoryTree[] = ['id' => $subcat->id, 'parent_id' => $cat->id, 'name' => $subcat->name];
        }
    }

    $budgetOptions = [
        5000000 => 'Under 50 Lacs',
        10000000 => '50 Lacs - 1 Crore',
        20000000 => '1 - 2 Crore',
        50000000 => '2 - 5 Crore',
        100000000 => 'Above 5 Crore',
    ];
@endphp

<link rel="stylesheet" href="{{ Theme::asset()->url('css/wizard/property-wizard.css') }}">
<link rel="stylesheet" href="{{ Theme::asset()->url('css/wanted/wanted-page.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<div class="wanted-page">
    <section class="wanted-hero">
        <div class="wanted-hero__inner">
            <div class="wanted-hero__content">
                <span class="wanted-hero__eyebrow">{{ __('GEMlisting Wanted') }}</span>
                <h1 class="wanted-hero__heading">
                    {{ __('Your Property Goals.') }}<br>
                    {{ __('Our') }} <span class="wanted-hero__heading-accent">{{ __('Expertise.') }}</span>
                </h1>
                <p class="wanted-hero__text">
                    {{ __('Every great property journey starts with the right request. Tell us what you have in mind, and let us help you find the right opportunity.') }}
                </p>
                <div class="wanted-hero__highlights">
                    <span class="wanted-hero__highlight"><i class="fas fa-home"></i>{{ __('Buy & Rent') }}</span>
                    <span class="wanted-hero__highlight"><i class="fas fa-chart-line"></i>{{ __('Invest') }}</span>
                    <span class="wanted-hero__highlight"><i class="fas fa-drafting-compass"></i>{{ __('Build') }}</span>
                </div>
            </div>
            <div class="wanted-hero__image-wrap">
                <img class="wanted-hero__image" src="{{ Theme::asset()->url('images/wanted-right-image.jpg') }}" alt="{{ __('Wanted') }}">
                <div class="wanted-hero__caption">
                    <p class="wanted-hero__caption-title">{{ __('Your next chapter starts here.') }}</p>
                    <p class="wanted-hero__caption-text">{{ __('Find the space that fits your vision.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="wanted-form-section">
        <div class="wizard-panel">
            <div class="wanted-panel-head">
                <div>
                    <span class="wanted-panel-head__eyebrow">{{ __('01 / Your Requirements') }}</span>
                    <h2 class="wanted-panel-head__title">{{ __('What are you looking for?') }}</h2>
                    <p class="wanted-panel-head__desc">{{ __('Choose your goal and share a few details with us.') }}</p>
                </div>
                <span class="wanted-protected-note"><i class="fas fa-shield-alt"></i>{{ __('Your details stay protected') }}</span>
            </div>
            <hr class="wanted-divider">

            <form id="wanted-form" action="{{ route('public.send.wanted') }}" method="post"
                  data-category-tree="{{ json_encode($categoryTree) }}">
                @csrf
                <input type="hidden" id="wanted-type" name="type" value="buy">
                <input type="hidden" id="wanted-category-id" name="category_id" value="{{ optional($categories->first())->id }}">

                <span class="wanted-section-label">{{ __('Select your property goal') }}</span>
                <div class="wanted-goal-grid">
                    <button type="button" class="wanted-goal-tile" data-type="buy">
                        <span class="wanted-goal-tile__check"><i class="fas fa-check"></i></span>
                        <span class="wanted-goal-tile__icon"><i class="fas fa-home"></i></span>
                        <span class="wanted-goal-tile__title">{{ __('Buy Property') }}</span>
                        <span class="wanted-goal-tile__subtitle">{{ __('Find your perfect place') }}</span>
                    </button>
                    <button type="button" class="wanted-goal-tile" data-type="rent">
                        <span class="wanted-goal-tile__check"><i class="fas fa-check"></i></span>
                        <span class="wanted-goal-tile__icon"><i class="fas fa-key"></i></span>
                        <span class="wanted-goal-tile__title">{{ __('Rent Property') }}</span>
                        <span class="wanted-goal-tile__subtitle">{{ __('A place to call home') }}</span>
                    </button>
                    <button type="button" class="wanted-goal-tile" data-type="invest">
                        <span class="wanted-goal-tile__check"><i class="fas fa-check"></i></span>
                        <span class="wanted-goal-tile__icon"><i class="fas fa-chart-line"></i></span>
                        <span class="wanted-goal-tile__title">{{ __('Invest') }}</span>
                        <span class="wanted-goal-tile__subtitle">{{ __('Grow your portfolio') }}</span>
                    </button>
                    <button type="button" class="wanted-goal-tile" data-type="build">
                        <span class="wanted-goal-tile__check"><i class="fas fa-check"></i></span>
                        <span class="wanted-goal-tile__icon"><i class="fas fa-drafting-compass"></i></span>
                        <span class="wanted-goal-tile__title">{{ __('Build & Construct') }}</span>
                        <span class="wanted-goal-tile__subtitle">{{ __('Bring your vision to life') }}</span>
                    </button>
                </div>

                <div class="wanted-subgroup">
                    <div class="wanted-subgroup__row">
                        <span class="wanted-section-label">{{ __('Property category') }}</span>
                        <div class="wizard-chip-row" data-category-row>
                            @foreach($categories as $cat)
                                <button type="button" class="wizard-chip-btn" data-id="{{ $cat->id }}">{{ $cat->name }}</button>
                            @endforeach
                        </div>
                    </div>
                    <div class="wanted-subgroup__row">
                        <span class="wanted-section-label">{{ __('Property type') }}</span>
                        <div class="wizard-chip-row" data-subcategory-row data-empty-label="{{ __('No property types for this category') }}"></div>
                    </div>
                </div>

                <div class="wizard-field-grid">
                    <div class="wizard-field">
                        <label class="required" for="wanted-name">{{ __('Full Name') }}</label>
                        <input type="text" id="wanted-name" name="name" class="wizard-input" placeholder="{{ __('Enter your full name') }}" required minlength="3" maxlength="100">
                    </div>
                    <div class="wizard-field">
                        <label class="required" for="wanted-email">{{ __('Email Address') }}</label>
                        <input type="email" id="wanted-email" name="email" class="wizard-input" placeholder="name@example.com" required>
                    </div>
                    <div class="wizard-field">
                        <label class="required" for="wanted-mobile-no">{{ __('Phone Number') }}</label>
                        <input type="text" id="wanted-mobile-no" name="mobile_no" class="wizard-input" placeholder="+92 300 1234567" required>
                    </div>
                    <div class="wizard-field">
                        <label class="required" for="wanted-city-id">{{ __('Preferred City') }}</label>
                        <div class="wizard-combobox" data-combobox="city"
                             data-combobox-options="{{ collect($city)->map(fn($name, $id) => ['id' => $id, 'name' => $name])->values()->toJson() }}">
                            <input type="text" id="wanted-city-id" class="wizard-input" data-combobox-input autocomplete="off" placeholder="{{ __('Search city...') }}">
                            <input type="hidden" name="city_id" data-combobox-value>
                            <div class="wizard-combobox__menu" data-combobox-menu></div>
                        </div>
                        <div class="wizard-error" data-error-for="city_id"></div>
                    </div>
                    <div class="wizard-field">
                        <label class="required" for="wanted-city-area-id">{{ __('Preferred Area') }}</label>
                        <div class="wizard-combobox" data-combobox="city_area" data-combobox-empty="{{ __('Select a city first') }}">
                            <input type="text" id="wanted-city-area-id" class="wizard-input" data-combobox-input autocomplete="off" placeholder="{{ __('Select a city first') }}">
                            <input type="hidden" name="city_area_id" data-combobox-value>
                            <div class="wizard-combobox__menu" data-combobox-menu></div>
                        </div>
                        <div class="wizard-error" data-error-for="city_area_id"></div>
                    </div>
                    <div class="wizard-field">
                        <label for="wanted-amount">{{ __('Estimated Budget') }}</label>
                        <select id="wanted-amount" name="amount" class="wizard-select">
                            <option value="">{{ __('Choose your budget') }}</option>
                            @foreach($budgetOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="wizard-field wizard-field--span2 wanted-project-block" data-project-block hidden>
                        <div class="wizard-field-grid">
                            <div class="wizard-field">
                                <label for="wanted-project-select">{{ __('Project') }}</label>
                                <select id="wanted-project-select" name="project_select" class="wizard-select">
                                    <option value="">{{ __('Select project...') }}</option>
                                    @foreach($projects as $key => $val)
                                        <option value="{{ $val }}">{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="wizard-field">
                                <label for="wanted-new-project-value">{{ __('New Project') }}</label>
                                <div class="wizard-checkbox" style="margin-bottom:8px;">
                                    <input type="checkbox" id="wanted-new-project" name="new_project">
                                    <label for="wanted-new-project" style="margin:0;font-weight:500;">{{ __("It's a new project") }}</label>
                                </div>
                                <input type="text" id="wanted-new-project-value" name="new_project_value" class="wizard-input" placeholder="{{ __('New project name') }}" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="wizard-field wizard-field--span2">
                        <label class="required" for="wanted-comments">{{ __('Tell us about your requirements') }}</label>
                        <textarea id="wanted-comments" name="comments" class="wizard-textarea" placeholder="{{ __('Share your preferred property size, location, budget or construction plans...') }}" required minlength="5" maxlength="255"></textarea>
                    </div>
                </div>

                <div class="alert alert-success" data-alert-success hidden style="margin-top:16px;"><span></span></div>
                <div class="alert alert-danger" data-alert-error hidden style="margin-top:16px;"><ul style="margin:0;padding-left:18px;"></ul></div>

                <div class="wanted-form-footer">
                    <span class="wanted-form-footer__note"><i class="fas fa-check-circle"></i>{{ __('Our team can use these details to understand your requirements.') }}</span>
                    <button type="submit" id="wanted-submit-btn" class="wizard-btn wizard-btn--primary">
                        {{ __('Submit Your Request') }} <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>

<script src="{{ Theme::asset()->url('js/wanted/wanted-page.js') }}"></script>
