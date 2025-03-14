<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\RealEstate\Models\Member;

class MemberForm extends FormAbstract
{
    protected $template = 'plugins/real-estate::account.admin.form';

    public function buildForm()
    {
        $this
            ->setupModel(new Member)
            ->add('full_name', 'text', [
                'label' => trans('Full Name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('Full Name'),
                    'data-counter' => 120,
                ],
            ])->add('email', 'text', [
                'label' => trans('plugins/real-estate::account.form.email'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::account.email_placeholder'),
                    'data-counter' => 60,
                    'disabled' => 'disabled',
                ],
            ])->add('mobile_no', 'text', [
                'label' => trans('Mobile No'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('Mobile No'),
                    'data-counter' => 20,
                ],
            ])->add('credits', 'text', [
                'label' => trans('Credits'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'placeholder' => trans('Credits'),
                    'data-counter' => 20,
                    'disabled' => 'disabled',
                ],
            ]);
    }
}
