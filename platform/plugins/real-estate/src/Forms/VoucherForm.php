<?php

namespace Botble\RealEstate\Forms;

use Assets;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\RealEstate\Repositories\Interfaces\CurrencyInterface;
use Botble\RealEstate\Http\Requests\PackageRequest;
use BeyondCode\Vouchers\Models\Voucher;
use Botble\RealEstate\Repositories\Interfaces\PackageInterface;
use Botble\RealEstate\Repositories\Interfaces\VoucherInterface;

class VoucherForm extends FormAbstract
{
    /**
     * @var CurrencyInterface
     */
    protected $currencyRepository;
    protected $packageRepository;

    /**
     * PackageForm constructor.
     * @param CurrencyInterface $currencyRepository
     */
    public function __construct(CurrencyInterface $currencyRepository,PackageInterface $packageRepository)
    {
        parent::__construct();
        $this->currencyRepository = $currencyRepository;
        $this->packageRepository = $packageRepository;
    }

    /**
     * @return mixed|void
     * @throws \Throwable
     */
    public function buildForm()
    {

        Assets::addScripts(['input-mask']);

        $currencies = $this->currencyRepository->pluck('re_currencies.title', 're_currencies.id');
        $package = $this->packageRepository->pluck('re_packages.name', 're_packages.id');
        $this
            ->setupModel(new Voucher())

            ->withCustomFields()

            ->add('rowOpen1', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('code', 'text', [
                'label'      => trans('core/base::forms.code'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.auto_code'),
                    'data-counter' => 120,
                  // 'readonly' => true,
                ],
                'wrapper'    => [
                    'class' => 'form-group col-md-4',
                ]
            ])
            ->add('data', 'text', [
                'label'      => trans('core/base::forms.discount_percent'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper'    => [
                    'class' => 'form-group col-md-4',
                ],
                'attr'       => [
                    'id'          => 'percent-save-number',
                    'placeholder' => trans('plugins/real-estate::package.percent_save'),
                    'class'       => 'form-control input-mask-number',
                ],
            ])
            ->add('model_id', 'customSelect', [
                'label'      => trans('core/base::forms.package'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper'    => [
                    'class' => 'form-group col-md-6',
                ],
                'attr'       => [
                    'class' => 'form-control select-full',
                ],
                'wrapper'    => [
                    'class' => 'form-group col-md-4',
                ],
                'choices'    => $package,
            ])
            ->add('rowClose1', 'html', [
                'html' => '</div>',
            ])
            ->add('rowOpen2', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('expires_at', 'date', [
                'label'      => trans('core/base::forms.expires_at'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper'    => [
                    'class' => 'form-group col-md-4',
                ],

            ])


            ->add('rowClose2', 'html', [
                'html' => '</div>',
            ])

            ->add('status', 'customSelect', [
                'label'      => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'class' => 'form-control select-full',
                ],
                'choices'    => BaseStatusEnum::labels(),
            ])
            ->setBreakFieldPoint('status');
    }
}
