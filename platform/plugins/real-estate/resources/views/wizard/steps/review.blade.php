@php
    $p = $property;
    $currency = $p->currency;
    $priceLabel = $p->price ? number_format($p->price, 0) . ($currency && $currency->symbol ? ' ' . $currency->symbol : '') : '-';
    $imageItems = collect($p->images)->values()->all();
    $documentItems = json_decode($p->documents ?: '[]', true) ?: [];
    $documentNameById = $categoryDocuments->pluck('document.name', 'document_id');
@endphp

<div class="wizard-panel" data-finalize-url="{{ $stepUrls['finalize'] }}">
    <h2 class="wizard-panel__heading">{{ __('Review Your Ad') }}</h2>
    <p class="wizard-panel__description">{{ __('Double check everything below before submitting.') }}</p>

    <div class="wizard-review-section">
        <div class="wizard-review-section__head">
            <h3>{{ __('Basics & Price') }}</h3>
            <a href="{{ $showBaseUrl }}?step=1" class="wizard-btn wizard-btn--ghost">{{ __('Edit') }}</a>
        </div>
        <dl class="wizard-review-grid">
            <div class="wizard-review-item"><dt>{{ __('Title') }}</dt><dd>{{ $p->name }}</dd></div>
            <div class="wizard-review-item"><dt>{{ __('Listing Type') }}</dt><dd>{{ $p->type == 'rent' ? __('For Rent') : __('For Sale') }}</dd></div>
            <div class="wizard-review-item"><dt>{{ __('Category') }}</dt><dd>{{ optional($p->category)->name ?: '-' }}</dd></div>
            <div class="wizard-review-item"><dt>{{ __('Price') }}</dt><dd>{{ $priceLabel }} {{ $p->price_unit }}</dd></div>
            <div class="wizard-review-item"><dt>{{ __('Area') }}</dt><dd>{{ $p->square_text }}</dd></div>
            <div class="wizard-review-item"><dt>{{ __('Bed / Bath') }}</dt><dd>{{ $p->number_bedroom ?: 0 }} / {{ $p->number_bathroom ?: 0 }}</dd></div>
        </dl>
    </div>

    <div class="wizard-review-section">
        <div class="wizard-review-section__head">
            <h3>{{ __('Location & Details') }}</h3>
            <a href="{{ $showBaseUrl }}?step=2" class="wizard-btn wizard-btn--ghost">{{ __('Edit') }}</a>
        </div>
        <dl class="wizard-review-grid">
            <div class="wizard-review-item wizard-field--span2"><dt>{{ __('Address') }}</dt><dd>{{ $p->location }}</dd></div>
            <div class="wizard-review-item"><dt>{{ __('City') }}</dt><dd>{{ optional($p->city)->name ?: '-' }}</dd></div>
            <div class="wizard-review-item"><dt>{{ __('City Area') }}</dt><dd>{{ optional($p->cityArea)->city_area_name ?: '-' }}</dd></div>
            <div class="wizard-review-item"><dt>{{ __('Features') }}</dt><dd>{{ $p->features->pluck('name')->join(', ') ?: '-' }}</dd></div>
        </dl>
    </div>

    <div class="wizard-review-section">
        <div class="wizard-review-section__head">
            <h3>{{ __('Media & Documents') }}</h3>
            <a href="{{ $showBaseUrl }}?step=3" class="wizard-btn wizard-btn--ghost">{{ __('Edit') }}</a>
        </div>
        <div class="wizard-thumbs">
            @forelse ($imageItems as $image)
                <div class="wizard-thumb"><img src="{{ RvMedia::getImageUrl($image) }}" alt=""></div>
            @empty
                <p class="wizard-hint">{{ __('No photos added yet.') }}</p>
            @endforelse
        </div>

        <div class="wizard-thumbs wizard-thumbs--documents" style="margin-top:14px;">
            @forelse ($documentItems as $document)
                @php
                    $documentId = is_array($document) ? ($document['document_id'] ?? null) : null;
                    $documentLabel = ($documentId && $documentNameById->has($documentId))
                        ? $documentNameById->get($documentId)
                        : (is_array($document) ? ($document['name'] ?? __('Document')) : __('Document'));
                    $documentUrl = is_array($document) ? ($document['url'] ?? '') : '';
                @endphp
                <div class="wizard-doc-item">
                    @if ($documentUrl)
                        <a href="{{ RvMedia::url($documentUrl) }}" target="_blank" rel="noopener" download><i class="fas fa-file"></i> {{ $documentLabel }}</a>
                    @else
                        <span><i class="fas fa-file"></i> {{ $documentLabel }}</span>
                    @endif
                </div>
            @empty
                <p class="wizard-hint">{{ __('No documents added.') }}</p>
            @endforelse
        </div>

        <dl class="wizard-review-grid" style="margin-top:14px;">
            <div class="wizard-review-item"><dt>{{ __('Auto-renew') }}</dt><dd>{{ $p->auto_renew ? __('Yes') : __('No') }}</dd></div>
        </dl>
    </div>

    @if ($role === 'guest')
        <div class="wizard-guest-auth" data-guest-auth data-authenticate-url="{{ $authenticateUrl }}" style="display:none;">
            <h3>{{ __('Almost done - log in or create your free account to publish this ad') }}</h3>

            <div class="wizard-guest-auth__toggle">
                <label>
                    <input type="radio" name="member_status" value="existing_user" data-field="member_status" checked>
                    {{ __('I already have an account') }}
                </label>
                <label>
                    <input type="radio" name="member_status" value="new_user" data-field="member_status">
                    {{ __('Create a new account') }}
                </label>
            </div>

            <div data-existing-fields>
                <div class="wizard-field-grid">
                    <div class="wizard-field">
                        <label>{{ __('Email') }}</label>
                        <input type="email" class="wizard-input" data-field="email" placeholder="{{ __('you@example.com') }}">
                        <div class="wizard-error" data-error-for="email"></div>
                    </div>
                    <div class="wizard-field">
                        <label>{{ __('Password') }}</label>
                        <input type="password" class="wizard-input" data-field="password" placeholder="{{ __('Your password') }}">
                        <div class="wizard-error" data-error-for="password"></div>
                    </div>
                </div>
            </div>

            <div data-new-fields style="display:none;">
                <div class="wizard-field-grid">
                    <div class="wizard-field">
                        <label>{{ __('Full Name') }}</label>
                        <input type="text" class="wizard-input" data-field="full_name" placeholder="{{ __('e.g. John Smith') }}">
                        <div class="wizard-error" data-error-for="full_name"></div>
                    </div>
                    <div class="wizard-field">
                        <label>{{ __('Email') }}</label>
                        <input type="email" class="wizard-input" data-field="new_email" placeholder="{{ __('you@example.com') }}">
                        <div class="wizard-error" data-error-for="new_email"></div>
                    </div>
                    <div class="wizard-field">
                        <label>{{ __('Mobile Number') }}</label>
                        <input type="text" class="wizard-input" data-field="mobile_number" placeholder="{{ __('e.g. +1234567890') }}">
                        <div class="wizard-error" data-error-for="mobile_number"></div>
                    </div>
                    <div class="wizard-field">
                        <label>{{ __('Password') }}</label>
                        <input type="password" class="wizard-input" data-field="new_password" placeholder="{{ __('At least 6 characters') }}">
                        <div class="wizard-error" data-error-for="new_password"></div>
                    </div>
                </div>
            </div>

            <label class="wizard-checkbox" style="margin-top:14px;">
                <input type="checkbox" data-field="terms">
                {{ __('I accept the Terms & Conditions') }}
            </label>
            <div class="wizard-error" data-error-for="terms"></div>

            <div class="wizard-panel__actions">
                <span></span>
                <button type="button" class="wizard-btn wizard-btn--primary" data-guest-auth-submit data-loading-text="{{ __('Please wait...') }}">
                    {{ __('Create Account & Publish') }} <i class="fas fa-check"></i>
                </button>
            </div>
        </div>
    @endif

    <div class="wizard-panel__actions">
        <a href="{{ $showBaseUrl }}?step=3" class="wizard-btn wizard-btn--ghost"><i class="fas fa-arrow-left"></i> {{ __('Back') }}</a>
        <button type="button" class="wizard-btn wizard-btn--primary" data-finalize-submit data-loading-text="{{ __('Submitting...') }}">
            {{ __('Submit Ad') }} <i class="fas fa-check"></i>
        </button>
    </div>
</div>
