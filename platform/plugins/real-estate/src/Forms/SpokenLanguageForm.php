<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\FormAbstract;
use Botble\RealEstate\Http\Requests\SpokenLanguageRequest;
use Botble\RealEstate\Models\SpokenLanguage;
use Throwable;

class SpokenLanguageForm extends FormAbstract
{
    /**
     * @return mixed|void
     * @throws Throwable
     */
    public function buildForm()
    {
        $this
            ->setupModel(new SpokenLanguage)
            ->setValidatorClass(SpokenLanguageRequest::class)
            ->add('name', 'text', [
                'label' => trans('plugins/real-estate::spoken-language.form.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::spoken-language.form.name'),
                    'data-counter' => 120,
                ],
            ])
            ->add('order', 'number', [
                'label' => trans('core/base::forms.order'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'placeholder' => trans('core/base::forms.order_by_placeholder'),
                ],
                'default_value' => 0,
            ])
            ->add('status', 'customSelect', [
                'label' => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => BaseStatusEnum::labels(),
            ])
            ->setBreakFieldPoint('status');
    }
}
