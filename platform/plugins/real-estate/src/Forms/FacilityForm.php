<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Enums\BaseStatusEnum;
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
                'choices'    => [
                    ''                 => trans('plugins/real-estate::facility.form.google_place_type_not_mapped'),
                    'hospital'         => 'Hospital',
                    'supermarket'      => 'Supermarket',
                    'school'           => 'School',
                    'pharmacy'         => 'Pharmacy',
                    'airport'          => 'Airport',
                    'train_station'    => 'Train Station',
                    'bus_station'      => 'Bus Station',
                    'subway_station'   => 'Subway Station',
                    'shopping_mall'    => 'Shopping Mall',
                    'bank'             => 'Bank',
                    'atm'              => 'ATM',
                    'restaurant'       => 'Restaurant',
                    'park'             => 'Park',
                    'gym'              => 'Gym',
                    'university'       => 'University',
                    'library'          => 'Library',
                    'place_of_worship' => 'Place of Worship',
                    'gas_station'      => 'Gas Station',
                    'parking'          => 'Parking',
                    'lodging'          => 'Lodging',
                    'post_office'      => 'Post Office',
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
