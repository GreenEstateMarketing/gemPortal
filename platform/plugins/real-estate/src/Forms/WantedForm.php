<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Location\Models\City;
use Botble\Location\Models\CityArea;
use Botble\RealEstate\Http\Requests\CategoryRequest;
use Botble\RealEstate\Models\Wanted;
use Botble\RealEstate\Models\Category;
use Throwable;

class WantedForm extends FormAbstract
{

    /**
     * @return mixed|void
     * @throws Throwable
     */
    private $category;
    public function buildForm()
    {
        $category = new Category();
        $category_id = $this->getModel()->category_id;
        $category_list = Category::where('id', $category_id)->get();
        $category_name = $category_list[0]->name;
        $city_id = $this->getModel()->city_id;
        $city_list = City::where('id', $city_id)->get();
        $city_name = $city_list[0]->name;
        $cityArea = CityArea::where('city_id', $city_id)->first();

        $this
            ->setupModel(new Wanted())
            ->withCustomFields()
            ->add('name', 'text', [
                'label' => trans('plugins/real-estate::wanted.customer_name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                    'readonly' => 'readonly'
                ]

            ])
            ->add('email', 'text', [
                'label' => trans('plugins/real-estate::wanted.email'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                    'readonly' => 'readonly'
                ]

            ])
            ->add('mobile_no', 'text', [
                'label' => trans('plugins/real-estate::wanted.mobile_no'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                    'readonly' => 'readonly'
                ]

            ])

            ->add('type', 'text', [
                'label' => trans('plugins/real-estate::wanted.type'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'rows' => 4,
                    'placeholder' => trans('core/base::forms.description_placeholder'),
                    'readonly' => 'readonly'
                ],
            ])
            ->add('category_id', 'text', [
                'label' => trans('plugins/real-estate::wanted.category'),
                'label_attr' => ['class' => 'control-label'],
                'value' => $category_name,
                'attr' => [
                    'readonly' => 'readonly'
                ]
            ])
            ->add('city_id', 'text', [
                'label' => trans('plugins/real-estate::wanted.city'),
                'label_attr' => ['class' => 'control-label'],
                'value' => $city_name,
                'attr' => [
                    'readonly' => 'readonly'
                ]
            ])
            ->add('area', 'text', [
                'label' => 'City Area',
                'label_attr' => ['class' => 'control-label'],
                'value' => $cityArea->city_area_name,
                'attr' => [
                    'readonly' => 'readonly'
                ]
            ])
            ->add('project_name', 'text', [
                'label' => 'Project Name',
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'readonly' => 'readonly'
                ]
            ])
            ->add('amount', 'text', [
                'label' => 'Amount',
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'readonly' => 'readonly'
                ]
            ])
            ->add('comments', 'textarea', [
                'label' => trans('plugins/real-estate::wanted.comments'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'rows' => 4,
                    'placeholder' => trans('plugins/real-estate::wanted.comments'),
                    'readonly' => 'readonly'
                ],
            ]);
    }

    public function getActionButtons(): string
    {
        return ''; // Return an empty string to remove the buttons
    }
}
