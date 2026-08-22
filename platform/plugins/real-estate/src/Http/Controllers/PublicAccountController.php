<?php

namespace Botble\RealEstate\Http\Controllers;

use Assets;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Media\Chunks\Exceptions\UploadMissingFileException;
use Botble\Media\Chunks\Handler\DropZoneUploadHandler;
use Botble\Media\Chunks\Receiver\FileReceiver;
use Botble\Media\Repositories\Interfaces\MediaFileInterface;
use Botble\Media\Services\ThumbnailService;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Repositories\Interfaces\PaymentInterface;
use Botble\Payment\Services\Gateways\PayPalPaymentService;
use Botble\RealEstate\Http\Requests\AvatarRequest;
use Botble\RealEstate\Http\Requests\SettingRequest;
use Botble\RealEstate\Http\Requests\UpdatePasswordRequest;
use Botble\RealEstate\Http\Resources\AccountResource;
use Botble\RealEstate\Http\Resources\ActivityLogResource;
use Botble\RealEstate\Http\Resources\PackageResource;
use Botble\RealEstate\Http\Resources\TransactionResource;
use Botble\RealEstate\Models\Package;
use Botble\RealEstate\Repositories\Interfaces\AccountActivityLogInterface;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Botble\RealEstate\Repositories\Interfaces\PackageInterface;
use Botble\RealEstate\Repositories\Interfaces\TransactionInterface;
use Exception;
use File;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use RvMedia;
use SeoHelper;

class PublicAccountController extends Controller
{
    /**
     * @var AccountInterface
     */
    protected $accountRepository;

    /**
     * @var AccountActivityLogInterface
     */
    protected $activityLogRepository;

    /**
     * @var MediaFileInterface
     */
    protected $fileRepository;

    /**
     * PublicController constructor.
     * @param Repository $config
     * @param AccountInterface $accountRepository
     * @param AccountActivityLogInterface $accountActivityLogRepository
     * @param MediaFileInterface $fileRepository
     */
    public function __construct(
        Repository $config,
        AccountInterface $accountRepository,
        AccountActivityLogInterface $accountActivityLogRepository,
        MediaFileInterface $fileRepository
    ) {
        $this->accountRepository = $accountRepository;
        $this->activityLogRepository = $accountActivityLogRepository;
        $this->fileRepository = $fileRepository;

        Assets::setConfig($config->get('plugins.real-estate.assets'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse|View
     */
    public function getDashboard()
    {
        $user = auth('account')->user();

        SeoHelper::setTitle(auth('account')->user()->getFullName());

        Assets::addScriptsDirectly('vendor/core/plugins/real-estate/js/components.js');

        return view('plugins/real-estate::account.dashboard.index', compact('user'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getSettings()
    {
        SeoHelper::setTitle(trans('plugins/real-estate::account.account_settings'));

        $user = auth('account')->user();

        return view('plugins/real-estate::account.settings.index', compact('user'));
    }

    /**
     * @param SettingRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse|\Illuminate\Http\RedirectResponse
     */
    public function postSettings(SettingRequest $request, BaseHttpResponse $response)
    {
        $year = $request->input('year');
        $month = $request->input('month');
        $day = $request->input('day');

        if ($year && $month && $day) {
            $request->merge(['dob' => implode('-', [$year, $month, $day])]);

            $validator = Validator::make($request->input(), [
                'dob' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return redirect()->route('public.account.settings');
            }
        }

        $this->accountRepository->createOrUpdate($request->except('email'),
            ['id' => auth('account')->user()->getAuthIdentifier()]);

        $this->activityLogRepository->createOrUpdate(['action' => 'update_setting']);

        return $response
            ->setNextUrl(route('public.account.settings'))
            ->setMessage(trans('plugins/real-estate::account.update_profile_success'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getSecurity()
    {
        SeoHelper::setTitle(trans('plugins/real-estate::account.security'));

        return view('plugins/real-estate::account.settings.security');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getPackages()
    {
        SeoHelper::setTitle(trans('plugins/real-estate::account.packages'));

        Assets::addScriptsDirectly('vendor/core/plugins/real-estate/js/components.js');

        return view('plugins/real-estate::account.settings.package');
    }

    /**
     * @return Factory|View
     */
    public function getTransactions()
    {
        SeoHelper::setTitle(trans('plugins/real-estate::account.transactions'));

        Assets::addScriptsDirectly('vendor/core/plugins/real-estate/js/components.js');

        return view('plugins/real-estate::account.settings.transactions');
    }

    /**
     * @param PackageInterface $packageRepository
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetPackages(PackageInterface $packageRepository, BaseHttpResponse $response)
    {
        $account = $this->accountRepository->findOrFail(auth('account')->user()->getAuthIdentifier(),
            ['packages']);

        $packages = $packageRepository->getModel()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->get();

        $packages = $packages->filter(function ($package) use ($account) {
            return $package->account_limit === null || $account->packages->where('id',
                    $package->id)->count() < $package->account_limit;
        });

        return $response->setData([
            'packages' => PackageResource::collection($packages),
            'account'  => new AccountResource($account),
        ]);
    }

    /**
     * @param Request $request
     * @param PackageInterface $packageRepository
     * @param BaseHttpResponse $response
     */
    public function ajaxSubscribePackage(
        Request $request,
        PackageInterface $packageRepository,
        BaseHttpResponse $response,
        TransactionInterface $transactionRepository
    ) {
        $package = $packageRepository->findOrFail($request->input('id'));

        $account = $this->accountRepository->findOrFail(auth('account')->user()->getAuthIdentifier());

        if ($package->account_limit && $account->packages()->where('package_id',
                $package->id)->count() >= $package->account_limit) {
            abort(403);
        }

        if ($package->price) {

            return $response->setData(['next_page' => route('public.account.package.subscribe', $package->id)]);
        }

        $this->savePayment($package, null, $transactionRepository, true);

        return $response
            ->setData(new AccountResource($account->refresh()))
            ->setMessage(trans('plugins/real-estate::package.add_credit_success'));
    }

    /**
     * @param Package $package
     * @param string|null $chargeId
     * @param TransactionInterface $transactionRepository
     * @param bool $force
     * @return bool
     */
    protected function savePayment(Package $package, ?string $chargeId, TransactionInterface $transactionRepository, bool $force = false)
    {
        $payment = app(PaymentInterface::class)->getFirstBy(['charge_id' => $chargeId]);

        if (!$payment && !$force) {
            return false;
        }

        if (($payment && $payment->status == PaymentStatusEnum::COMPLETED) || $force) {
            $account = auth('account')->user();
            $account->credits += $package->number_of_listings;
            $account->save();

            $account->packages()->attach($package);
        }

        $transactionRepository->createOrUpdate([
            'user_id'    => 0,
            'account_id' => auth('account')->user()->getAuthIdentifier(),
            'credits'    => $package->number_of_listings,
            'payment_id' => $payment ? $payment->id : null,
        ]);

        return true;
    }

    /**
     * @param int $id
     * @param PackageInterface $packageRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSubscribePackage(
        $id,
        PackageInterface $packageRepository,
        PaymentInterface $paymentRepository
    )
    {
        $package = $packageRepository->findOrFail($id);

        SeoHelper::setTitle(trans('plugins/real-estate::package.subscribe_package', ['name' => $package->name]));

        $total_price = $package->price;
        $voucher = false;
        if (session('discount')) {
            $package->price = $total_price - session('discount');
        }

        //Create Payment With Pending Status
        $member = auth('account')->user();
        $orderId = 'GEM-' . date('is') . '-' . rand(1000, 9999);
        $paymentData = [
            'amount' => $package->price,
            'currency' => 'PKR',
            'user_id' => $member->id,
            'charge_id' => $orderId,
            'payment_channel' => 'credit_card',
            'order_id' => $orderId,
            'status' => PaymentStatusEnum::PENDING,
            'payment_type' => 'confirm',
            'package_id' => $package->id,
            'user_type' => 'agent'
        ];

        $payment = $paymentRepository->create($paymentData);

        //BankAlfalahPaymentImplementation
        $url = env('HS_URL');
        $bankorderId = $orderId;

        $Key1 = env('KEY1');
        $Key2 = env('KEY2');
        $HS_ChannelId = env('CHANNEL_ID');
        $HS_MerchantId = env('MERCHANT_ID');
        $HS_StoreId = env('STORE_ID');
        $HS_IsRedirectionRequest = 0;
        $HS_ReturnURL = route('package.callback');
        $HS_MerchantHash = env('MERCHANT_HASH');
        $HS_MerchantUsername = env('MERCHANT_USERNAME');
        $HS_MerchantPassword = env('MERCHANT_PASSWORD');
        $HS_TransactionReferenceNumber = $bankorderId;
        $TransactionTypeId = "3";
        $TransactionAmount = $package->price;

        $cipher = "aes-128-cbc";

        $mapString =
            "HS_ChannelId=$HS_ChannelId"
            . "&HS_IsRedirectionRequest=$HS_IsRedirectionRequest"
            . "&HS_MerchantId=$HS_MerchantId"
            . "&HS_StoreId=$HS_StoreId"
            . "&HS_ReturnURL=$HS_ReturnURL"
            . "&HS_MerchantHash=$HS_MerchantHash"
            . "&HS_MerchantUsername=$HS_MerchantUsername"
            . "&HS_MerchantPassword=$HS_MerchantPassword"
            . "&HS_TransactionReferenceNumber=$HS_TransactionReferenceNumber";


        $cipher_text = openssl_encrypt($mapString, $cipher, $Key1, OPENSSL_RAW_DATA, $Key2);
        $hashRequest = base64_encode($cipher_text);


        $fields = [
            "HS_ChannelId" => $HS_ChannelId,
            "HS_IsRedirectionRequest" => $HS_IsRedirectionRequest,
            "HS_MerchantId" => $HS_MerchantId,
            "HS_StoreId" => $HS_StoreId,
            "HS_ReturnURL" => $HS_ReturnURL,
            "HS_MerchantHash" => $HS_MerchantHash,
            "HS_MerchantUsername" => $HS_MerchantUsername,
            "HS_MerchantPassword" => $HS_MerchantPassword,
            "HS_TransactionReferenceNumber" => $HS_TransactionReferenceNumber,
            "HS_RequestHash" => $hashRequest
        ];

        $fields_string = http_build_query($fields);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        $handshake = json_decode($result);

        $AuthToken = $handshake->AuthToken;

        $RequestHash1 = NULL;
        $Currency = "PKR";
        $IsBIN = 0;

        $mapStringSSo =
            "AuthToken=$AuthToken"
            . "&RequestHash=$RequestHash1"
            . "&ChannelId=$HS_ChannelId"
            . "&Currency=$Currency"
            . "&IsBIN=$IsBIN"
            . "&ReturnURL=$HS_ReturnURL"
            . "&MerchantId=$HS_MerchantId"
            . "&StoreId=$HS_StoreId"
            . "&MerchantHash=$HS_MerchantHash"
            . "&MerchantUsername=$HS_MerchantUsername"
            . "&MerchantPassword=$HS_MerchantPassword"
            . "&TransactionTypeId=3"
            . "&TransactionReferenceNumber=$HS_TransactionReferenceNumber"
            . "&TransactionAmount=$TransactionAmount";


        $cipher_text = openssl_encrypt($mapStringSSo, $cipher, $Key1, OPENSSL_RAW_DATA, $Key2);
        $hashRequest1 = base64_encode($cipher_text);

        $ssoUrl = env('SSO_URL');

        SeoHelper::setTitle(trans('plugins/real-estate::package.subscribe_package', ['name' => $package->name]));
        //return Theme::scope('real-estate.member.wanted',$data)->render();
        return view('plugins/real-estate::account.checkout', compact(
            'package',
            'voucher',
            'total_price',
            'AuthToken',
            'hashRequest1',
            'HS_ChannelId',
            'HS_ReturnURL',
            'HS_MerchantId',
            'HS_StoreId',
            'HS_MerchantHash',
            'HS_MerchantUsername',
            'HS_MerchantPassword',
            'HS_TransactionReferenceNumber',
            'TransactionAmount',
            'Currency',
            'IsBIN',
            'TransactionTypeId',
            'ssoUrl'
        ));
    }

    public function packageCallback(
        BaseHttpResponse     $response,
        PaymentInterface     $paymentRepository,
        TransactionInterface $transactionRepository,
        PackageInterface     $packageRepository
    )
    {
        $orderId = $_GET['O'];
        $transactionStatus = $_GET['TS'];

        try {
            if ($transactionStatus == 'P') {

//                $payment = $paymentRepository->getFirstBy(['charge_id' => $orderId]);
                $payment = $paymentRepository->getLastRecord();
                $package = $packageRepository->findById($payment->package_id);

                $account = auth('account')->user();
                $account->credits += $package->number_of_listings;
                $account->save();

//                $account->packages()->attach($package);

                //Update payment data
                DB::table('payments')->where('id', '=', $payment->id)->update([
                    'status' => PaymentStatusEnum::COMPLETED
                ]);

                $transactionRepository->createOrUpdate([
                    'user_id' => $account->id,
                    'account_id' => auth('account')->user()->getAuthIdentifier(),
                    'credits' => $package->number_of_listings,
                    'payment_id' => $payment ? $payment->id : null,
                    'user_type' => $payment->user_type
                ]);

                $message = 'Your payment has been received. Credits have been added to your account';

                event(new CreatedContentEvent(PACKAGE_MODULE_SCREEN_NAME, $payment, $package));

                return $response
                    ->setNextUrl(route('public.account.packages'))
                    ->setMessage($message);
            } else {
                $message = 'Something went wrong with the payment. Please try again';

                return $response
                    ->setNextUrl(route('public.account.packages'))
                    ->setError()
                    ->setMessage($message);
            }
        } catch (Exception $e) {
            $message = 'Something went wrong with the payment. Please try again';

            return $response
                ->setNextUrl(route('public.account.packages'))
                ->setError()
                ->setMessage($message);
        }
    }

    /**
     * @param int $packageId
     * @param Request $request
     * @param PayPalPaymentService $payPalService
     * @param \Botble\RealEstate\Repositories\Interfaces\PackageInterface $packageRepository
     * @param \Botble\RealEstate\Repositories\Interfaces\TransactionInterface $transactionRepository
     * @return BaseHttpResponse
     */
    public function getPackageSubscribeCallback(
        $packageId,
        Request $request,
        PayPalPaymentService $payPalService,
        PackageInterface $packageRepository,
        TransactionInterface $transactionRepository,
        BaseHttpResponse $response
    ) {
        $package = $packageRepository->findOrFail($packageId);

        if ($request->input('type') == PaymentMethodEnum::PAYPAL) {
            $validator = Validator::make($request->input(), [
                'amount'   => 'required|numeric',
                'currency' => 'required',
            ]);

            if ($validator->fails()) {
                return $response->setError()->setMessage($validator->getMessageBag()->first());
            }

            $paymentStatus = $payPalService->getPaymentStatus($request);
            if ($paymentStatus && $paymentStatus->state === 'approved') {
                $payPalService->afterMakePayment($request);

                $this->savePayment($package, $request->input('paymentId'), $transactionRepository);

                return $response
                    ->setNextUrl(route('public.account.packages'))
                    ->setMessage(trans('plugins/real-estate::package.add_credit_success'));
            }

            return $response
                ->setError()
                ->setNextUrl(route('public.account.packages'))
                ->setMessage($payPalService->getErrorMessage());
        }

        $this->savePayment($package, $request->input('charge_id'), $transactionRepository);

        return $response
            ->setNextUrl(route('public.account.packages'))
            ->setMessage(trans('plugins/real-estate::package.add_credit_success'));
    }

    /**
     * @param UpdatePasswordRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postSecurity(UpdatePasswordRequest $request, BaseHttpResponse $response)
    {
        $this->accountRepository->update(['id' => auth('account')->user()->getAuthIdentifier()], [
            'password' => bcrypt($request->input('password')),
        ]);

        $this->activityLogRepository->createOrUpdate(['action' => 'update_security']);

        return $response->setMessage(trans('plugins/real-estate::dashboard.password_update_success'));
    }

    /**
     * @param AvatarRequest $request
     * @param ThumbnailService $thumbnailService
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postAvatar(AvatarRequest $request, ThumbnailService $thumbnailService, BaseHttpResponse $response)
    {
        try {
            $account = auth('account')->user();
               //  dd($account);exit;
            $result = RvMedia::handleUpload($request->file('avatar_file'), 0, 'accounts');
          //  dd($result);exit;
            if ($result['error'] != false) {
                return $response->setError()->setMessage($result['message']);
            }

            $avatarData = json_decode($request->input('avatar_data'));

            $file = $result['data'];

            $thumbnailService
                ->setImage(Storage::url($file->url))
                ->setSize((int)$avatarData->width, (int)$avatarData->height)
                ->setCoordinates((int)$avatarData->x, (int)$avatarData->y)
                ->setDestinationPath(File::dirname($file->url))
                ->setFileName(File::name($file->url) . '.' . File::extension($file->url))
                ->save('crop');
           // dd($file);exit;
            $this->fileRepository->forceDelete(['id' => $account->avatar_id]);

            $account->avatar_id = $file->id;

            $this->accountRepository->createOrUpdate($account);

            $this->activityLogRepository->createOrUpdate([
                'action' => 'changed_avatar',
            ]);

            return $response
                ->setMessage(trans('plugins/real-estate::dashboard.update_avatar_success'))
                ->setData(['url' => Storage::url($file->url)]);
        } catch (Exception $ex) {
            return $response
                ->setError()
                ->setMessage($ex->getMessage());
        }
    }

    /**
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function getActivityLogs(BaseHttpResponse $response)
    {
        $activities = $this->activityLogRepository->getAllLogs(auth('account')->user()->getAuthIdentifier());

        Assets::addScriptsDirectly('vendor/core/plugins/real-estate/js/components.js');

        return $response->setData(ActivityLogResource::collection($activities))->toApiResponse();
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse|\Illuminate\Http\JsonResponse
     */
    public function postUpload(Request $request, BaseHttpResponse $response)
    {
        if (setting('media_chunk_enabled') != '1') {
            $validator = Validator::make($request->all(), [
                'file.0' => 'required|image|mimes:jpg,jpeg,png',
            ]);

            if ($validator->fails()) {
                return $response->setError()->setMessage($validator->getMessageBag()->first());
            }

            $result = RvMedia::handleUpload(Arr::first($request->file('file')), 0, 'accounts');

            if ($result['error']) {
                return $response->setError(true)->setMessage($result['message']);
            }

            return $response->setData($result['data']);
        }

        try {
            // Create the file receiver
            $receiver = new FileReceiver('file', $request, DropZoneUploadHandler::class);
            // Check if the upload is success, throw exception or return response you need
            if ($receiver->isUploaded() === false) {
                throw new UploadMissingFileException;
            }
            // Receive the file
            $save = $receiver->receive();
            // Check if the upload has finished (in chunk mode it will send smaller files)
            if ($save->isFinished()) {
                $result = RvMedia::handleUpload($save->getFile(), 0, 'accounts');

                if ($result['error'] == false) {
                    return $response->setData($result['data']);
                }

                return $response->setError(true)->setMessage($result['message']);
            }
            // We are in chunk mode, lets send the current progress
            $handler = $save->handler();
            return response()->json([
                'done'   => $handler->getPercentageDone(),
                'status' => true,
            ]);
        } catch (Exception $exception) {
            return $response->setError(true)->setMessage($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function postUploadFromEditor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'upload' => 'required|image|mimes:jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return response('<script>alert("' . trans('core/media::media.can_not_detect_file_type') . '")</script>')
                ->header('Content-Type', 'text/html');
        }

        $result = RvMedia::handleUpload($request->file('upload'), 0, 'accounts');

        if ($result['error'] == false) {
            $file = $result['data'];
            return response('<script>parent.setImageValue("' . RvMedia::getImageUrl($file->url) . '"); </script>')
                ->header('Content-Type', 'text/html');
        }

        return response('<script>alert("' . Arr::get($result, 'message') . '")</script>')
            ->header('Content-Type', 'text/html');
    }

    /**
     * @param \Botble\RealEstate\Repositories\Interfaces\TransactionInterface $transactionRepository
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetTransactions(TransactionInterface $transactionRepository, BaseHttpResponse $response)
    {
        $transactions = $transactionRepository->advancedGet([
            'condition' => [
                'account_id' => auth('account')->user()->id,
            ],
            'paginate'  => [
                'per_page'      => 10,
                'current_paged' => 1,
            ],
            'order_by'  => ['created_at' => 'DESC'],
            'with'      => ['payment', 'user'],
        ]);

        return $response->setData(TransactionResource::collection($transactions))->toApiResponse();
    }
    public function getConsultCount(Request $request){

        $count=Auth::guard('account')->user()->getConsults();
        $arr=array('status'=>true,'count'=>$count);
        echo json_encode($arr);

    }

}
