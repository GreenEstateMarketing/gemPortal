<?php

namespace Botble\RealEstate\Forms;

use Botble\RealEstate\Models\Consult;
use Botble\Base\Forms\FormAbstract;
use Botble\RealEstate\Enums\ConsultStatusEnum;
use Botble\RealEstate\Http\Requests\ConsultRequest;
use Throwable;
use Assets;

class AccountConsultForm extends ConsultForm
{

    /**
     * @return mixed|void
     * @throws Throwable
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
        $this
            ->setupModel(new Consult)
            ->setFormOption('template', 'plugins/real-estate::account.forms.consult-base')
            ->setFormOption('enctype', 'multipart/form-data')
            ->setValidatorClass(ConsultRequest::class)
            ->remove('status')

            ->addAfter('status', 'status', 'customSelect', [
                'label'      => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'class' => 'form-control select-full',
                    'disabled'=>true,
                ],
                'choices'    => ConsultStatusEnum::labels(),
            ])



            /*->add('status', 'customSelect', [
                'label'      => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'class' => 'form-control select-full',
                ],
                'choices'    => ConsultStatusEnum::labels(),
            ])*/


            ->setActionButtons(view('plugins/real-estate::account.forms.actions')->render());
    }
}
