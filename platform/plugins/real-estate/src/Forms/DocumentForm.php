<?php

namespace Botble\RealEstate\Forms;

use Assets;
use Botble\Base\Forms\FormAbstract;
use Botble\RealEstate\Http\Requests\DocumentRequest;
use Botble\RealEstate\Models\Document;
use Botble\RealEstate\Repositories\Interfaces\DocumentInterface;

class DocumentForm extends FormAbstract
{

    protected $documentRepo;

    public function __construct(DocumentInterface $documentRepo)
    {
        parent::__construct();
        $this->documentRepo = $documentRepo;
    }

    public function buildForm()
    {

        Assets::addScripts(['input-mask']);

        $this
            ->setupModel(new Document())
            ->setValidatorClass(DocumentRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label' => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])->add('type', 'text', [
                'label' => trans('core/base::forms.type'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('core/base::forms.type_placeholder'),
                    'data-counter' => 120,
                ],
            ]);
    }
}