<?php

namespace Botble\RealEstate\Forms;

use Assets;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Forms\Fields\CustomEditorField;
use Botble\RealEstate\Forms\Fields\MultipleUploadField;
use Botble\RealEstate\Forms\Fields\CustomFileField;
use Botble\RealEstate\Forms\Fields\CustomFileField2;
use Botble\RealEstate\Forms\Fields\CustomFileField3;
use Botble\RealEstate\Http\Requests\PropertyRequest;

class AccountPropertyForm extends PropertyForm
{

    /**
     * @return mixed|void
     * @throws \Throwable
     */
    public function buildForm()
    {
        parent::buildForm();

        Assets::addScriptsDirectly('vendor/core/core/base/libraries/tinymce/tinymce.min.js');
        Assets::addStyles(['datetimepicker'])
            ->addScripts(['input-mask'])
            ->addScriptsDirectly([
                'vendor/core/plugins/real-estate/js/real-estate.js',
                'vendor/core/plugins/real-estate/js/components.js',
                '/js/real-estate-agent.js'
            ])
            ->addStylesDirectly('vendor/core/plugins/real-estate/css/real-estate.css');

        if (!$this->formHelper->hasCustomField('customEditor')) {
            $this->formHelper->addCustomField('customEditor', CustomEditorField::class);
        }

        if (!$this->formHelper->hasCustomField('multipleUpload')) {
            $this->formHelper->addCustomField('multipleUpload', MultipleUploadField::class);
        }
        if (!$this->formHelper->hasCustomField('customfile')) {
            $this->formHelper->addCustomField('customfile', CustomFileField::class);
        }
        if (!$this->formHelper->hasCustomField('customfile2')) {
            $this->formHelper->addCustomField('customfile2', CustomFileField2::class);
        }
        if (!$this->formHelper->hasCustomField('customfile3')) {
            $this->formHelper->addCustomField('customfile3', CustomFileField3::class);
        }


        $this
            ->setupModel(new Property)
            ->setFormOption('template', 'plugins/real-estate::account.forms.base')
            ->setFormOption('enctype', 'multipart/form-data')
            ->setValidatorClass(PropertyRequest::class)
            ->setActionButtons(view('plugins/real-estate::account.forms.actions')->render())
            ->remove('is_featured')
            ->remove('moderation_status')
            ->remove('content')
            ->remove('images[]')
            ->remove('document1')
            ->remove('document2')
            ->remove('document3')
            ->remove('never_expired')
            ->remove('btn_verify')
            ->remove('comments')

            ->modify('auto_renew', 'onOff', [
                'label'         => trans('plugins/real-estate::property.renew_notice', ['days' => config('plugins.real-estate.real-estate.property_expired_after_x_days')]),
                'label_attr'    => ['class' => 'control-label'],
                'default_value' => false,
                'wrapper'       => [
                    'class' => 'form-group col-md-6 auto-renew-form-group' . (!$this->getModel()->id || $this->getModel()->never_expired == true ? ' hidden' : null),
                ],
            ], true)
            ->remove('author_id')
            ->remove('rowOpenagent')

           /* ->addAfter('description', 'content', 'customEditor', [
                'label'      => trans('core/base::forms.content'),
                'label_attr' => ['class' => 'control-label required'],
            ])*/
           ->addAfter('description', 'images', 'multipleUpload', [
                'label'      => trans('plugins/real-estate::property.form.images'),
                'label_attr' => ['class' => 'control-label required'],
            ]) ;




    }
}
