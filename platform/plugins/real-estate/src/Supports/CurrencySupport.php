<?php

namespace Botble\RealEstate\Supports;

use Botble\RealEstate\Models\Currency;
use Botble\RealEstate\Repositories\Interfaces\CurrencyInterface;

class CurrencySupport
{
    /**
     * @var Currency
     */
    protected $currency;

    /**
     * @param Currency $currency
     */
    public function setApplicationCurrency(Currency $currency)
    {
        $this->currency = $currency;

        if (session('currency') == $currency->title) {
            return;
        }
        session(['currency' => $currency->title]);
    }

    /**
     * @return Currency
     */
    public function getApplicationCurrency()
    {
        $currency = $this->currency;

        if (empty($currency)) {
            if (session('currency')) {
                $currency = app(CurrencyInterface::class)->getFirstBy(['title' => session('currency')]);
            }

            if (!$currency) {
                $currency = app(CurrencyInterface::class)->getFirstBy(['is_default' => 1]);
            }

            if (!$currency) {
                $currency = new Currency;
            }

            $this->currency = $currency;
        }

        return $currency;
    }
}
