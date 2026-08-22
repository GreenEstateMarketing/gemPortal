<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\RealEstate\Http\Requests\CategoryDocumentRequest;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Models\CategoryDocument;
use Botble\RealEstate\Repositories\Interfaces\CategoryDocumentInterface;
use Botble\RealEstate\Repositories\Interfaces\CategoryInterface;
use Botble\RealEstate\Repositories\Interfaces\DocumentInterface;
use Assets;

class CategoryDocumentForm extends FormAbstract
{
    protected $categoryDocumentRepo;

    protected $documentRepo;

    protected $categoryRepo;

    public function __construct(
        CategoryDocumentInterface $categoryDocumentRepo,
        DocumentInterface         $documentRepo,
        CategoryInterface         $categoryRepo
    )
    {
        parent::__construct();
        $this->categoryDocumentRepo = $categoryDocumentRepo;
        $this->documentRepo = $documentRepo;
        $this->categoryRepo = $categoryRepo;
    }

    public function buildForm()
    {
        Assets::addScripts(['input-mask']);

        $documents = $this->documentRepo->pluck('documents.name', 'documents.id');
        $categories = Category::where('parent_id', '!=', '0')->pluck('re_categories.name', 're_categories.id')->toArray();

        $this
            ->setupModel(new CategoryDocument())
            ->setValidatorClass(CategoryDocumentRequest::class)
            ->withCustomFields()
            ->add('document_id', 'customSelect', [
                'label' => 'Document',
                'label_attr' => ['class' => 'control-label'],
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => $documents,
            ])->add('category_id', 'customSelect', [
                'label' => 'Category',
                'label_attr' => ['class' => 'control-label'],
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => $categories,
            ])->add('required', 'onOff', [
                'label' => 'Required?',
                'label_attr' => ['class' => 'control-label'],
                'default_value' => true,
            ]);
    }
}