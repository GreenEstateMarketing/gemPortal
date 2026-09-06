@php
    $p = $property;
    $squareValue = old('square', $p->square);

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
    }

    $subcategories = $selectedParentId
        ? $categories->where('parent_id', $selectedParentId)->values()
        : collect();

    $categoryNameValue = optional($currentCategory)->name;

    $currentTemplate = $p->category_id
        ? \App\Models\description_template::where('status', 1)->where('category_id', $p->category_id)->first()
        : null;
@endphp

<div class="wizard-panel">
    <h2 class="wizard-panel__heading">{{ __('Basics & Price') }}</h2>
    <p class="wizard-panel__description">{{ __('Tell us what you are listing and how much it costs.') }}</p>

    <form data-step-form action="{{ $stepUrls['basics'] }}" method="post" data-category-tree="{{ $categories->map(function ($c) { return ['id' => $c->id, 'name' => $c->name, 'parent_id' => (int) $c->parent_id]; })->toJson() }}">
        <div class="wizard-field-grid">
            <div class="wizard-field wizard-field--span2">
                <label>{{ __('Ad Title') }}</label>
                <input type="text" class="wizard-input" data-field="name" value="{{ $p->name === 'Untitled draft' ? '' : $p->name }}" placeholder="{{ __('e.g. Modern 3 Bedroom Apartment in Downtown') }}">
                <div class="wizard-error" data-error-for="name"></div>
            </div>

            <div class="wizard-field">
                <label>{{ __('Listing Type') }}</label>
                <select class="wizard-select" data-field="type" id="wizard-type">
                    <option value="sale" {{ ($p->type == 'sale') ? 'selected' : '' }}>{{ __('For Sale') }}</option>
                    <option value="rent" {{ ($p->type == 'rent') ? 'selected' : '' }}>{{ __('For Rent') }}</option>
                </select>
                <div class="wizard-error" data-error-for="type"></div>
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
                <label>{{ __('Category') }}</label>
                <select class="wizard-select" id="wizard-category">
                    <option value="">{{ __('Select a category') }}</option>
                    @foreach ($topCategories as $category)
                        <option value="{{ $category->id }}" {{ $selectedParentId == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="wizard-field">
                <label>{{ __('Sub-category') }}</label>
                <select class="wizard-select" id="wizard-subcategory" {{ $subcategories->isEmpty() ? 'disabled' : '' }}>
                    <option value="">{{ __('Select a sub-category') }}</option>
                    @foreach ($subcategories as $subcategory)
                        <option value="{{ $subcategory->id }}" {{ $selectedSubId == $subcategory->id ? 'selected' : '' }}>{{ $subcategory->name }}</option>
                    @endforeach
                </select>
                <div class="wizard-error" data-error-for="category_id"></div>
            </div>

            <input type="hidden" data-field="category_id" id="wizard-category-id" value="{{ $p->category_id }}">
            <input type="hidden" id="wizard-category-name" value="{{ $categoryNameValue }}">

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
                            <option value="{{ $currency->id }}" {{ ($p->currency_id == $currency->id) ? 'selected' : '' }}>{{ $currency->symbol ?: $currency->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="wizard-error" data-error-for="price"></div>
            </div>

            <div class="wizard-field" data-rent-only-field style="{{ $p->type == 'rent' ? '' : 'display:none;' }}">
                <label>{{ __('Price Unit') }} <span class="wizard-hint">({{ __('optional') }})</span></label>
                <input type="text" class="wizard-input" data-field="price_unit" value="{{ $p->price_unit }}" placeholder="{{ __('e.g. /month') }}">
            </div>

            <div class="wizard-field">
                <label>{{ __('Area') }}</label>
                <div class="wizard-input-group">
                    <input type="number" step="0.01" min="0" class="wizard-input" data-field="square" id="wizard-square" value="{{ $squareValue }}" placeholder="{{ __('e.g. 1200') }}">
                    <select class="wizard-select" data-field="area_units" id="wizard-area-units" style="max-width: 110px;">
                        <option value="ft2">{{ __('sq ft') }}</option>
                        <option value="m2">{{ __('sq m') }}</option>
                        <option value="marla">{{ __('marla') }}</option>
                        <option value="yards">{{ __('yards') }}</option>
                        <option value="kanal">{{ __('kanal') }}</option>
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

        <div class="wizard-panel__actions">
            <span></span>
            <button type="submit" class="wizard-btn wizard-btn--primary" data-step-submit data-loading-text="{{ __('Saving...') }}">
                {{ __('Save & Continue') }} <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </form>
</div>
