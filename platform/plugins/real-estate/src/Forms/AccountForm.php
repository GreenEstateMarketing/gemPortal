<?php

namespace Botble\RealEstate\Forms;

use Assets;
use Botble\Base\Forms\FormAbstract;
use Botble\RealEstate\Http\Requests\AccountCreateRequest;
use Botble\RealEstate\Models\Account;
use Throwable;

class AccountForm extends FormAbstract
{

    /**
     * @var string
     */
    protected $template = 'plugins/real-estate::account.admin.form';

    /**
     * @return mixed|void
     * @throws Throwable
     */
    public function buildForm()
    {
        Assets::addStylesDirectly('vendor/core/plugins/real-estate/css/account-admin.css')
            ->addScriptsDirectly(['/vendor/core/plugins/real-estate/js/account-admin.js'])
        ->addStylesDirectly('/css/real-estate-admin.css');
        $this
            ->setupModel(new Account)
            ->setValidatorClass(AccountCreateRequest::class)
            ->withCustomFields()
            ->add('first_name', 'text', [
                'label'      => trans('plugins/real-estate::account.first_name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('plugins/real-estate::account.first_name'),
                    'data-counter' => 120,
                ],
            ])
            ->add('last_name', 'text', [
                'label'      => trans('plugins/real-estate::account.last_name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('plugins/real-estate::account.last_name'),
                    'data-counter' => 120,
                ],
            ])
            ->add('username', 'text', [
                'label'      => trans('plugins/real-estate::account.username'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('plugins/real-estate::account.username_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('phone', 'text', [
                'label'      => trans('plugins/real-estate::account.phone'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => trans('plugins/real-estate::account.phone_placeholder'),
                    'data-counter' => 20,
                ],
            ])
            ->add('email', 'text', [
                'label'      => trans('plugins/real-estate::account.form.email'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('plugins/real-estate::account.email_placeholder'),
                    'data-counter' => 60,
                ],
            ])
            ->add('is_change_password', 'checkbox', [
                'label'      => trans('plugins/real-estate::account.form.change_password'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class' => 'hrv-checkbox',
                ],
                'value'      => 1,
            ])

            ->add('password', 'password', [
                'label'      => trans('plugins/real-estate::account.form.password'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'data-counter' => 60,
                ],
                'wrapper'    => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ($this->getModel()->id ? ' hidden' : null),
                ],
            ])
            ->add('password_confirmation', 'password', [
                'label'      => trans('plugins/real-estate::account.form.password_confirmation'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'data-counter' => 60,
                ],
                'wrapper'    => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ($this->getModel()->id ? ' hidden' : null),
                ],
            ])
        ->add('agent_area', 'hidden', [

            'value'      =>'', //($this->getModel()->id?$this->getModel()->agent_area:'')
        'id'=>'agent_area',


    ])
        ->add('agent_area_edit', 'hidden', [

        'value'      => ($this->getModel()->id?$this->getModel()->coordinate:''),
        'id'=>'agent_area_edit',


    ]);

        if ($this->getModel()->id) {
            $this->addMetaBoxes([
                'credits' => [
                    'title'   => null,
                    'content' => view('plugins/real-estate::account.admin.credits', ['account' => $this->model, 'transactions' => $this->model->transactions()->orderBy('created_at', 'DESC')->get()])->render(),
                    'wrap'    => false,
                ],
            ]);
        }

        $this->addMetaBoxes([
            'Areas ' => [
                'title'   => 'mark areas for agent ',
                'content' => view('plugins/real-estate::account.admin.agent_map')->render(),
                'wrap'    => false,
            ],
        ]);
    }
}
