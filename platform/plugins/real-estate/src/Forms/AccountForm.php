<?php

namespace Botble\RealEstate\Forms;

use Assets;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\FormAbstract;
use Botble\RealEstate\Http\Requests\AccountCreateRequest;
use Botble\RealEstate\Models\Account;
use Throwable;
use Botble\Location\Repositories\Interfaces\CityInterface;
use Botble\Location\Repositories\Interfaces\CityAreaInterface;

class AccountForm extends FormAbstract
{

    /**
     * @var string
     */
    protected $template = 'plugins/real-estate::account.admin.form';

    /**
     * @var CityInterface
     */
    protected $cityRepository;

    /**
     * @var CityAreaInterface
     */
    protected $cityAreaRepository;

    public function __construct(
        CityInterface $cityRepository,
        CityAreaInterface $cityAreaRepository
    ) {
        parent::__construct();

        $this->cityRepository = $cityRepository;
        $this->cityAreaRepository = $cityAreaRepository;

    }

    /**
     * @return mixed|void
     * @throws Throwable
     */
    public function buildForm()
    {
        Assets::addStylesDirectly('vendor/core/plugins/real-estate/css/account-admin.css')
            ->addScriptsDirectly('/js/real-estate-admin.js')
            ->addScriptsDirectly(['/vendor/core/plugins/real-estate/js/account-admin.js'])
            ->addStylesDirectly('/css/real-estate-admin.css');
        $this
            ->setupModel(new Account)
            ->setValidatorClass(AccountCreateRequest::class)
            ->withCustomFields()
            ->add('first_name', 'text', [
                'label' => trans('plugins/real-estate::account.first_name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::account.first_name'),
                    'data-counter' => 120,
                ],
            ])
            ->add('last_name', 'text', [
                'label' => trans('plugins/real-estate::account.last_name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::account.last_name'),
                    'data-counter' => 120,
                ],
            ])
            ->add('username', 'text', [
                'label' => trans('plugins/real-estate::account.username'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::account.username_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('phone', 'text', [
                'label' => trans('plugins/real-estate::account.phone'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::account.phone_placeholder'),
                    'data-counter' => 20,
                ],
            ])
            ->add('email', 'text', [
                'label' => trans('plugins/real-estate::account.form.email'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::account.email_placeholder'),
                    'data-counter' => 60,
                ],
            ]);

        $cities = $this->cityRepository->allBy(
            ['status' => BaseStatusEnum::PUBLISHED],
            ['state', 'country'],
            ['cities.name', 'cities.state_id', 'cities.country_id', 'cities.id']
        );

        $cityareas = [];
        if ($this->getModel()->city_id > 0) {
            $cityareas = $this->cityAreaRepository->allBy(['city_id' => $this->getModel()->city_id]);
        }


        $cityChoices = [];
        $cityAreaChoices = [];
        //for getting published cities
        foreach ($cities as $city) {
            if ($city->state->status != BaseStatusEnum::PUBLISHED || $city->country->status != BaseStatusEnum::PUBLISHED) {
                continue;
            }
            $cityChoices[$city->id] = $city->name . ($city->state->name ? ' (' . $city->state->name . ')' : '');
        }

        //for getting city areas for selected cities
        foreach ($cityareas as $area) {
            $cityAreaChoices[$area->id] = $area->city_area_name;
        }

        $this->add('city_id', 'customSelect', [
            'label' => trans('plugins/real-estate::property.form.city'),
            'label_attr' => ['class' => 'control-label required'],
            'wrapper' => [
                'class' => 'form-group col-md-6',

            ],
            'attr' => [
                'class' => 'form-control select-search-full city_id',
            ],
            'choices' => [0 => trans('plugins/real-estate::property.select_city')] + $cityChoices,
        ])
            ->add('city_area_id', 'customSelect', [
                'label' => trans('plugins/real-estate::property.form.city_area'),
                'label_attr' => ['class' => 'control-label required'],
                'wrapper' => [
                    'class' => 'form-group col-md-6',

                ],
                'attr' => [
                    'class' => 'form-control select-search-full',
                ],
                'choices' => [trans('plugins/real-estate::property.select_city_area')] + $cityAreaChoices,
            ]);


        $this->add('is_change_password', 'checkbox', [
            'label' => trans('plugins/real-estate::account.form.change_password'),
            'label_attr' => ['class' => 'control-label'],
            'attr' => [
                'class' => 'hrv-checkbox',
            ],
            'value' => 1,
        ])

            ->add('password', 'password', [
                'label' => trans('plugins/real-estate::account.form.password'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'data-counter' => 60,
                ],
                'wrapper' => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ($this->getModel()->id ? ' hidden' : null),
                ],
            ])
            ->add('password_confirmation', 'password', [
                'label' => trans('plugins/real-estate::account.form.password_confirmation'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'data-counter' => 60,
                ],
                'wrapper' => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ($this->getModel()->id ? ' hidden' : null),
                ],
            ])
            ->add('image_path', 'mediaImage', [
                'label' => 'Profile Picture',
                'label_attr' => ['class' => 'control-label form-control'],
            ])
            ->add('agent_area', 'hidden', [

                'value' => '', //($this->getModel()->id?$this->getModel()->agent_area:'')
                'id' => 'agent_area',


            ])
            ->add('agent_area_edit', 'hidden', [

                'value' => ($this->getModel()->id ? $this->getModel()->coordinate : ''),
                'id' => 'agent_area_edit',


            ])
            ->add('location', 'hidden', [
                'value' => '',
                'id' => 'location',
            ]);

        if ($this->getModel()->id) {
            $this->addMetaBoxes([
                'credits' => [
                    'title' => null,
                    'content' => view('plugins/real-estate::account.admin.credits', ['account' => $this->model, 'transactions' => $this->model->transactions()->orderBy('created_at', 'DESC')->get()])->render(),
                    'wrap' => false,
                ],
            ]);
        }

        $this->addMetaBoxes([
            'Areas ' => [
                'title' => 'mark areas for agent ',
                'content' => view('plugins/real-estate::account.admin.agent_map')->render(),
                'wrap' => false,
            ],
        ]);
    }
}
