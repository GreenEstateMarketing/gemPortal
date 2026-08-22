<?php

namespace Botble\Location\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Location\Http\Requests\CityAreaRequest;
use Botble\Location\Models\CityArea;
use Botble\Location\Repositories\Interfaces\CityAreaInterface;
use Botble\Location\Repositories\Interfaces\CityInterface;
use Botble\Location\Repositories\Interfaces\CountryInterface;
use Botble\Location\Repositories\Interfaces\StateInterface;
class CityAreaForm extends FormAbstract
{
    protected $cityRepository;
protected $countryRepository;
protected $stateRepository;
    public function __construct(
    CountryInterface $countryRepository,
    StateInterface $stateRepository,
    CityInterface $cityRepository
)
{
    parent::__construct();

    $this->countryRepository = $countryRepository;
    $this->stateRepository = $stateRepository;
    $this->cityRepository = $cityRepository;
}

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $countries = $this->countryRepository
    ->pluck('countries.name', 'countries.id');

$states = [
    '' => 'Select State',
];
        $cityAreas = CityArea::where('parent_id', 0)->pluck('city_area.city_area_name', 'city_area.id')->toArray();
        $cityAreas[0] = 'Select Area';
        ksort($cityAreas);

        $this
            ->setupModel(new CityArea)
            ->setValidatorClass(CityAreaRequest::class)
            ->withCustomFields()
            ->add('city_area_name', 'text', [
                'label' => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('country_id', 'customSelect', [
    'label' => 'Country',
    'label_attr' => ['class' => 'control-label required'],
    'attr' => [
    'id' => 'country_id',
    'class' => 'form-control select-search-full',
    'data-type' => 'country',
'data-change-country-url' => url('/ajax/get-states'),],
'choices' => ['' => 'Select Country'] + $countries,
])
->add('state_id', 'customSelect', [
    'label' => 'State',
    'label_attr' => ['class' => 'control-label required'],
   'attr' => [
    'id' => 'state_id',
    'class' => 'form-control select-search-full',
    'data-type' => 'state',
    'data-change-state-url' => url('/ajax/get-cities'),
],
    'choices' => $states,
])
            ->add('city_id', 'customSelect', [
                'label' => 'City',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
    'id' => 'city_id',
    'class' => 'form-control select-search-full',
    'data-type' => 'city',
],
                'choices' => [
    '' => 'Select City',
],
            ])
            ->add('parent_id', 'customSelect', [
                'label' => 'Parent Area',
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'class' => 'form-control select-search-full',
                ],
                'choices' => $cityAreas,
            ])
            ->setBreakFieldPoint('status');
    }
}
