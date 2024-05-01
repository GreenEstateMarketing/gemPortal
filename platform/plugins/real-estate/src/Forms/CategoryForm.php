<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\RealEstate\Http\Requests\CategoryRequest;
use Botble\RealEstate\Models\Category;
use Throwable;

class CategoryForm extends FormAbstract
{

    /**
     * @return mixed|void
     * @throws Throwable
     */
    private $category;
    public function buildForm()
    {
        $category=new Category();
        $choices=$category->select(['id','name'])->where('parent_id',0)->pluck('name','id')->toArray();
       // print_r($choices);exit;
        $this
            ->setupModel(new Category)
            ->setValidatorClass(CategoryRequest::class)
            ->withCustomFields()
            ->add('parent_id_check', 'onOff', [
                'label'         => trans('core/base::forms.parent_id_check'),
                'label_attr'    => ['class' => 'control-label'],
                'default_value' => false,

            ])
            ->add('parent_id', 'customSelect', [
                'label'      => trans('core/base::forms.parent_category'),
                'label_attr' => ['class' => 'control-label required parentCategory'],
                'attr'       => [
                   'placeholder'  => trans('core/base::forms.category_placeholder'),
                    'data-counter' => 120,
                    'class'=>'parentCategory'
                ],
                'choices'    =>$choices
            ])
           ->add('name', 'text', [
                'label'      => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ]

            ])
           ->add('description', 'textarea', [
                'label'      => trans('core/base::forms.description'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'rows'         => 4,
                    'placeholder'  => trans('core/base::forms.description_placeholder'),
                    'data-counter' => 350,
                ],
            ])
            ->add('order', 'number', [
                'label'         => trans('core/base::forms.order'),
                'label_attr'    => ['class' => 'control-label'],
                'attr'          => [
                    'placeholder' => trans('core/base::forms.order_by_placeholder'),
                ],
                'default_value' => 0,
            ])
            ->add('is_default', 'onOff', [
                'label'         => trans('core/base::forms.is_default'),
                'label_attr'    => ['class' => 'control-label'],
                'default_value' => false,
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
