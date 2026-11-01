<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\RealEstate\Enums\AgentApplicationStatusEnum;
use Botble\RealEstate\Models\AgentApplication;
use Throwable;

class AgentApplicationForm extends FormAbstract
{
    /**
     * @return mixed|void
     * @throws Throwable
     */
    public function buildForm()
    {
        $this
            ->setupModel(new AgentApplication())
            ->withCustomFields()
            ->add('status', 'customSelect', [
                'label' => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'class' => 'form-control select-full',
                    'disabled' => true,
                ],
                'choices' => AgentApplicationStatusEnum::labels(),
            ])
            ->addMetaBoxes([
                'information' => [
                    'title' => trans('plugins/real-estate::agent-application.application_information'),
                    'content' => view(
                        'plugins/real-estate::agent-application-info',
                        ['application' => $this->getModel()]
                    )->render(),
                    'attributes' => [
                        'style' => 'margin-top: 0',
                    ],
                ],
            ])
            ->setBreakFieldPoint('status');
    }
}
