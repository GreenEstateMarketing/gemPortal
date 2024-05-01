<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Location\Models\City;
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
        $category=new Category();
        $category_id=$this->getModel()->category_id;
        $category_list=Category::where('id',$category_id)->get();
        $category_name=$category_list[0]->name;
        $city_id=$this->getModel()->city_id;
        $city_list=City::where('id',$city_id)->get();
        $city_name=$city_list[0]->name;
        $this
            ->setupModel(new Wanted())
          /*  ->setValidatorClass(CategoryRequest::class)*/
            ->withCustomFields()
            ->add('name', 'text', [
                'label'      => trans('plugins/real-estate::wanted.customer_name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ]

            ])
            ->add('email', 'text', [
                'label'      => trans('plugins/real-estate::wanted.email'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ]

            ])
            ->add('mobile_no', 'text', [
                'label'      => trans('plugins/real-estate::wanted.mobile_no'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ]

            ])

           ->add('type', 'text', [
                'label'      => trans('plugins/real-estate::wanted.type'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'rows'         => 4,
                    'placeholder'  => trans('core/base::forms.description_placeholder'),
                    'data-counter' => 350,
                ],
            ])
            ->add('category_id', 'text', [
                'label'         =>  trans('plugins/real-estate::wanted.category'),
                'label_attr'    => ['class' => 'control-label'],
                 'default_value' => 0,
                'value'=>$category_name
            ])
            ->add('city_id', 'text', [
                'label'         =>  trans('plugins/real-estate::wanted.city'),
                'label_attr'    => ['class' => 'control-label'],
                'default_value' => 0,
                'value'=>$city_name
            ])
            ->add('area', 'text', [
                'label'         =>  trans('plugins/real-estate::wanted.city_area'),
                'label_attr'    => ['class' => 'control-label'],
                'default_value' => 0,
            ])
            ->add('comments', 'text', [
                'label'         =>  trans('plugins/real-estate::wanted.comments'),
                'label_attr'    => ['class' => 'control-label'],
                'default_value' => 0,
            ]);
            /*->add('status', 'customSelect', [
                'label'      => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'class' => 'form-control select-full',
                ],
                'choices'    => BaseStatusEnum::labels(),
            ])
            ->setBreakFieldPoint('status');*/
    }
}
