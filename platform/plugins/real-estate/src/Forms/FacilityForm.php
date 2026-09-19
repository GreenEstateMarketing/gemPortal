<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\RealEstate\Enums\GooglePlaceTypeEnum;
use Botble\RealEstate\Forms\Fields\FontawesomeSelectField;
use Botble\RealEstate\Http\Requests\FacilityRequest;
use Botble\RealEstate\Models\Facility;

class FacilityForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $this
            ->setupModel(new Facility)
            ->setValidatorClass(FacilityRequest::class)
            ->addCustomField('fontawesomeSelect', FontawesomeSelectField::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label'      => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('icon', 'fontawesomeSelect', [
                'label'         => trans('plugins/real-estate::feature.form.icon'),
                'label_attr'    => ['class' => 'control-label'],
                'attr'          => [
                    'placeholder'  => trans('plugins/real-estate::feature.form.icon'),
                    'data-counter' => 60,
                ],
                'default_value' => 'fas fa-check',
            ])
            ->add('google_place_type', 'customSelect', [
                'label'      => trans('plugins/real-estate::facility.form.google_place_type'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class' => 'form-control select-full',
                ],
                'choices'    => ['' => trans('plugins/real-estate::facility.form.google_place_type_none')] + GooglePlaceTypeEnum::labels(),
                'help_block' => [
                    'text' => trans('plugins/real-estate::facility.form.google_place_type_helper'),
                ],
            ])
            ->add('google_place_keyword', 'text', [
                'label'      => trans('plugins/real-estate::facility.form.google_place_keyword'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => trans('plugins/real-estate::facility.form.google_place_keyword_placeholder'),
                    'data-counter' => 120,
                ],
                'help_block' => [
                    'text' => trans('plugins/real-estate::facility.form.google_place_keyword_helper'),
                ],
            ])
            ->add('google_place_radius', 'number', [
                'label'      => trans('plugins/real-estate::facility.form.google_place_radius'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder' => trans('plugins/real-estate::facility.form.google_place_radius_placeholder'),
                    'min'         => 100,
                    'max'         => 50000,
                ],
            ])
            ->add('status', 'customSelect', [
                'label'      => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'class' => 'form-control select-full',
                ],
                'choices'    => BaseStatusEnum::labels(),
            ])
            ->setBreakFieldPoint('status');
    }
}
