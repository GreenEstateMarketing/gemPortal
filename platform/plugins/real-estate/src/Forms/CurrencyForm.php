<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\RealEstate\Http\Requests\CurrencyRequest;
use Botble\RealEstate\Models\Currency;
use Assets;

class CurrencyForm extends FormAbstract
{

    public function __construct()
    {
        parent::__construct();
    }

    public function buildForm()
    {
        Assets::addScripts(['input-mask']);

        $this
            ->setupModel(new Currency)
            ->setValidatorClass(CurrencyRequest::class)
            ->withCustomFields()
            ->add('title', 'text', [
                'label' => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('symbol', 'text', [
                'label' => trans('plugins/real-estate::currency.symbol'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::currency.symbol'),
                    'data-counter' => 120,
                ],
            ])
            ->add('is_prefix_symbol', 'onOff', [
                'label' => trans('plugins/real-estate::currency.is_prefix_symbol'),
                'label_attr' => ['class' => 'control-label'],
                'default_value' => false
            ])
            ->add('decimals', 'number', [
                'label' => trans('plugins/real-estate::currency.decimals'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::currency.decimals'),
                    'data-counter' => 120,
                ],
            ])
            ->add('is_default', 'onOff', [
                'label' => trans('plugins/real-estate::currency.is_default'),
                'label_attr' => ['class' => 'control-label'],
                'default_value' => false
            ])
            ->add('exchange_rate', 'number', [
                'label' => trans('plugins/real-estate::currency.exchange_rate'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::currency.exchange_rate'),
                    'data-counter' => 120,
                ],
            ]);
    }
}