<?php

namespace Botble\RealEstate\Forms;

use App\Models\description_template;
use Assets;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\FormAbstract;
use Botble\Location\Models\CityArea;
use Botble\Location\Repositories\Interfaces\CityInterface;
use Botble\Location\Repositories\Interfaces\CityAreaInterface;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\PropertyPeriodEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\RealEstate\Forms\Fields\LocationField;
use Botble\RealEstate\Forms\Fields\MediaFileField1;
use Botble\RealEstate\Http\Requests\PropertyRequest;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Botble\RealEstate\Repositories\Interfaces\CategoryDocumentInterface;
use Botble\RealEstate\Repositories\Interfaces\CategoryInterface;
use Botble\RealEstate\Repositories\Interfaces\CurrencyInterface;
use Botble\RealEstate\Repositories\Interfaces\FacilityInterface;
use Botble\RealEstate\Repositories\Interfaces\FeatureInterface;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Str;
use Throwable;
use Auth;

class PropertyForm extends FormAbstract
{
    /**
     * @var FacilityInterface
     */
    protected $facilityRepository;

    /**
     * @var PropertyInterface
     */
    protected $propertyRepository;

    /**
     * @var ProjectInterface
     */
    protected $projectRepository;

    /**
     * @var FeatureInterface
     */
    protected $featureRepository;

    /**
     * @var CurrencyInterface
     */
    protected $currencyRepository;

    /**
     * @var CityInterface
     */
    protected $cityRepository;

    /**
     * @var CityAreaInterface
     */
    protected $cityAreaRepository;

    /**
     * @var CategoryInterface
     */
    protected $categoryRepository;

    protected $categoryDocumentRepository;

    protected $accountRepository;


    public function __construct(
        PropertyInterface $propertyRepository,
        ProjectInterface $projectRepository,
        FeatureInterface $featureRepository,
        CurrencyInterface $currencyRepository,
        CityInterface $cityRepository,
        CityAreaInterface $cityAreaRepository,
        CategoryInterface $categoryRepository,
        FacilityInterface $facilityRepository,
        CategoryDocumentInterface $categoryDocumentRepository,
        AccountInterface $accountRepository
    ) {
        parent::__construct();
        $this->propertyRepository = $propertyRepository;
        $this->projectRepository = $projectRepository;
        $this->featureRepository = $featureRepository;
        $this->currencyRepository = $currencyRepository;
        $this->cityRepository = $cityRepository;
        $this->cityAreaRepository = $cityAreaRepository;
        $this->categoryRepository = $categoryRepository;
        $this->facilityRepository = $facilityRepository;
        $this->categoryDocumentRepository = $categoryDocumentRepository;
        $this->accountRepository = $accountRepository;
    }

    /**
     * @return mixed|void
     * @throws Throwable
     */
    public function buildForm()
    {
        Assets::addStyles(['datetimepicker'])
            ->addScripts(['input-mask'])
            ->addScriptsDirectly([
                'vendor/core/plugins/real-estate/js/real-estate.js',
                'vendor/core/plugins/real-estate/js/components.js',
                '/js/toast.min.js',
                '/js/real-estate-admin.js',
                '/js/app.js'
            ])
            ->addStylesDirectly('vendor/core/plugins/real-estate/css/real-estate.css')
            ->addStylesDirectly('/css/real-estate-admin.css')
            ->addStylesDirectly('/css/toast.css');

        //Auto selected city and city area for agents
        $cityId = 0;
        $cityAreaId = 0;
        if (!auth('member')->user() && auth('account')->user()) {
            $agentId = auth('account')->user()->getAuthIdentifier();
            $agent = $this->accountRepository->findById($agentId);
            $cityId = $agent->city_id;
            $cityAreaId = $agent->city_area_id;
        }

        $projects = $this->projectRepository->pluck('re_projects.name', 're_projects.id');
        $cityareas = [];
        if ($cityAreaId != 0) {
            $cityareas = CityArea::where('city_id', $cityId)->whereIn('id', explode(',', $cityAreaId))->get();
        } else {
            if ($this->getModel()) {
                $cityareas = $this->cityAreaRepository->allBy(['city_id' => $this->getModel()->city_id]);
            }
        }

        $currencies = $this->currencyRepository->pluck('re_currencies.title', 're_currencies.id');
        $cities = $this->cityRepository->allBy(
            ['status' => BaseStatusEnum::PUBLISHED],
            ['state', 'country'],
            ['cities.name', 'cities.state_id', 'cities.country_id', 'cities.id']
        );
        $areaUnits = array('ft²' => 'Square Feet', 'm²' => 'Square Meter', 'yards' => 'Yards', 'marla' => 'Marla', 'kanal' => 'Kanal');
        $res_data = description_template::where('status', 1)->first();

        $properties = $this->getModel();

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
        $categories = $this->categoryRepository->allBy(
            ['status' => BaseStatusEnum::PUBLISHED, 'parent_id' => 0],
            [],
            ['id', 'parent_id', 'name']
        );
        $html = '<div class="col-md-6">  <ul class="parent-category">';
        $subcategory = '';
        $firstSelected = '';
        if ($this->getModel()) {
            $label_primary = 'label-secondary';
            $label_sub_category = 'label-sub-category';
        } else {
            $firstSelected = '<span class="category-tick-selected"><i class="fas fa-check"></i></span>';
            $label_primary = 'label-primary';
            $label_sub_category = 'label-primary';
        }

        foreach ($categories as $key => $val) {
            if ($this->getModel()) {
                $innerCategory = $this->categoryRepository->findById($this->getModel()->category_id);
                if ($innerCategory->parent_id == $val->id) {
                    $label_primary = 'label-primary';
                } else {
                    $label_primary = 'label-secondary';
                }
            }

            $html .= '<li  data-id=' . $val->id . ' data-category_name="' . $val->name . '"  style="cursor:pointer" class="' . $label_primary . ' p-category"> ' . $val->name . '</li>';

            // create hiddenly list here of each categories
            $subcategory .= '<div class="offset-md-1 col-md-8 p' . $val->id . '" style="display:none"><ul class="sub-category">';
            $sub_categories = $this->categoryRepository->allBy(
                ['status' => BaseStatusEnum::PUBLISHED, 'parent_id' => $val->id],
                [],
                ['id', 'parent_id', 'name']
            );


            foreach ($sub_categories as $key1 => $val1) {

                if ($key1 == 0)
                    $subcategory .= '<li class="p-subcategory ' . $label_sub_category . '"  data-parent-name="' . $val->name . '" data-id=' . $val1->id . ' data-category_name="' . $val1->name . '"  style="cursor:pointer" >' . $firstSelected . $val1->name . '</li>';
                else
                    $subcategory .= '<li class="label-sub-category p-subcategory" data-parent-name="' . $val->name . '" data-id=' . $val1->id . ' data-category_name="' . $val1->name . '"  style="cursor:pointer" >' . $val1->name . '</li>';

            }
            $subcategory .= '</ul></div>';

        }
        $html .= '</ul></div>';

        /*
         **
         * logic for type btn
         **
         */

        $sale_btn = '';
        $rent_btn = '';
        if ($this->getModel()) {
            if ($this->getModel()->type == 'rent') {
                $sale_btn = '<div class="col-md"><button type="button" class="btn label-secondary type_sale" data-id="sale"  data-type-name="SALE" style="Width:100%" value="sale">SALE</button></div>';
                $rent_btn = '<div class="col-md"><button type="button" class="btn label-primary type_rent" data-id="rent"  data-type-name="RENT" style="Width:100%" value="rent"><span class="tick-selected"><i class="fas fa-check"></i></span>   RENT</button></div>';

            } else {
                $sale_btn = '<div class="col-md"><button type="button" class="btn label-primary type_sale" data-id="sale"  data-type-name="SALE" style="Width:100%" value="sale"><span class="tick-selected"><i class="fas fa-check"></i></span>  SALE</button></div>';
                $rent_btn = '<div class="col-md"><button type="button" class="btn label-secondary type_rent" data-id="rent"  data-type-name="RENT" style="Width:100%" value="rent">RENT</button></div>';

            }
        } else {
            $sale_btn = '<div class="col-md"><button type="button" class="btn label-primary type_sale" data-id="sale"  data-type-name="SALE" style="Width:100%" value="sale"><span class="tick-selected"><i class="fas fa-check"></i></span>  SALE</button></div>';
            $rent_btn = '<div class="col-md"><button type="button" class="btn label-secondary type_rent" data-id="rent"  data-type-name="RENT" style="Width:100%" value="rent">RENT</button></div>';
        }

        $selectedFeatures = [];
        if ($this->getModel()) {
            $selectedFeatures = $this->getModel()->features()->pluck('re_features.id')->all();
        }

        $features = $this->featureRepository->allBy([], [], ['re_features.id', 're_features.name']);

        $facilities = $this->facilityRepository->allBy([], [], ['re_facilities.id', 're_facilities.name'])->toArray();

        $selectedFacilities = [];
        if ($this->getModel()) {
            $selectedFacilities = $this->getModel()->facilities()->select('re_facilities.id', 'distance')->get()->toArray();
        }

        $sellerType = $this->getModel() ? $this->getModel()->member_id ? 'Member' : 'Agent' : 'None';

        $sellerName = 'Not Available';
        $sellerEmail = 'Not Available';
        $sellerPhone = 'Not Available';
        $sellerEmailAddress = 'Not Available';
        $credits = true;

        if ($sellerType == 'Member') {
            if ($this->getModel()->member) {
                $sellerName = $this->getModel()->member->full_name;
                $sellerEmail = $this->getModel()->member->email;
                $sellerEmailAddress = $this->getModel()->member->email;
                $sellerPhone = $this->getModel()->member->mobile_no;
                $credits = $this->getModel()->member->credits > 0;
            }

        } else if ($sellerType == 'Agent') {
            if ($this->getModel()->user) {
                $sellerName = $this->getModel()->user->first_name . ' ' . $this->getModel()->user->last_name;
                $sellerEmail = $this->getModel()->user->email;
                $sellerEmailAddress = $this->getModel()->user->email;
                $sellerPhone = $this->getModel()->user->phone;
                $credits = $this->getModel()->user->credits > 0;
            }
        }

        $sellerName = ucwords($sellerName);

        $sellerType = $credits ? $sellerType : $sellerType . ' (Credits Not Available)';

        $moderationStatuses = ModerationStatusEnum::labels();
        $selectedModerationStatus = $this->model ? $this->model->moderation_status->getValue() : '';

        $verifyDocuments = false;
        if ($this->model) {
            $categoryDocuments = $this->categoryDocumentRepository->getByCategoryId($this->model->category_id);
            if ($categoryDocuments > 0) {
                $verifyDocuments = true;
            }
        }

        if ($sellerEmail != 'Not Available') {
            if ($credits) {
                $sellerEmail = '<a style="color: ' . ($credits ? '#155724' : '#721c24') . '; text-decoration: underline;" href="mailto:' . $sellerEmail . '">' . $sellerEmail . '</a>';
            } else {
                if ($this->getModel()->member_id) {
                    $query = [
                        'id' => $this->getModel()->member_id,
                        'type' => 'member',
                        'property_id' => $this->getModel()->id,
                        'title' => $this->getModel()->name,
                        'from' => auth('account')->user()->getAuthIdentifier() ? 'agent' : 'admin'
                    ];
                } else {
                    $query = [
                        'id' => $this->getModel()->author_id,
                        'type' => 'agent',
                        'property_id' => $this->getModel()->id,
                        'title' => $this->getModel()->name,
                        'from' => 'admin'
                    ];
                }
                $sellerEmail = '<a href="' . route('mail-for-payment', $query) . '" type="button" class="btn btn-primary">Mail ' . $sellerName . ' For Payment</a>';
            }
        }

        $verified = $this->model ? $this->model->verified : false;


        $this
            ->setupModel(new Property)
            ->setValidatorClass(PropertyRequest::class)
            ->withCustomFields()
            ->addCustomField('location', LocationField::class)
            ->addCustomField('mediafile1', MediaFileField1::class);

        if (!Str::contains(request()->url(), 'create')) {
            $this->add('rowOpenVerificatonInfo', 'html', [
                'html' => '<div class="row mb-2 ml-1 pt-1 pb-1 align-items-center" style="border-radius: 50px;">',
            ]);
        }


        if ($this->model->verified) {
            $this->add(
                'VerificatonInfo',
                'html',
                [
                    'html' => '<div class="col-md-4 col-lg-4 alert alert-success"><i class="fa fa-check"></i> This Property has been Verified by Agent.</div>'
                ]
            );
        } else {
            if (!Str::contains(request()->url(), 'create')) {
                $this->add(
                    'VerificatonInfo',
                    'html',
                    [
                        'html' => '<div class="col-md-4 col-lg-4 alert alert-danger"><i class="fa fa-times"></i> This Property has not been Verified by Agent.</div>'
                    ]
                );
            }

        }

        if (!Str::contains(request()->url(), 'create')) {
            $this->add('rowCloseVerificatonInfo', 'html', [
                'html' => '</div>',
            ]);
        }



        $this->add('rowOpenSellerInfo', 'html', [
            'html' => '<div class="row ml-1 mb-3 pt-2 pb-3 align-items-center" style="border-radius: 50px;">',
        ])
            ->add(
                'SellerInfo',
                'html',
                [
                    'html' => '
        <div class="col-md-3 col-lg-3">
            <div>
                <div class="bold">User Type:</div>
                <div>' . $sellerType . '</div>
            </div>
        </div>
        <div class="col-md-2 col-lg-2">
            <div>
                <div class="bold">Name:</div>
                <div>' . $sellerName . '</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-4">
            <div>
                <div><span class="bold">Email: </span>(' . $sellerEmailAddress . ')</div>
                <div style="color: ' . ($credits ? '#155724' : '#721c24') . ';">' . $sellerEmail . '</div>
            </div>
        </div>
        <div class="col-md-3 col-lg-3">
            <div class="">
                <div class="bold">Phone:</div>
                <div>
                    <a target="_blank" style="color: ' . ($credits ? '#155724' : '#721c24') . '; text-decoration: underline;" href="https://wa.me/+92' . ltrim($sellerPhone, '0') . '">' . $sellerPhone . '</a>
                </div>
            </div>
        </div>'
                ]
            )
            ->add('rowCloseSellerInfo', 'html', [
                'html' => '</div>',
            ])
            ->add('rowOpenType', 'html', [
                'html' => '<div class="row mb-2 align-items-center">',
            ])
            ->add(
                'type_label',
                'html',
                [
                    'html' => '<div class="col-md-1 control-label ">Type</div>'
                ]
            )
            ->add(
                'type_sale',
                'html',
                [
                    'html' => $sale_btn
                ]
            )
            ->add(
                'type_rent',
                'html',
                [
                    'html' => $rent_btn
                ]
            )
            ->add('rowCloseType', 'html', [
                'html' => '</div>',
            ])
            ->add('rowOpenCategory', 'html', [
                'html' => '<div class="row  mt-4 align-items-baseline">',
            ])
            ->add(
                'category_label',
                'html',
                [
                    'html' => '<div class="col-md-1 control-label ">Category</div>'
                ]
            )
            ->add(
                'type_category_list',
                'html',
                [
                    'html' => $html
                ]
            )
            ->add('rowCloseCategory', 'html', [
                'html' => '</div>',
            ])
            ->add('rowOpenCategory1', 'html', [
                'html' => '<div class="row mb-4  align-items-center">',
            ])
            ->add(
                'type_sub_category_list',
                'html',
                [
                    'html' => $subcategory . '
                                        <div class="offset-md-1 col-md-8 p">
                                        <div class="category-li pcateory_data">
                                                        </div>
                                                        </div>'
                ]
            )
            ->add('rowCloseCategory1', 'html', [
                'html' => '</div>',
            ])
            ->add('rowOpentitle', 'html', [
                'html' => '<div class="row mb-2 mt-3">',
            ])
            ->add('name', 'text', [
                'label' => trans('plugins/real-estate::property.form.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::property.form.name'),
                    'data-counter' => 120,
                ],
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
            ])
            ->add('project_id', 'customSelect', [
                'label' => trans('plugins/real-estate::property.form.project'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
                'attr' => [
                    'class' => 'form-control select-search-full',
                ],
                'choices' => [0 => trans('plugins/real-estate::property.select_project')] + $projects,
            ]);

        if ($cityId > 0) {
            $this->add('city_id', 'hidden', [
                'value' => $cityId,
            ])
                ->add('city_id_display', 'customSelect', [
                    'label' => trans('plugins/real-estate::property.form.city'),
                    'label_attr' => ['class' => 'control-label required'],
                    'wrapper' => [
                        'class' => 'form-group col-md-6',
                    ],
                    'attr' => [
                        'class' => 'form-control select-search-full city_id',
                        'disabled' => 'disabled',
                    ],
                    'choices' => [0 => trans('plugins/real-estate::property.select_city')] + $cityChoices,
                    'selected' => $cityId,
                ]);
        } else {
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
                'selected' => $this->getModel()->city_id ? $this->getModel()->city_id : '',
            ]);
        }

        if ($cityAreaId > 0) {
            $this
                ->add('city_area_id', 'customSelect', [
                    'label' => trans('plugins/real-estate::property.form.city_area'),
                    'label_attr' => ['class' => 'control-label required'],
                    'wrapper' => [
                        'class' => 'form-group col-md-6',
                    ],
                    'attr' => [
                        'class' => 'form-control select-search-full'
                    ],
                    'choices' => [trans('plugins/real-estate::property.select_city_area')] + $cityAreaChoices,
                    'selected' => $this->getModel()->city_area_id ? $this->getModel()->city_area_id : ''
                ]);
        } else {
            $this->add('city_area_id', 'customSelect', [
                'label' => trans('plugins/real-estate::property.form.city_area'),
                'label_attr' => ['class' => 'control-label required'],
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
                'attr' => [
                    'class' => 'form-control select-search-full',
                ],
                'choices' => [trans('plugins/real-estate::property.select_city_area')] + $cityAreaChoices
            ]);
        }

        $this->add('built_in', 'number', [
            'label' => trans('Built In'),
            'label_attr' => ['class' => 'control-label'],
            'attr' => [
                'placeholder' => trans('Year the property was built'),
                'data-counter' => 4,
            ],
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ],
        ])
            ->add('rowClosetitle', 'html', [
                'html' => '</div>',
            ])
            ->add('reject_reason', 'textarea', [
                'label' => trans('core/base::forms.reject_reason'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'rows' => 4,
                    'data-counter' => 350,
                ],
                'wrapper' => [
                    'class' => 'd-none',
                ],
            ])
            ->add('description', 'textarea', [
                'label' => trans('core/base::forms.description'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'rows' => 4,
                    'placeholder' => trans('core/base::forms.description_placeholder'),
                    'readonly' => true

                ]
            ])
            ->add('rowOpendocument', 'html', [
                'html' => '<div class="row mb-2 mt-3 document-row">',
            ])
            ->add('rowClosedocument', 'html', [
                'html' => '</div>',
            ])
            // ->add('document1', 'mediaFile', [
            //     'label' => trans('plugins/real-estate::property.form.document1'),
            //     'label_attr' => ['class' => 'control-label'],
            //     'id' => 'document3',
            //     'wrapper' => [
            //         'class' => 'form-group col-md-3',
            //     ],
            //     'values' => '',
            // ])
            // ->add('document2', 'mediafile1', [
            //     'label' => trans('plugins/real-estate::property.form.document2'),
            //     'label_attr' => ['class' => 'control-label'],
            //     'id' => 'document2',
            //     'wrapper' => [
            //         'class' => 'form-group col-md-3',
            //     ],
            //     'values' => '',
            // ])
            // ->add('document3', 'mediafile1', [
            //     'label' => trans('plugins/real-estate::property.form.document3'),
            //     'label_attr' => ['class' => 'control-label'],
            //     'id' => 'document3',
            //     'wrapper' => [
            //         'class' => 'form-group col-md-3',
            //     ],
            //     'values' => '',
            // ])
            ->add('rowOpenverify', 'html', [
                'html' => '<div class="row mb-2 mt-3">',
            ])
            ->add('btn_verify', 'html', [
                'html' => '<button type = "button"  class="btn btn-success" style="display: none" id = "btn_verify"  data-toggle="modal"   data-target="#myModal" > Verify Checklist</button >',
                'wrapper' => [
                    'class' => 'form-group col-md-3',
                ],
            ])
            ->add('rowCloseverify', 'html', [
                'html' => '</div>',
            ])
            ->add('images[]', 'mediaImages', [
                'label' => trans('plugins/real-estate::property.form.images'),
                'label_attr' => ['class' => 'control-label required'],
                'values' => $this->getModel()->id ? $this->getModel()->images : [],
                'attr' => [
                    'required' => true,
                ],
            ])
            ->add('location', 'location', [
                'label' => trans('plugins/real-estate::property.form.location'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::property.form.location'),
                    'data-counter' => 300,
                ],
            ])
            ->add('rowOpenLoc', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('latitude', 'text', [
                'label' => trans('plugins/real-estate::property.form.latitude'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper' => [
                    'class' => 'form-group col-md-6 d-none',
                ],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::property.form.latitude'),
                    'readonly' => true,

                ],
            ])
            ->add('longitude', 'text', [
                'label' => 'Longitude',//trans('plugins/real-estate::property.form.longitude')
                'label_attr' => ['class' => 'control-label'],
                'wrapper' => [
                    'class' => 'form-group col-md-6 d-none',
                ],
                'attr' => [
                    'placeholder' => 'Longitude',//trans('plugins/real-estate::property.form.longitude')
                    'readonly' => true,
                ],
            ])
            ->add('rowCloseLoc', 'html', [
                'html' => '</div>',
            ])
            ->add('rowOpen1', 'html', [
                'html' => '<div class="row category_attr">',
            ])
            ->add('number_bedroom', 'number', [
                'label' => trans('plugins/real-estate::property.form.number_bedroom'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper' => [
                    'class' => 'form-group col-md-4 number_bedroom',
                ],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::property.form.number_bedroom'),
                ],
            ])
            ->add('number_bathroom', 'number', [
                'label' => trans('plugins/real-estate::property.form.number_bathroom'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper' => [
                    'class' => 'form-group col-md-4 number_bathroom',
                ],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::property.form.number_bathroom'),
                ],
            ])
            ->add('number_floor', 'number', [
                'label' => trans('plugins/real-estate::property.form.number_floor'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::property.form.number_floor'),
                ],
            ])
            ->add('rowClose1', 'html', [
                'html' => '</div>',
            ])
            ->add('rowOpen2', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('area_units', 'customSelect', [
                'label' => trans('plugins/real-estate::property.form.area_units'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper' => [
                    'class' => 'form-group col-md-3',
                ],
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => $areaUnits,
            ])
            ->add('square', 'text', [
                'label' => trans('plugins/real-estate::property.form.square'),
                'label_attr' => ['class' => 'control-label info-area-icon required '],
                'wrapper' => [
                    'class' => 'form-group col-md-3',
                ],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::property.form.square'),
                    'id' => 'square',
                    'class' => 'form-control input-mask-number',
                    'required' => true
                ],
            ])
            ->add('price', 'text', [
                'label' => trans('plugins/real-estate::property.form.price'),
                'label_attr' => ['class' => 'control-label required'],
                'wrapper' => [
                    'class' => 'form-group col-md-3',
                ],
                'attr' => [
                    'id' => 'price-number',
                    'placeholder' => trans('plugins/real-estate::property.form.price'),
                    'class' => 'form-control input-mask-number',
                    'required' => true
                ],
            ])
            ->add('currency_id', 'customSelect', [
                'label' => trans('plugins/real-estate::project.form.currency'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper' => [
                    'class' => 'form-group col-md-3',
                ],
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => $currencies,
            ])
            ->addMetaBoxes([
                'features' => [
                    'title' => trans('plugins/real-estate::property.form.features'),
                    'content' => view(
                        'plugins/real-estate::partials.form-features',
                        compact('selectedFeatures', 'features')
                    )->render(),
                    'priority' => 2,
                ],
                'facilities' => [
                    'title' => trans('plugins/real-estate::property.distance_key'),
                    'content' => view(
                        'plugins/real-estate::partials.form-facilities',
                        compact('facilities', 'selectedFacilities')
                    ),
                    'priority' => 1,
                ],
                'moderation_status' => [
                    'title' => trans('plugins/real-estate::property.moderation_status'),
                    'content' => view(
                        'plugins/real-estate::partials.moderation-status',
                        compact('moderationStatuses', 'selectedModerationStatus', 'credits', 'verified')
                    ),
                    'priority' => 3
                ]
            ])
            ->add('period', 'customSelect', [
                'label' => trans('plugins/real-estate::property.form.period'),
                'label_attr' => ['class' => 'control-label required'],
                'wrapper' => [
                    'class' => 'form-group period-form-group col-md-4' . ($this->getModel()->type != PropertyTypeEnum::RENT ? ' hidden' : null),
                ],
                'attr' => [
                    'class' => 'form-control select-search-full',
                ],
                'choices' => PropertyPeriodEnum::labels(),
            ])
            ->add('rowClose2', 'html', [
                'html' => '</div>',
            ])
            ->add('rowOpenaccount', 'html', [
                'html' => '<div class="row align-items-center" >',
            ])
            ->add('rowOpenagent', 'html', [
                'html' => view('plugins/real-estate::partials.agent_list'),
            ])
            ->add('never_expired', 'onOff', [
                'label' => trans('plugins/real-estate::property.never_expired'),
                'label_attr' => ['class' => 'control-label'],
                'default_value' => true,
                'wrapper' => [
                    'class' => 'form-group period-form-group col-md-3',
                ],
            ])
            ->add('property_id', 'hidden', [

                'value' => $this->model->id ?: "",
                'id' => 'property_id'

            ])
            ->add('moderation_status_hidden', 'hidden', [
                'value' => $this->model->moderation_status ?: "",
            ])
            ->add('credits', 'hidden', [
                'value' => $credits
            ])
            ->add('moderation_status', 'hidden', [
                'value' => "",
                'id' => 'moderation-status'
            ])
            ->add('verify_documents', 'hidden', [
                'value' => $verifyDocuments
            ])
            ->add('author_id_hidden', 'hidden', [

                'value' => $this->model->author_id,
                'id' => 'author_id_hidden' //id is not updating*

            ])
            ->add('document1_id_hidden', 'hidden', [

                'value' => $this->model->document1,
                'id' => 'document1_id_hidden' //id is not updating*

            ])
            ->add('document2_id_hidden', 'hidden', [

                'value' => $this->model->document2,
                'id' => 'document2_id_hidden', //id is not updating*

            ])
            ->add('document3_id_hidden', 'hidden', [

                'value' => $this->model->document3,
                'id' => 'document3_id_hidden' //id is not updating*

            ])
            ->add('is_featured', 'onOff', [
                'label' => trans('core/base::forms.is_featured'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper' => [
                    'class' => 'form-group period-form-group col-md-3',
                ],
                'default_value' => false,
            ]);

        if ($this->model->expire_date) {
            if ($credits && $this->model->expire_date->isPast()) {
                $this->add('renew_now', 'onOff', [
                    'label' => 'Renew Now?',
                    'label_attr' => ['class' => 'control-label'],
                    'wrapper' => [
                        'class' => 'form-group period-form-group col-md-3',
                    ],
                    'default_value' => false,
                ]);
            }
        }

        //            ->add('auto_renew', 'onOff', [
//                'label' => trans(
//                    'plugins/real-estate::property.renew_notice',
//                    ['days' => config('plugins.real-estate.real-estate.property_expired_after_x_days')]
//                ),
//                'label_attr' => ['class' => 'control-label'],
//                'default_value' => false,
//                'wrapper' => [
//                    'class' => 'form-group col-md-6 auto-renew-form-group' . (!$this->getModel()->id || $this->getModel()->never_expired == true ? ' hidden' : null),
//                ],
//            ])
        $this->add('rowCloseaccount', 'html', [
            'html' => '</div>',
        ])
            ->add('rowOpenmodal', 'html', [
                'html' => view('plugins/real-estate::partials.checklist_modal'),
            ])
            ->add('rowClosemodal', 'html', [
                'html' => '</div>',
            ]);

        // $this->add('moderation_status', 'customSelect', [
        //     'label' => trans('plugins/real-estate::property.moderation_status'),
        //     'label_attr' => ['class' => 'control-label required font-weight-bold'],
        //     'attr' => [
        //         'class' => 'form-control select-full',
        //     ],
        //     'wrapper' => [
        //         'class' => 'form-group col-md-3  moderation_status d-none',

        //     ],
        //     'choices' => ModerationStatusEnum::labels()
        // ]);

        if ($sellerType == 'None') {
            $this->remove('rowOpenSellerInfo')
                ->remove('SellerInfo')
                ->remove('rowCloseSellerInfo');
        }
    }
}
