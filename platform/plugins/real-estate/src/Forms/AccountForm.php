<?php

namespace Botble\RealEstate\Forms;

use Assets;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\FormAbstract;
use Botble\RealEstate\Http\Requests\AccountCreateRequest;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Models\SpokenLanguage;
use Throwable;
use Botble\Location\Repositories\Interfaces\CityInterface;
use Botble\Location\Repositories\Interfaces\CityAreaInterface;
use Botble\Location\Repositories\Interfaces\CountryInterface;
use Botble\Location\Repositories\Interfaces\StateInterface;
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
    protected $countryRepository;
    protected $stateRepository;
    public function __construct(
        CountryInterface $countryRepository,
        StateInterface $stateRepository,
        CityInterface $cityRepository,
        CityAreaInterface $cityAreaRepository
    ) {
        parent::__construct();

        $this->countryRepository = $countryRepository;
        $this->stateRepository = $stateRepository;
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
            ->addScriptsDirectly('/js/real-estate-agent.js')
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

        $countries = $this->countryRepository
            ->pluck('countries.name', 'countries.id');

        // On edit, derive the existing country/state/city/city-area chain from
        // the account's stored city_id so these cascading dropdowns show what's
        // already saved instead of appearing empty and forcing the admin to
        // re-pick location on every edit even when it hasn't changed.
        $selectedCityId = $this->getModel()->city_id;
        $selectedCity = $selectedCityId ? $this->cityRepository->getModel()->find($selectedCityId) : null;
        $selectedStateId = $selectedCity->state_id ?? null;
        $selectedCountryId = $selectedCity->country_id ?? null;

        $states = $selectedCountryId
            ? $this->stateRepository->getModel()->where('country_id', $selectedCountryId)->pluck('name', 'id')->toArray()
            : [];
        $cityChoices = $selectedStateId
            ? $this->cityRepository->getModel()->where('state_id', $selectedStateId)->pluck('name', 'id')->toArray()
            : [];
        $cityAreaChoices = $selectedCityId
            ? $this->cityAreaRepository->getModel()->where('city_id', $selectedCityId)->pluck('city_area_name', 'id')->toArray()
            : [];

        $this->add('country_id', 'customSelect', [
            'label' => 'Country',
            'label_attr' => ['class' => 'control-label required'],
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ],
            'attr' => [
                'id' => 'country_id',
                'class' => 'form-control select-search-full',
                'data-change-country-url' => url('/ajax/get-states'),
            ],
            'choices' => ['' => 'Select Country'] + $countries,
            'selected' => $selectedCountryId,
        ])

            ->add('state_id', 'customSelect', [
                'label' => 'State',
                'label_attr' => ['class' => 'control-label required'],
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
                'attr' => [
                    'id' => 'state_id',
                    'class' => 'form-control select-search-full',
                    'data-change-state-url' => url('/ajax/get-cities'),
                ],
                'choices' => ['' => 'Select State'] + $states,
                'selected' => $selectedStateId,
            ]);
        $this->add('city_id', 'customSelect', [
            'label' => trans('plugins/real-estate::property.form.city'),
            'label_attr' => ['class' => 'control-label required'],
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ],
            'attr' => [
                'id' => 'city_id',
                'class' => 'form-control select-search-full city_id',
            ],
            'choices' => [
                '' => trans('plugins/real-estate::property.select_city'),
            ] + $cityChoices,
            'selected' => $selectedCityId,
        ])
            ->add('city_area_id', 'customSelect', [
                'label' => trans('plugins/real-estate::property.form.city_area'),
                'label_attr' => ['class' => 'control-label required'],
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
                'attr' => [
                    'id' => 'city_area_id',
                    'class' => 'form-control select-search-full',
                    'multiple' => 'multiple',
                    'name' => 'city_area_id[]'
                ],
                'choices' => $cityAreaChoices,
                'selected' => explode(',', $this->getModel()->city_area_id)
            ]);

        $languageChoices = SpokenLanguage::where('status', BaseStatusEnum::PUBLISHED)->orderBy('order')->pluck('name', 'id')->toArray();
        $specialtyChoices = Category::where('status', BaseStatusEnum::PUBLISHED)->orderBy('name')->pluck('name', 'id')->toArray();
        $selectedLanguageIds = $this->getModel()->id ? $this->getModel()->spokenLanguages()->pluck('re_spoken_languages.id')->all() : [];
        $selectedSpecialtyIds = $this->getModel()->id ? $this->getModel()->specialties()->pluck('re_categories.id')->all() : [];

        $this->add('years_of_experience', 'number', [
            'label' => trans('plugins/real-estate::account.years_of_experience'),
            'label_attr' => ['class' => 'control-label'],
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ],
            'attr' => [
                'min' => 0,
                'max' => 25,
            ],
        ])
            ->add('languages', 'customSelect', [
                'label' => trans('plugins/real-estate::account.languages'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
                'attr' => [
                    'class' => 'form-control select-search-full',
                    'multiple' => 'multiple',
                    'name' => 'languages[]',
                ],
                'choices' => $languageChoices,
                'selected' => $selectedLanguageIds,
            ])
            ->add('specialties', 'customSelect', [
                'label' => trans('plugins/real-estate::account.specialties'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
                'attr' => [
                    'class' => 'form-control select-search-full',
                    'multiple' => 'multiple',
                    'name' => 'specialties[]',
                ],
                'choices' => $specialtyChoices,
                'selected' => $selectedSpecialtyIds,
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
