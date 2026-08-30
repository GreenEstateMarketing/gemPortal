{{--
    Path in theme:  platform/themes/real-scout/partials/home-page-new/search-bar.blade.php
    Rendered via:   {!! Theme::partial('home-page-new/search-bar') !!}
    Included from:  home-page-new/header.blade.php, inside the hero section

    IMPORTANT: This ports the working search form from the old header file.
    All functional attributes (id, name, form action, data-* attributes) are
    left UNCHANGED so any existing JS (autocomplete, category popover, price
    range lists, area unit lists, currency/area modals) keeps working exactly
    as before. Only wrapper markup and classes were added/changed for styling.
--}}
@if (is_plugin_active('real-estate'))
    <div class="hero-search-card">

        {{-- ============ TYPE TABS ============ --}}
        <div class="typesearch hero-search-card__tabs" id="hometypesearch">
            <a href="javascript:void(0)" class="active top-left-radius hero-search-card__tab" rel="sale"
                data-url="{{ route('public.properties') }}">{{ __('Buy') }}</a>
            <a href="javascript:void(0)" class="hero-search-card__tab" rel="rent"
                data-url="{{ route('public.properties') }}">{{ __('Rent') }}</a>
            <a href="javascript:void(0)" class="top-right-radius hero-search-card__tab" rel="project"
                data-url="{{ route('public.projects') }}">{{ __('Projects') }}</a>

            <span class="hero-search-card__search-label">
                <i class="far fa-search"></i> {{ __('Search Property') }}
            </span>
        </div>

        <form action="{{ route('public.properties') }}" method="GET" id="frmhomesearch" class="hero-search-card__form">
            <input type="hidden" id="selected-unit" name="selected-unit"
                value="{{ getDefaultAreaByUnitForNextPage() }}" />
            <input type="hidden" name="type" value="sale" id="txttypesearch">

            <div class="hero-search-card__fields">

                {{-- ============ LOCATION ============ --}}
                <div class="hero-search-card__field hero-search-card__field--location">
                    <span class="hero-search-card__field-label">{{ __('Location') }}</span>

                    {{-- Matches the old realscouthomepage layout: an area/keyword text
                         input side by side with a City dropdown. The city select is
                         auto-populated by scripts.js's geolocation lookup (browser
                         geolocation -> Google reverse-geocode -> city name written into
                         the hidden #city-name-from-map -> homechoosen.js matches it to
                         an <option> here and selects it) - that logic already runs
                         unconditionally on "/", it was just never visible before since
                         this field used to hide the select entirely. --}}
                    <div class="hero-search-card__location-row">
                        <div id="parentChipContainer" class="keyword-input hero-search-card__location-input">
                            <div id="chipContainer">
                                <div class="position-relative input-field-container">
                                    <input placeholder="{{ __('Area') }}" class="hero-search-card__input"
                                        type="text" name="" id="autocomplete-ajax" autocomplete="off" />
                                    <input class="hero-search-card__input hero-search-card__input--ghost" type="text"
                                        name="" id="autocomplete-ajax-x" disabled="disabled" />
                                </div>
                                <div id="chipViewMore" class="chip" style="display:none">
                                    <div class="chip-content"></div>
                                </div>
                            </div>
                        </div>

                        <div class="hero-search-card__location-divider"></div>

                        <input id="city-name-from-map" type="hidden" class="select-city-state" autocomplete="off" />
                        <select class="hero-search-card__city-select" id="city_id" name="city_id">
                            <option value="0">{{ __('Select city...') }}</option>
                            @foreach (app(\Botble\Location\Repositories\Interfaces\CityInterface::class)->allBy(
                                ['status' => \Botble\Base\Enums\BaseStatusEnum::PUBLISHED, 'country_id' => 166],
                                ['state', 'country'],
                                ['cities.name', 'cities.state_id', 'cities.country_id', 'cities.id'],
                            ) as $city)
                                <option value={{ $city->id }}>
                                    {{ $city->name . ($city->state->name ? ' (' . $city->state->name . ')' : '') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="hero-search-card__divider"></div>

                {{-- ============ PROPERTY TYPE (relocated from the old "Advanced" panel) ============ --}}
                <div class="hero-search-card__field hero-search-card__field--type">
                    <span class="hero-search-card__field-label">{{ __('Property Type') }}</span>
                    <div class="form-control hero-search-card__dropdown-trigger">
                        <input type="hidden" name="category_id" class="category_id">
                        {{-- Real-estate plugin JS binds its click-to-open handler to this
                             exact id and writes the selected label into the sibling
                             ".category_id_text" span below. Left empty/zero-size on
                             purpose - home-page-new/header.js forwards clicks anywhere
                             on the trigger to this element so the visible label
                             (.category_id_text) and the arrow stay clickable too.
                             NOTE: no "dropdown-toggle" class here on purpose - that's
                             a bootstrap class whose own ::after CSS draws a second
                             caret glyph immediately after this (empty) span, to the
                             left of the label. This element doesn't use bootstrap's
                             dropdown JS at all (scripts.js binds its own plain click
                             handler by id), so the class was purely a leftover and
                             only the intentional caret on the trigger's right edge
                             (.hero-search-card__dropdown-trigger::after) should show. --}}
                        <span id="propertydropdownMenuLink"></span>
                        <span class="category_id_text hero-search-card__dropdown-toggle">{{ __('Any Type') }}</span>
                    </div>

                    {{-- Category popover -- unchanged markup, just relocated to sit near its trigger --}}
                    <div class="property-category-search-dropdown" style="display:none">
                        <div class="category-search-list" role="listbox">
                            <div>
                                <div>
                                    <ul class="category-ul" name="Category picker">
                                        <div>
                                            @foreach (app(\Botble\RealEstate\Repositories\Interfaces\CategoryInterface::class)->pluck('re_categories.name', 're_categories.id', ['parent_id' => 0]) as $categoryId => $categoryName)
                                                @if ($loop->index == 0)
                                                    <li class="category-parent-active p-category"
                                                        data-id="{{ $categoryId }}">{{ $categoryName }}</li>
                                                @else
                                                    <li class="category-parent-inactive p-category"
                                                        data-id="{{ $categoryId }}">{{ $categoryName }}</li>
                                                @endif
                                                <div class="p{{ $categoryId }}" style="display:none">
                                                    <div class="row">
                                                        @foreach (app(\Botble\RealEstate\Repositories\Interfaces\CategoryInterface::class)->pluck('re_categories.name', 're_categories.id', ['parent_id' => $categoryId]) as $subcategoryId => $subcategoryName)
                                                            <div class="@if ($loop->count == 1) col-md-12 @else col-md-6 @endif">
                                                                <li class="category-li-item"
                                                                    parent-name="{{ $categoryName }}"
                                                                    data-id="{{ $subcategoryId }}">
                                                                    {{ $subcategoryName }}</li>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="category-li pcateory_data"></div>
                                    </ul>
                                </div>
                            </div>
                            <div class="ab3dd470"></div>
                        </div>
                    </div>
                </div>

                {{-- ============ SUBMIT ============ --}}
                <div class="input-group-append search-button-wrapper hero-search-card__submit-wrapper">
                    <button class="btn btn-orange hero-search-card__submit" id="submitBtn" type="submit">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            {{-- ============ MORE FILTERS (formerly "Advanced") ============
                 Price Range / Area Unit / Change Currency sit in the same bar
                 as the "More Filters" toggle (not inside its collapsible
                 panel) so they stay reachable without expanding it, and so
                 Location/Property Type above don't need a 4th squeezed field
                 for them. --}}
            <div class="advanced-search hero-search-card__advanced">
                <div class="hero-search-card__advanced-bar">
                    <a href="#" class="advanced-search-toggler hero-search-card__advanced-toggle">
                        {{ __('More Filters') }} <i class="fas fa-caret-down"></i>
                    </a>

                    <div class="price-dropdown home-price-dp hero-search-card__advanced-bar-item">
                        <div class="dropdown">
                            <a id="min-max-price-range" class="hero-search-card__advanced-bar-trigger dropdown-toggle"
                                href="#" data-toggle="dropdown">
                                {{ __('Price Range') }}:
                                <span class="min_price_text">0</span> - <span class="max_price_text">{{ __('Any') }}</span>
                                <span class="currency">{{ CurrentCurrency()->title }}</span>
                            </a>
                            <div class="dropdown-menu" style="padding:10px;width:100%">
                                <div class="row justify-content-center">
                                    <div class="col-6">
                                        <input class="form-control price-label"
                                            style="border:1px solid #a0a0a0 !important;" name="min_price"
                                            placeholder="Min" data-dropdown-id="price-min" />
                                    </div>
                                    <div class="col-6">
                                        <input class="form-control price-label"
                                            style="border:1px solid #a0a0a0 !important;" name="max_price"
                                            placeholder="Max" data-dropdown-id="price-max" />
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="row mt-2 justify-content-center">
                                    <div class="col-md-6">
                                        <ul class="price-range col-md-12 price-min-ul list-unstyled"
                                            style="width:250px;height:150px;overflow-y:auto;overflow-x:hidden">
                                            {!! getPriceLists() !!}
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="price-range col-md-12 price-max-ul list-unstyled"
                                            style="width:250px;height:150px;overflow-y:auto;overflow-x:hidden">
                                            {!! getPriceLists() !!}
                                        </ul>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-reset-price"
                                    style="margin:10px;height:35px !important;">{{ __('Reset') }}</button>
                            </div>
                        </div>
                    </div>

                    <a href="#" class="hero-search-card__advanced-bar-trigger area-unit" id="changeAreaUnitlabel"
                        data-toggle="modal" data-target="#area_modal">{{ __('Area Unit') }}</a>
                    <a href="#" class="hero-search-card__advanced-bar-trigger currency" id="changeCurrencylabel"
                        data-toggle="modal" data-target="#currency_modal">{{ __('Change Currency') }}</a>
                </div>

                <div class="advanced-search-content property-advanced-search hero-search-card__advanced-panel"
                    id="propertysearch">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4 bedrooms">
                                <div class="select--arrow">
                                    <select name="bedroom" id="select-bedroom" class="form-control">
                                        <option value="">{{ __('Bedrooms') }}</option>
                                        @for ($i = 1; $i < 5; $i++)
                                            <option value="{{ $i }}"
                                                @if (request()->input('bedroom') == $i) selected @endif>
                                                {{ $i }} {{ $i == 1 ? __('room') : __('rooms') }}</option>
                                        @endfor
                                        <option value="5" @if (request()->input('bedroom') == 5) selected @endif>
                                            {{ __('5+ rooms') }}</option>
                                    </select>
                                    <i class="fas fa-angle-down"></i>
                                </div>
                            </div>
                            <div class="col-md-4 bathrooms">
                                <div class="select--arrow">
                                    <select name="bathroom" id="select-bathroom" class="form-control">
                                        <option value="">{{ __('Bathrooms') }}</option>
                                        @for ($i = 1; $i < 5; $i++)
                                            <option value="{{ $i }}"
                                                @if (request()->input('bathroom') == $i) selected @endif>
                                                {{ $i }} {{ $i == 1 ? __('room') : __('rooms') }}</option>
                                        @endfor
                                        <option value="5" @if (request()->input('bathroom') == 5) selected @endif>
                                            {{ __('5+ rooms') }}</option>
                                    </select>
                                    <i class="fas fa-angle-down"></i>
                                </div>
                            </div>
                            <div class="col-md-4 floors home-floors">
                                <div class="select--arrow">
                                    <select name="floor" id="select-floor" class="form-control">
                                        <option value="">{{ __('Floors') }}</option>
                                        @for ($i = 1; $i < 5; $i++)
                                            <option value="{{ $i }}"
                                                @if (request()->input('floor') == $i) selected @endif>
                                                {{ $i }} {{ $i == 1 ? __('floor') : __('floors') }}</option>
                                        @endfor
                                        <option value="5" @if (request()->input('floor') == 5) selected @endif>
                                            {{ __('5+ floors') }}</option>
                                    </select>
                                    <i class="fas fa-angle-down"></i>
                                </div>
                            </div>
                            <div class="col-md-4 floors commerical-floors d-none">
                                <div class="select--arrow">
                                    <select name="floor" id="select-floor" class="form-control">
                                        <option value="">{{ __('Floors') }}</option>
                                        @for ($i = 1; $i < 5; $i++)
                                            <option value="{{ $i }}"
                                                @if (request()->input('floor') == $i) selected @endif>
                                                {{ $i }} {{ $i == 1 ? __('floor') : __('floors') }}</option>
                                        @endfor
                                        <option value="5" @if (request()->input('floor') == 5) selected @endif>
                                            {{ __('5+ floors') }}</option>
                                    </select>
                                    <i class="fas fa-angle-down"></i>
                                </div>
                            </div>
                            {{-- Plot/commercial price block -- kept for category-based JS toggling.
                                 Shares id="min-max-price-range" with the visible price field above,
                                 same as the original code; only one is ever un-hidden at a time. --}}
                            <div class="col-md-4 plot-price-dp d-none">
                                <div class="price-dropdown">
                                    <div class="dropdown">
                                        <a id="min-max-price-range" class="form-control price-select dropdown-toggle"
                                            href="#" data-toggle="dropdown">{{ __('Price') }}
                                            <span class="currency">{{ CurrentCurrency()->title }}</span>
                                            <strong class="caret"></strong>
                                        </a>
                                        <div class="row price-from-to">
                                            <div class="col-md-4"><span class="min_price_text">0</span></div>
                                            <div class="col-md-1">to</div>
                                            <div class="col-md-4"><span class="max_price_text">Any</span></div>
                                        </div>
                                        <div class="dropdown-menu" style="padding:10px;width:100%">
                                            <div class="row justify-content-center">
                                                <div class="col-6">
                                                    <input class="form-control price-label"
                                                        style="border:1px solid #a0a0a0 !important;" name="min_price"
                                                        placeholder="Min" data-dropdown-id="price-min" />
                                                </div>
                                                <div class="col-6">
                                                    <input class="form-control price-label"
                                                        style="border:1px solid #a0a0a0 !important;" name="max_price"
                                                        placeholder="Max" data-dropdown-id="price-max" />
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="row mt-2 justify-content-center">
                                                <div class="col-md-6">
                                                    <ul class="price-range col-md-12 price-min-ul list-unstyled"
                                                        style="width:250px;height:150px;overflow-y:auto;overflow-x:hidden">
                                                        {!! getPriceLists() !!}
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <ul class="price-range col-md-12 price-max-ul list-unstyled"
                                                        style="width:250px;height:150px;overflow-y:auto;overflow-x:hidden">
                                                        {!! getPriceLists() !!}
                                                    </ul>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-reset-price"
                                                style="margin:10px;height:35px !important;">{{ __('Reset') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Area/unit dropdown -- shares the old "home-price-dp" class name from the
                             original markup (a naming quirk carried over as-is). --}}
                        <div class="row second-row">
                            <div class="col-md-4 home-price-dp">
                                <div class="price-dropdown">
                                    <div class="dropdown">
                                        <a id="min-max-unit-range" class="form-control dropdown-toggle" href="#"
                                            data-toggle="dropdown">{{ __('Area') }}
                                            <span class="currency">({{ getDefaultAreaUnit() }})</span>
                                            <strong class="caret"></strong>
                                        </a>
                                        <div class="row unit-from-to">
                                            <div class="col-md-4"><span class="min_unit_text">0</span></div>
                                            <div class="col-md-2">to</div>
                                            <div class="col-md-4"><span class="max_unit_text">Any</span></div>
                                        </div>
                                        <div class="dropdown-menu" style="padding:10px;width:100%">
                                            <div class="row justify-content-center">
                                                <div class="col-6">
                                                    <input class="form-control" style="border:1px solid #a0a0a0 !important;"
                                                        name="min_unit" placeholder="Min" id="input_min_unit"
                                                        data-dropdown-id="unit-min" />
                                                </div>
                                                <div class="col-6">
                                                    <input class="form-control" style="border:1px solid #a0a0a0 !important;"
                                                        name="max_unit" placeholder="Max" data-dropdown-id="unit-max"
                                                        id="input_max_unit" />
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="row mt-2 justify-content-center">
                                                <div class="col-md-6 unit-list">
                                                    <ul class="units-range col-md-12 unit-min-ul list-unstyled"
                                                        style="width:250px;height:150px;overflow-y:auto;overflow-x:hidden">
                                                        @foreach (getAreaLists() as $unit)
                                                            <li class="unit-li-item" data-value="{{ $unit }}">
                                                                {{ $unit }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <ul class="units-range col-md-12 unit-max-ul list-unstyled"
                                                        style="width:250px;height:150px;overflow-y:auto;overflow-x:hidden">
                                                        @foreach (getAreaLists() as $unit)
                                                            <li class="unit-li-item" data-value="{{ $unit }}">
                                                                {{ $unit }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn btn-reset-unit">{{ __('Reset') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Project (sell/projects tab) advanced content -- kept, unchanged --}}
                <div class="advanced-search-content project-advanced-search hero-search-card__advanced-panel"
                    id="projectysearch">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="price-dropdown">
                                    <div class="dropdown">
                                        <a id="min-max-price-range" class="form-control dropdown-toggle" href="#"
                                            data-toggle="dropdown">{{ __('Price') }}
                                            <span class="currency">{{ CurrentCurrency()->title }}</span>
                                            <strong class="caret"></strong>
                                        </a>
                                        <div class="row price-from-to">
                                            <div class="col-md-4"><span class="min_price_text">0</span></div>
                                            <div class="col-md-2">to</div>
                                            <div class="col-md-4"><span class="max_price_text">Any</span></div>
                                        </div>
                                        <div class="dropdown-menu" style="padding:10px;width:100%">
                                            <div class="row justify-content-center">
                                                <div class="col-6">
                                                    <input class="form-control price-label"
                                                        style="border:1px solid #a0a0a0 !important" name="min_price"
                                                        placeholder="Min" data-dropdown-id="price-min" />
                                                </div>
                                                <div class="col-6">
                                                    <input class="form-control price-label"
                                                        style="border:1px solid #a0a0a0 !important" name="max_price"
                                                        placeholder="Max" data-dropdown-id="price-max" />
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="row mt-2 justify-content-center">
                                                <div class="col-md-6">
                                                    <ul class="price-range col-md-12 price-min-ul list-unstyled"
                                                        style="width:250px;height:150px;overflow-y:auto;overflow-x:hidden">
                                                        {!! getPriceLists() !!}
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <ul class="price-range col-md-12 price-max-ul list-unstyled"
                                                        style="width:250px;height:150px;overflow-y:auto;overflow-x:hidden">
                                                        {!! getPriceLists() !!}
                                                    </ul>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-reset-price"
                                                style="margin:10px;height:35px !important;">{{ __('Reset') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="project-category-search-dropdown" style="display:none">
                        <div class="category-search-list" role="listbox">
                            <div>
                                <div>
                                    <ul class="category-ul" name="Category picker">
                                        <div>
                                            @foreach (app(\Botble\RealEstate\Repositories\Interfaces\CategoryInterface::class)->pluck('re_categories.name', 're_categories.id', ['parent_id' => 0]) as $categoryId => $categoryName)
                                                @if ($loop->index == 0)
                                                    <li class="category-parent-active p-category"
                                                        data-id="{{ $categoryId }}">{{ $categoryName }}</li>
                                                @else
                                                    <li class="category-parent-inactive p-category"
                                                        data-id="{{ $categoryId }}">{{ $categoryName }}</li>
                                                @endif
                                                <div class="p{{ $categoryId }}" style="display:none">
                                                    <div class="row">
                                                        @foreach (app(\Botble\RealEstate\Repositories\Interfaces\CategoryInterface::class)->pluck('re_categories.name', 're_categories.id', ['parent_id' => $categoryId]) as $subcategoryId => $subcategoryName)
                                                            <div class="@if ($loop->count == 1) col-md-12 @else col-md-6 @endif">
                                                                <li class="category-li-item"
                                                                    parent-name="{{ $categoryName }}"
                                                                    data-id="{{ $subcategoryId }}">
                                                                    {{ $subcategoryName }}</li>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="category-li pcateory_data"></div>
                                    </ul>
                                </div>
                            </div>
                            <div class="ab3dd470"></div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="listsuggest"></div>
        </form>
    </div>

    {{-- ============ MODALS (unchanged) ============ --}}
    <div class="modal" id="currency_modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Change Currency') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @php $currencies = get_all_currencies(); @endphp
                    <select class="form-control" id="currency_val">
                        @foreach ($currencies as $currency)
                            <option value="{{ $currency->id }}">{{ $currency->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" style="height:29px;"
                        data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" id="update_currency" class="btn btn-primary btn-sm">{{ __('Save changes') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="area_modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Change Area Unit') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <select class="form-control" id="area_units-val">
                        <option value="m²">Square Meter</option>
                        <option value="ft²" selected>Square Feet</option>
                        <option value="yards">Yards</option>
                        <option value="marla">Marla</option>
                        <option value="kanal">Kanal</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" style="height:29px;"
                        data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" id="update_area" class="btn btn-primary btn-sm">{{ __('Save changes') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="search_map_modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Search Location') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="map-container">
                        <div id="map"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" id="update_search" class="btn btn-primary">{{ __('Go!') }}</button>
                </div>
            </div>
        </div>
    </div>
@endif