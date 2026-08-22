<div class="shop__sort">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group--inline">
                <label for="per-page">{{ __('Per page') }}</label>
                <div class="form-group__content">
                    <div class="select--arrow">
                        <select name="per_page" id="per-page" class="form-control">
                            <option value="12" @if (request()->input('per_page') == 12) selected @endif>12</option>
                            <option value="24" @if (request()->input('per_page') == 24) selected @endif>24</option>
                            <option value="32" @if (request()->input('per_page') == 32) selected @endif>32</option>
                            <option value="64" @if (request()->input('per_page') == 64) selected @endif>64</option>
                            <option value="120" @if (request()->input('per_page') == 120) selected @endif>120</option>
                        </select>
                        <i class="fas fa-angle-down"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group--inline float-right text-right">
                <label for="sort-by">{{ __('Sort by') }}</label>
                <div class="form-group__content">
                    <div class="select--arrow">
                        <select name="sort_by" id="sort-by" class="form-control">
                            <option value="default_sorting" @if (request()->input('sort_by') == 'default_sorting') selected @endif>{{ __('Default') }}</option>
                            <option value="date_asc" @if (request()->input('sort_by') == 'date_asc') selected @endif>{{ __('Oldest') }}</option>
                            <option value="date_desc" @if (request()->input('sort_by') == 'date_desc') selected @endif>{{ __('Newest') }}</option>
                            <option value="price_asc" @if (request()->input('sort_by') == 'price_asc') selected @endif>{{ __('Price') }}: {{ __('low to high') }}</option>
                            <option value="price_desc" @if (request()->input('sort_by') == 'price_desc') selected @endif>{{ __('Price') }}: {{ __('high to low') }}</option>
                            <option value="name_asc" @if (request()->input('sort_by') == 'name_asc') selected @endif>{{ __('Name') }}: {{ __('A-Z') }}</option>
                            <option value="name_desc" @if (request()->input('sort_by') == 'name_desc') selected @endif>{{ __('Name') }}: {{ __('Z-A') }}</option>
                        </select>
                        <i class="fas fa-angle-down"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
