@extends('core/base::layouts.master')
@section('content')
    {!! Form::open(['url' => route('real-estate.settings'), 'class' => 'main-setting-form']) !!}
    <div class="max-width-1200">

        <div class="flexbox-annotated-section">
            <div class="flexbox-annotated-section-annotation">
                <div class="annotated-section-title pd-all-20">
                        <h2>{{ trans('plugins/real-estate::settings.general') }}</h2>
                </div>
                <div class="annotated-section-description pd-all-20 p-none-t">
                    <p class="color-note">{{ trans('plugins/real-estate::settings.general_description') }}</p>
                </div>
            </div>
            <div class="flexbox-annotated-section-content">
                <div class="wrapper-content pd-all-20">
                    <div class="form-group">
                        <label class="text-title-field" for="real_estate_square_unit">{{ trans('plugins/real-estate::settings.square_unit') }}</label>
                        <div class="ui-select-wrapper">
                            <select class="ui-select" name="real_estate_square_unit" id="real_estate_square_unit">
                                <option value="" @if (setting('real_estate_square_unit', 'm²') == null) selected @endif>{{ trans('plugins/real-estate::settings.square_unit_none') }}</option>
                                <option value="m²" @if (setting('real_estate_square_unit', 'm²') === 'm²') selected @endif>{{ trans('plugins/real-estate::settings.square_unit_meter') }}</option>
                                <option value="ft²" @if (setting('real_estate_square_unit', 'm²') === 'ft²') selected @endif>{{ trans('plugins/real-estate::settings.square_unit_feet') }}</option>
                                <option value="marla" @if (setting('real_estate_square_unit', 'm²') === 'marla') selected @endif>{{ trans('plugins/real-estate::settings.square_unit_marla') }}</option>
                                <option value="yards" @if (setting('real_estate_square_unit', 'm²') === 'yards') selected @endif>{{ trans('plugins/real-estate::settings.square_unit_yard') }}</option>
                                <option value="kanal" @if (setting('real_estate_square_unit', 'm²') === 'kanal') selected @endif>{{ trans('plugins/real-estate::settings.square_unit_kanal') }}</option>

                            </select>
                            <svg class="svg-next-icon svg-next-icon-size-16">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Aera Unit Conversion  e.g:squre feet to marla-->
        <div class="flexbox-annotated-section">
            <div class="flexbox-annotated-section-annotation">
                <div class="annotated-section-title pd-all-20">
                    <h2>{{ trans('plugins/real-estate::settings.area_unit') }}</h2>
                </div>
                <div class="annotated-section-description pd-all-20 p-none-t">
                    <p class="color-note">{{ trans('plugins/real-estate::settings.area_unit_description') }}</p>
                </div>
            </div>
            <div class="flexbox-annotated-section-content">
                <div class="wrapper-content pd-all-20">
                    <div class="form-group">
                        <label class="text-title-field" for="real_estate_sqaure_feet_unit_to_marla">{{ trans('plugins/real-estate::settings.real_estate_sqaure_feet_unit_to_marla') }}</label>
                        <div class="ui-select-wrapper">
                            <input type="number" class="form-control" name="real_estate_sqaure_feet_unit_to_marla" value="{{ setting('real_estate_sqaure_feet_unit_to_marla') }}" id="real_estate_sqaure_feet_unit_to_marla" placeholder="225 sq.ft=1 Marla" step='0.01'>

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="text-title-field" for="real_estate_sqaure_feet_unit_to_marla">{{ trans('plugins/real-estate::settings.real_estate_sqaure_feet_unit_to_square_meter') }}</label>
                        <div class="ui-select-wrapper">
                            <input type="number" class="form-control" name="real_estate_sqaure_feet_unit_to_square_meter" value="{{ setting('real_estate_sqaure_feet_unit_to_square_meter') }}" id="real_estate_sqaure_feet_unit_to_square_meter" placeholder="sq.ft/10.764=1 square meter" step='0.001'>

                        </div>
                    </div>
                   <!-- <div class="form-group">
                        <label class="text-title-field" for="real_estate_square_unit_to_marla">{{ trans('plugins/real-estate::settings.real_estate_square_unit_to_marla') }}</label>
                        <div class="ui-select-wrapper">
                            <input type="number" class="form-control" name="real_estate_square_unit_to_marla" value="{{ setting('real_estate_square_unit_to_marla') }}" id="real_estate_square_unit_to_marla" placeholder="20.90 sq(m)2=1 Marla" step='0.01'>

                        </div>
                    </div>-->
                </div>

            </div>
        </div>
        <!-- Aera Unit calculation on property add page  e.g:marla to square-->
        <div class="flexbox-annotated-section">
            <div class="flexbox-annotated-section-annotation">
                <div class="annotated-section-title pd-all-20">
                    <h2>{{ trans('plugins/real-estate::settings.area_unit_calculation') }}</h2>
                </div>
                <div class="annotated-section-description pd-all-20 p-none-t">
                    <p class="color-note">{{ trans('plugins/real-estate::settings.area_unit_calculation_description') }}</p>
                </div>
            </div>
            <div class="flexbox-annotated-section-content">
                <div class="wrapper-content pd-all-20">
                    <div class="form-group">
                        <label class="text-title-field" for="real_estate_marla_to_square_ft">{{ trans('plugins/real-estate::settings.real_estate_marla_to_square_ft') }}</label>
                        <div class="ui-select-wrapper">
                            <input type="number" class="form-control" name="real_estate_marla_to_square_ft" value="{{ setting('real_estate_marla_to_square_ft') }}" id="real_estate_marla_to_square_ft" placeholder="1 Marla=225 Square Feet" step='0.01'>

                        </div>

                    </div>
                    <div class="form-group">
                        <label class="text-title-field" for="real_estate_square_meter_to_sq_ft">{{ trans('plugins/real-estate::settings.real_estate_square_meter_to_sq_ft') }}</label>
                        <div class="ui-select-wrapper">
                            <input type="number" class="form-control" name="real_estate_square_meter_to_sq_ft" value="{{ setting('real_estate_square_meter_to_sq_ft') }}" id="real_estate_square_meter_to_sq_ft" placeholder="Square Meter=10.764 Square Feet" step='0.001'>

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="text-title-field" for="real_estate_yards_to_sq_ft">{{ trans('plugins/real-estate::settings.real_estate_yards_to_sq_ft') }}</label>
                        <div class="ui-select-wrapper">
                            <input type="number" class="form-control" name="real_estate_yards_to_sq_ft" value="{{ setting('real_estate_yards_to_sq_ft') }}" id="real_estate_yards_to_sq_ft" placeholder="9" step='0.001'>

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="text-title-field" for="real_estate_kanal_to_sq_ft">{{ trans('plugins/real-estate::settings.real_estate_kanal_to_sq_ft') }}</label>
                        <div class="ui-select-wrapper">
                            <input type="number" class="form-control" name="real_estate_kanal_to_sq_ft" value="{{ setting('real_estate_kanal_to_sq_ft') }}" id="real_estate_kanal_to_sq_ft" placeholder="4500" step='0.001'>

                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="flexbox-annotated-section">
            <div class="flexbox-annotated-section-annotation">
                <div class="annotated-section-title pd-all-20">
                    <h2>{{ trans('plugins/real-estate::currency.currencies') }}</h2>
                </div>
                <div class="annotated-section-description pd-all-20 p-none-t">
                    <p class="color-note">{{ trans('plugins/real-estate::currency.setting_description') }}</p>
                </div>
            </div>
            <div class="flexbox-annotated-section-content">
                <div class="wrapper-content pd-all-20">
                    <div class="form-group">
                        <label class="text-title-field"
                               for="real_estate_convert_money_to_text_enabled">{{ trans('plugins/real-estate::settings.real_estate_convert_money_to_text_enabled') }}
                        </label>
                        <label class="hrv-label">
                            <input type="radio" name="real_estate_convert_money_to_text_enabled" class="hrv-radio"
                                   value="1"
                                   @if (setting('real_estate_convert_money_to_text_enabled', config('plugins.real-estate.real-estate.display_big_money_in_million_billion')) == 1) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                        </label>
                        <label class="hrv-label">
                            <input type="radio" name="real_estate_convert_money_to_text_enabled" class="hrv-radio"
                                   value="0"
                                   @if (setting('real_estate_convert_money_to_text_enabled', config('plugins.real-estate.real-estate.display_big_money_in_million_billion')) == 0) checked @endif>{{ trans('core/setting::setting.general.no') }}
                        </label>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="text-title-field" for="real_estate_thousands_separator">{{ trans('plugins/real-estate::settings.thousands_separator') }}</label>
                            <div class="ui-select-wrapper">
                                <select class="ui-select" name="real_estate_thousands_separator" id="real_estate_thousands_separator">
                                    <option value="," @if (setting('real_estate_thousands_separator', ',') == ',') selected @endif>{{ trans('plugins/real-estate::settings.separator_comma') }}</option>
                                    <option value="." @if (setting('real_estate_thousands_separator', ',') == '.') selected @endif>{{ trans('plugins/real-estate::settings.separator_period') }}</option>
                                    <option value=" " @if (setting('real_estate_thousands_separator', ',') == ' ') selected @endif>{{ trans('plugins/real-estate::settings.separator_space') }}</option>
                                </select>
                                <svg class="svg-next-icon svg-next-icon-size-16">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                                </svg>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-title-field" for="real_estate_decimal_separator">{{ trans('plugins/real-estate::settings.decimal_separator') }}</label>
                            <div class="ui-select-wrapper">
                                <select class="ui-select" name="real_estate_decimal_separator" id="real_estate_decimal_separator">
                                    <option value="." @if (setting('real_estate_decimal_separator', '.') == '.') selected @endif>{{ trans('plugins/real-estate::settings.separator_period') }}</option>
                                    <option value="," @if (setting('real_estate_decimal_separator', '.') == ',') selected @endif>{{ trans('plugins/real-estate::settings.separator_comma') }}</option>
                                    <option value=" " @if (setting('real_estate_decimal_separator', '.') == ' ') selected @endif>{{ trans('plugins/real-estate::settings.separator_space') }}</option>
                                </select>
                                <svg class="svg-next-icon svg-next-icon-size-16">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                                </svg>
                            </div>
                        </div>
                    </div>

                <textarea name="currencies"
                          id="currencies"
                          class="hidden">{!! json_encode($currencies) !!}</textarea>
                    <textarea name="deleted_currencies"
                              id="deleted_currencies"
                              class="hidden"></textarea>
                    <div class="swatches-container">
                        <div class="header clearfix">
                            <div class="swatch-item">
                                {{ trans('plugins/real-estate::currency.name') }}
                            </div>
                            <div class="swatch-item">
                                {{ trans('plugins/real-estate::currency.symbol') }}
                            </div>
                            <div class="swatch-item swatch-decimals">
                                {{ trans('plugins/real-estate::currency.number_of_decimals') }}
                            </div>
                            <div class="swatch-item swatch-exchange-rate">
                                {{ trans('plugins/real-estate::currency.exchange_rate') }}
                            </div>
                            <div class="swatch-item swatch-is-prefix-symbol">
                                {{ trans('plugins/real-estate::currency.is_prefix_symbol') }}
                            </div>
                            <div class="swatch-is-default">
                                {{ trans('plugins/real-estate::currency.is_default') }}
                            </div>
                            <div class="remove-item">{{ trans('plugins/real-estate::currency.remove') }}</div>
                        </div>
                        <ul class="swatches-list">

                        </ul>
                        <a href="#" class="js-add-new-attribute">
                            {{ trans('plugins/real-estate::currency.new_currency') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if (!app()->environment('demo'))
            <div class="flexbox-annotated-section">
                <div class="flexbox-annotated-section-annotation">
                    <div class="annotated-section-title pd-all-20">
                        <h2>{{ trans('plugins/real-estate::real-estate.google_map') }}</h2>
                    </div>
                    <div class="annotated-section-description pd-all-20 p-none-t">
                        <p class="color-note">{{ trans('plugins/real-estate::real-estate.google_map_description') }}</p>
                    </div>
                </div>
                <div class="flexbox-annotated-section-content">
                    <div class="wrapper-content pd-all-20">
                        <div class="form-group">
                            <label class="text-title-field" for="google_map_api_key">{{ trans('plugins/real-estate::real-estate.api_key') }}</label>
                            <input type="text" class="form-control" name="google_map_api_key" value="{{ setting('google_map_api_key') }}" id="google_map_api_key" placeholder="AIzaSyAvS1cTtst2cOnxxxxxxxxxxxxx">
                            <span class="help-ts">{{ trans('plugins/real-estate::real-estate.api_key_helper') }} (<a href="https://console.developers.google.com/apis/dashboard" target="_blank">https://console.developers.google.com/apis/dashboard</a>)</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="flexbox-annotated-section">
            <div class="flexbox-annotated-section-annotation">
                <div class="annotated-section-title pd-all-20">
                    <h2>{{ trans('plugins/real-estate::settings.title') }}</h2>
                </div>
                <div class="annotated-section-description pd-all-20 p-none-t">
                    <p class="color-note">{{ trans('plugins/real-estate::settings.description') }}</p>
                </div>
            </div>

            <div class="flexbox-annotated-section-content">
                <div class="wrapper-content pd-all-20">
                    <div class="form-group">
                        <label class="text-title-field"
                               for="verify_account_email">{{ trans('plugins/real-estate::settings.verify_account_email') }}
                        </label>
                        <div class="ui-select-wrapper">
                            <select name="verify_account_email" class="ui-select" id="verify_account_email">
                                <option value="1" @if (setting('verify_account_email', config('plugins.real-estate.real-estate.verify_email')) == 1) selected @endif>{{ trans('core/base::base.yes') }}</option>
                                <option value="0" @if (setting('verify_account_email', config('plugins.real-estate.real-estate.verify_email')) == 0) selected @endif>{{ trans('core/base::base.no') }}</option>
                            </select>
                            <svg class="svg-next-icon svg-next-icon-size-16">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="flexbox-annotated-section" style="border: none">
            <div class="flexbox-annotated-section-annotation">
                &nbsp;
            </div>
            <div class="flexbox-annotated-section-content">
                <button class="btn btn-info" type="submit">{{ trans('plugins/real-estate::currency.save_settings') }}</button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@push('footer')
    <script id="currency_template" type="text/x-custom-template">
        <li data-id="__id__" class="clearfix">
            <div class="swatch-item" data-type="title">
                <input type="text" class="form-control" value="__title__">
            </div>
            <div class="swatch-item" data-type="symbol">
                <input type="text" class="form-control" value="__symbol__">
            </div>
            <div class="swatch-item swatch-decimals" data-type="decimals">
                <input type="number" class="form-control" value="__decimals__">
            </div>
            <div class="swatch-item swatch-exchange-rate" data-type="exchange_rate">
                <input type="number" class="form-control" value="__exchangeRate__" step="0.00000001">
            </div>
            <div class="swatch-item swatch-is-prefix-symbol" data-type="is_prefix_symbol">
                <div class="ui-select-wrapper">
                    <select class="ui-select">
                        <option value="1" __isPrefixSymbolChecked__>{{ trans('plugins/real-estate::currency.before_number') }}</option>
                        <option value="0" __notIsPrefixSymbolChecked__>{{ trans('plugins/real-estate::currency.after_number') }}</option>
                    </select>
                    <svg class="svg-next-icon svg-next-icon-size-16">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                    </svg>
                </div>
            </div>
            <div class="swatch-is-default" data-type="is_default">
                <input type="radio" name="currencies_is_default" value="__position__" __isDefaultChecked__>
            </div>
            <div class="remove-item"><a href="#" class="font-red"><i class="fa fa-trash"></i></a></div>
        </li>
    </script>
@endpush
