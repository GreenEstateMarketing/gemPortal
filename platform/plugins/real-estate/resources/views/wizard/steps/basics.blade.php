@php
    $p = $property;

    // The site-wide area unit ('real_estate_square_unit', used to display
    // every property's square footage - see Property::square_text) is
    // stored using ft²/m² unicode symbols; the wizard's own <select> uses
    // plain ft2/m2 since that's what getSqFeet() expects. Auto-select
    // whichever one matches the current setting rather than always
    // defaulting to the first option.
    $displayToAsciiAreaUnit = ['ft²' => 'ft2', 'm²' => 'm2', 'marla' => 'marla', 'yards' => 'yards', 'kanal' => 'kanal'];
    $currentDisplayUnit = setting('real_estate_square_unit', 'm²');
    $selectedAreaUnit = old('area_units', $displayToAsciiAreaUnit[$currentDisplayUnit] ?? 'ft2');

    // Property::square is always stored in sq ft (see PropertySubmissionService::saveBasics());
    // convert it to match whichever unit is pre-selected above rather than
    // showing the raw sq-ft number next to a different unit label.
    $squareValue = old('square', $p->square ? getDefaultAreaByUnit($p->square, $currentDisplayUnit) : $p->square);

    // Conversion factors for the JS live-recompute below, keyed the same
    // way as the <select>'s option values - each is "how many sq ft is 1 of
    // this unit" (ft2's is trivially 1), matching getSqFeet()'s own factors
    // so the round trip stays consistent with what actually gets saved.
    $areaUnitToSqFtFactor = [
        'ft2' => 1,
        'm2' => setting('real_estate_square_meter_to_sq_ft'),
        'marla' => setting('real_estate_marla_to_square_ft'),
        'yards' => setting('real_estate_yards_to_sq_ft'),
        'kanal' => setting('real_estate_kanal_to_sq_ft'),
    ];

    // Same idea for currency - default to whichever one is flagged
    // is_default rather than leaving the dropdown on its blank placeholder
    // (which the browser then silently submits as an empty currency_id).
    $selectedCurrencyId = old('currency_id', $p->currency_id ?: optional($currencies->firstWhere('is_default', 1))->id);

    $topCategories = $categories->where('parent_id', 0)->values();
    $currentCategory = $categories->firstWhere('id', $p->category_id);

    $selectedParentId = null;
    $selectedSubId = null;

    if ($currentCategory) {
        if ((int) $currentCategory->parent_id === 0) {
            $selectedParentId = $currentCategory->id;
        } else {
            $selectedParentId = $currentCategory->parent_id;
            $selectedSubId = $currentCategory->id;
        }
    } else {
        // Nothing chosen yet (brand new draft) - default to the first
        // category and its first sub-category rather than leaving it empty.
        $selectedParentId = optional($topCategories->first())->id;
    }

    $subcategories = $selectedParentId
        ? $categories->where('parent_id', $selectedParentId)->values()
        : collect();

    if (! $selectedSubId && $subcategories->isNotEmpty()) {
        $selectedSubId = $subcategories->first()->id;
    }

    $effectiveCategoryId = $selectedSubId ?: $selectedParentId;
    $categoryNameValue = optional($categories->firstWhere('id', $effectiveCategoryId))->name;
    $effectiveType = $p->type ?: 'sale';

    $currentTemplate = $effectiveCategoryId
        ? \App\Models\description_template::where('status', 1)->where('category_id', $effectiveCategoryId)->first()
        : null;
@endphp

<div class="wizard-panel">
    <h2 class="wizard-panel__heading">{{ __('Basics & Price') }}</h2>
    <p class="wizard-panel__description">{{ __('Tell us what you are listing and how much it costs.') }}</p>

    <form {{ ($isLocked ?? false) ? '' : 'data-step-form' }} action="{{ $stepUrls['basics'] }}" method="post" data-category-tree="{{ $categories->map(function ($c) { return ['id' => $c->id, 'name' => $c->name, 'parent_id' => (int) $c->parent_id]; })->toJson() }}">
        <fieldset {{ ($isLocked ?? false) ? 'disabled' : '' }} style="border:0; padding:0; margin:0;">
        <div class="wizard-field-grid">
            <div class="wizard-field wizard-field--span2">
                <label>{{ __('Listing Type') }}</label>
                <div class="wizard-toggle-group" data-type-toggle>
                    <button type="button" class="wizard-toggle-btn {{ $effectiveType == 'sale' ? 'wizard-toggle-btn--active' : '' }}" data-type-value="sale">
                        <i class="fas fa-tag"></i> {{ __('For Sale') }}
                    </button>
                    <button type="button" class="wizard-toggle-btn {{ $effectiveType == 'rent' ? 'wizard-toggle-btn--active' : '' }}" data-type-value="rent">
                        <i class="fas fa-key"></i> {{ __('For Rent') }}
                    </button>
                </div>
                <input type="hidden" data-field="type" id="wizard-type" value="{{ $effectiveType }}">
                <div class="wizard-error" data-error-for="type"></div>
            </div>

            <div class="wizard-field wizard-field--span2">
                <label>{{ __('Category') }}</label>
                <div class="wizard-chip-row" data-category-row>
                    @foreach ($topCategories as $category)
                        <button type="button" class="wizard-chip-btn {{ $selectedParentId == $category->id ? 'wizard-chip-btn--active' : '' }}" data-category-option="{{ $category->id }}">{{ $category->name }}</button>
                    @endforeach
                </div>
            </div>

            <div class="wizard-field wizard-field--span2">
                <label>{{ __('Sub-category') }}</label>
                <div class="wizard-chip-row" data-subcategory-row data-empty-label="{{ __('This category has no sub-categories') }}">
                    @foreach ($subcategories as $subcategory)
                        <button type="button" class="wizard-chip-btn {{ $selectedSubId == $subcategory->id ? 'wizard-chip-btn--active' : '' }}" data-subcategory-option="{{ $subcategory->id }}">{{ $subcategory->name }}</button>
                    @endforeach
                </div>
                <div class="wizard-error" data-error-for="category_id"></div>
            </div>

            <input type="hidden" data-field="category_id" id="wizard-category-id" value="{{ $effectiveCategoryId }}">
            <input type="hidden" id="wizard-category-name" value="{{ $categoryNameValue }}">

            <div class="wizard-field wizard-field--span2">
                <label>{{ __('Ad Title') }} <span class="wizard-hint">({{ __('be clear and specific - this is the first thing buyers see') }})</span></label>
                <input type="text" class="wizard-input" data-field="name" value="{{ $p->name === 'Untitled draft' ? '' : $p->name }}" placeholder="{{ __('e.g. Modern 3 Bedroom Apartment in Downtown') }}">
                <div class="wizard-error" data-error-for="name"></div>
            </div>

            <div class="wizard-field">
                <label>{{ __('Project') }} <span class="wizard-hint">({{ __('optional') }})</span></label>
                <select class="wizard-select" data-field="project_id">
                    <option value="">{{ __('No project - standalone listing') }}</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" {{ ($p->project_id == $project->id) ? 'selected' : '' }}>{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="wizard-field">
                <label>{{ __('Built In') }} <span class="wizard-hint">({{ __('optional') }})</span></label>
                <input type="text" class="wizard-input" data-field="built_in" value="{{ $p->built_in }}" placeholder="{{ __('e.g. 2019') }}">
            </div>

            <div class="wizard-field wizard-field--span2">
                <label>{{ __('Description') }}</label>
                <textarea class="wizard-textarea" data-field="description" id="wizard-description" readonly placeholder="{{ __('Select a category above to generate a description automatically.') }}">{{ $p->description }}</textarea>
                <span class="wizard-hint">{{ __('Generated automatically from your category and details above.') }}</span>
                <input type="hidden" id="wizard-template-description" value="{{ $currentTemplate->detail ?? '' }}">
            </div>

            <div class="wizard-field">
                <label>{{ __('Price') }}</label>
                <div class="wizard-input-group">
                    <input type="number" step="0.01" min="0" class="wizard-input" data-field="price" value="{{ $p->price }}" placeholder="{{ __('e.g. 150000') }}">
                    <select class="wizard-select" data-field="currency_id" style="max-width: 110px;">
                        <option value="">{{ __('Currency') }}</option>
                        @foreach ($currencies as $currency)
                            <option value="{{ $currency->id }}" {{ $selectedCurrencyId == $currency->id ? 'selected' : '' }}>{{ $currency->symbol ?: $currency->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="wizard-error" data-error-for="price"></div>
            </div>

            <div class="wizard-field" data-rent-only-field style="{{ $effectiveType == 'rent' ? '' : 'display:none;' }}">
                <label>{{ __('Price Unit') }} <span class="wizard-hint">({{ __('optional') }})</span></label>
                <input type="text" class="wizard-input" data-field="price_unit" value="{{ $p->price_unit }}" placeholder="{{ __('e.g. /month') }}">
            </div>

            <div class="wizard-field">
                <label>{{ __('Area') }}</label>
                <div class="wizard-input-group">
                    <input type="number" step="0.01" min="0" class="wizard-input" data-field="square" id="wizard-square" value="{{ $squareValue }}" placeholder="{{ __('e.g. 1200') }}">
                    <select class="wizard-select" data-field="area_units" id="wizard-area-units" style="max-width: 110px;" data-area-factors="{{ json_encode($areaUnitToSqFtFactor) }}" data-previous-unit="{{ $selectedAreaUnit }}">
                        <option value="ft2" {{ $selectedAreaUnit === 'ft2' ? 'selected' : '' }}>{{ __('sq ft') }}</option>
                        <option value="m2" {{ $selectedAreaUnit === 'm2' ? 'selected' : '' }}>{{ __('sq m') }}</option>
                        <option value="marla" {{ $selectedAreaUnit === 'marla' ? 'selected' : '' }}>{{ __('marla') }}</option>
                        <option value="yards" {{ $selectedAreaUnit === 'yards' ? 'selected' : '' }}>{{ __('yards') }}</option>
                        <option value="kanal" {{ $selectedAreaUnit === 'kanal' ? 'selected' : '' }}>{{ __('kanal') }}</option>
                    </select>
                </div>
                <div class="wizard-error" data-error-for="square"></div>
            </div>

            <div class="wizard-field">
                <label>{{ __('Bedrooms') }}</label>
                <input type="number" min="0" class="wizard-input" data-field="number_bedroom" id="wizard-number-bedroom" value="{{ $p->number_bedroom }}" placeholder="{{ __('e.g. 3') }}">
            </div>

            <div class="wizard-field">
                <label>{{ __('Bathrooms') }}</label>
                <input type="number" min="0" class="wizard-input" data-field="number_bathroom" id="wizard-number-bathroom" value="{{ $p->number_bathroom }}" placeholder="{{ __('e.g. 2') }}">
            </div>

            <div class="wizard-field">
                <label>{{ __('Floors') }}</label>
                <input type="number" min="0" class="wizard-input" data-field="number_floor" id="wizard-number-floor" value="{{ $p->number_floor }}" placeholder="{{ __('e.g. 1') }}">
            </div>
        </div>
        </fieldset>

        <div class="wizard-panel__actions">
            <span></span>
            @if ($isLocked ?? false)
                <a href="{{ $showBaseUrl }}?step=2" class="wizard-btn wizard-btn--primary">
                    {{ __('Next') }} <i class="fas fa-arrow-right"></i>
                </a>
            @else
                <button type="submit" class="wizard-btn wizard-btn--primary" data-step-submit data-loading-text="{{ __('Saving...') }}">
                    {{ __('Save & Continue') }} <i class="fas fa-arrow-right"></i>
                </button>
            @endif
        </div>
    </form>
</div>
