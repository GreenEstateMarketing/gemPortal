<?php

namespace Botble\RealEstate\Forms;

use Assets;
use Botble\Base\Forms\FormAbstract;
use Botble\RealEstate\Http\Requests\TemplateRequest;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Models\Template;
use Botble\RealEstate\Repositories\Interfaces\TemplateInterface;

class TemplateForm extends FormAbstract
{
    protected $templateRepo;

    public function __construct(TemplateInterface $templateRepo)
    {
        parent::__construct();
        $this->templateRepo = $templateRepo;
    }

    public function buildForm()
    {
        $statuses = [
            '1' => 'Published',
            '0' => 'Not Published'
        ];

        $types = [
            'Property' => 'Property'
        ];

        $categories = Category::where('parent_id', '!=', '0')->pluck('re_categories.name', 're_categories.id')->toArray();

        Assets::addScripts(['input-mask']);

        $this
            ->setupModel(new Template())
            ->setValidatorClass(TemplateRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label' => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])->add('detail', 'textarea', [
                'label' => 'Detail',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Enter detail'
                ],
            ])->add('type', 'customSelect', [
                'label' => 'Type',
                'label_attr' => ['class' => 'control-label required'],
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => $types,
            ])->add('status', 'customSelect', [
                'label' => 'Status',
                'label_attr' => ['class' => 'control-label required'],
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => $statuses,
            ])->add('category_id', 'customSelect', [
                'label' => 'Category',
                'label_attr' => ['class' => 'control-label required'],
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => $categories,
            ]);
    }
}