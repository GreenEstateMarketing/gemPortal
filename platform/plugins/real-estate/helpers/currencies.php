<?php

use Botble\RealEstate\Facades\CurrencyFacade;
use Botble\RealEstate\Repositories\Interfaces\CurrencyInterface;
use Botble\RealEstate\Models\Currency;
use Botble\RealEstate\Supports\CurrencySupport;
use Illuminate\Support\Collection;

if (!function_exists('format_price')) {
    /**
     * @param int $price
     * @param Currency|null $currency
     * @param bool $withoutCurrency
     * @param bool $useSymbol
     * @return string
     */
    function format_price($price, $currency = null, $withoutCurrency = false, $useSymbol = true): string
    {
        if (!$currency) {
            $currency = get_application_currency();
        } elseif ($currency != null && !($currency instanceof Currency)) {
            $currency = app(CurrencyInterface::class)->getFirstBy(['re_currencies.id' => $currency]);
        }

        if (!$currency) {
            return human_price_text($price, $currency);
        }

        if ($currency->id != get_application_currency_id() && $currency->exchange_rate > 0) {
            $currentCurrency = get_application_currency();

            if ($currentCurrency->is_default) {
                $price = $price / $currency->exchange_rate;
            } else {
                $price = $price * $currentCurrency->exchange_rate;
            }

            $currency = $currentCurrency;
        }

        if ($useSymbol && $currency->is_prefix_symbol) {
            return $currency->symbol . human_price_text($price, $currency);
        }

        if ($withoutCurrency) {
            return human_price_text($price, $currency);
        }

        return human_price_text($price, $currency, ($useSymbol ? $currency->symbol : $currency->title));
    }
}

if (!function_exists('human_price_text')) {
    /**
     * @param int $price
     * @param Currency|null $currency
     * @param string $priceUnit
     * @return string
     */
    function human_price_text($price, $currency, $priceUnit = ''): string
    {
        $numberAfterDot = ($currency instanceof Currency) ? $currency->decimals : 0;

        if (setting('real_estate_convert_money_to_text_enabled', config('plugins.real-estate.real-estate.display_big_money_in_million_billion'))) {
            if ($price >= 1000000 && $price < 1000000000) {
                $price = round($price / 1000000, 2);
                $numberAfterDot = 2;
                $priceUnit = __('million') . ' ' . $priceUnit;
            } elseif ($price >= 1000000000) {
                $price = round($price / 1000000000, 2);
                $numberAfterDot = 2;
                $priceUnit = __('billion') . ' ' . $priceUnit;
            }
        }

        if (is_numeric($price)) {
            $price = preg_replace('/[^0-9,.]/s', '', $price);
        }

        $price = number_format($price, $numberAfterDot, setting('real_estate_decimal_separator', '.'), setting('real_estate_thousands_separator', ','));

        return $price . ($priceUnit ? ' ' . $priceUnit : '');
    }
}

if (!function_exists('get_current_exchange_rate')) {
    /**
     * @param null $currency
     */
    function get_current_exchange_rate($currency = null)
    {
        if (!$currency) {
            $currency = get_application_currency();
        } elseif ($currency != null && !($currency instanceof Currency)) {
            $currency = app(CurrencyInterface::class)->getFirstBy(['ec_currencies.id' => $currency]);
        }

        if (!$currency->is_default && $currency->exchange_rate > 0) {
            return $currency->exchange_rate;
        }

        return 1;
    }
}

if (!function_exists('cms_currency')) {
    /**
     * @return CurrencySupport
     */
    function cms_currency()
    {
        return CurrencyFacade::getFacadeRoot();
    }
}

if (!function_exists('get_all_currencies')) {
    /**
     * @return Collection
     */
    function get_all_currencies()
    {
        return app(CurrencyInterface::class)->getAllCurrencies();
    }
}

if (!function_exists('get_application_currency')) {
    /**
     * @return Currency|null
     */
    function get_application_currency()
    {
        return cms_currency()->getApplicationCurrency();
    }
}

if (!function_exists('get_application_currency_id')) {
    /**
     * @return int|null
     */
    function get_application_currency_id()
    {
        $currency = cms_currency()->getApplicationCurrency();

        return $currency ? $currency->id : null;
    }
}
