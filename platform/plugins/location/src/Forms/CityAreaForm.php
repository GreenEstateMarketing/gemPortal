<?php

namespace Botble\Location\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Location\Http\Requests\CityAreaRequest;
use Botble\Location\Models\CityArea;
use Botble\Location\Repositories\Interfaces\CityAreaInterface;
use Botble\Location\Repositories\Interfaces\CityInterface;

class CityAreaForm extends FormAbstract
{
    protected $cityRepository;

    public function __construct(CityInterface $cityRepository)
    {
        parent::__construct();

        $this->cityRepository = $cityRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $cities = $this->cityRepository->pluck('cities.name', 'cities.id');
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
            ->add('city_id', 'customSelect', [
                'label' => 'City',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'id' => 'city_id',
                    'class' => 'form-control select-search-full',
                ],
                'choices' => $cities,
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
