<?php

namespace Botble\RealEstate\Http\Controllers;

use App;
use App\Models\Rating;
use App\Models\table_properties_check_lists;
use Assets;
use Auth;
use BeyondCode\Vouchers\Models\Voucher;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Location\Repositories\Interfaces\CityAreaInterface;
use Botble\Location\Repositories\Interfaces\CityInterface;
use Botble\Media\Chunks\Exceptions\UploadMissingFileException;
use Botble\Media\Chunks\Handler\DropZoneUploadHandler;
use Botble\Media\Chunks\Receiver\FileReceiver;
use Botble\Page\Models\Page;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Repositories\Interfaces\PaymentInterface;
use Botble\Payment\Services\Gateways\PayPalPaymentService;
use Botble\RealEstate\Forms\AccountPropertyForm;
use Botble\RealEstate\Forms\GeneralPropertyForm;
use Botble\RealEstate\Forms\MemberPropertyForm;
use Botble\RealEstate\Http\Requests\MemberSettingRequest;
use Botble\RealEstate\Http\Requests\PropertyRequest;
use Botble\RealEstate\Http\Requests\UpdatePasswordRequest;
use Botble\RealEstate\Http\Resources\ActivityLogResource;
use Botble\RealEstate\Http\Resources\MemberResource;
use Botble\RealEstate\Http\Resources\PackageResource;
use Botble\RealEstate\Http\Resources\TransactionResource;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Currency;
use Botble\RealEstate\Models\Member;
use Botble\RealEstate\Models\Package;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Repositories\Interfaces\AccountActivityLogInterface;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Botble\RealEstate\Repositories\Interfaces\CategoryInterface;
use Botble\RealEstate\Repositories\Interfaces\MemberActivityLogInterface;
use Botble\RealEstate\Repositories\Interfaces\MemberInterface;
use Botble\RealEstate\Repositories\Interfaces\PackageInterface;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\RealEstate\Repositories\Interfaces\TransactionInterface;
use Botble\RealEstate\Services\SaveFacilitiesService;
use Botble\RealEstate\Tables\AccountPropertyTable;
use Botble\RealEstate\Tables\MemberPropertyTable;
use Botble\Setting\Supports\SettingStore;
use Carbon\Carbon;
use EmailHandler;
use Exception;
use File;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Log;
use RvMedia;
use SeoHelper;
use Theme;
use URL;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Botble\Location\Models\State;
use Botble\Location\Models\City;
use Botble\Location\Repositories\Interfaces\CountryInterface;
use Botble\Location\Repositories\Interfaces\StateInterface;
class GeneralPropertyController extends Controller
{
    /**
     * @var AccountInterface
     */
    protected $memberRepository;
    protected $cityRepository;
    protected $cityAreaRepository;
protected $countryRepository;
protected $stateRepository;
    /**
     * @var PropertyInterface
     */
    protected $propertyRepository;

    protected $projectRepository;

    /**
     * @var AccountActivityLogInterface
     */
    protected $activityLogRepository;
    protected $memberLogRepository;
    protected $categoryRepository;

    /**
     * PublicController constructor.
     * @param Repository $config
     * @param AccountInterface $memberRepository
     * @param PropertyInterface $propertyRepository
     * @param AccountActivityLogInterface $accountActivityLogRepository
     */
    public function __construct(
        Repository                  $config,
        MemberInterface             $memberRepository,
        PropertyInterface           $propertyRepository,
        AccountActivityLogInterface $accountActivityLogRepository,
        CategoryInterface           $categoryRepository,
        CityInterface               $cityRepository,
        CountryInterface           $countryRepository,
        StateInterface              $stateRepository,
        CityAreaInterface           $cityAreaRepository,
        MemberActivityLogInterface  $memberActivityLogRepository,
        ProjectInterface            $projectRepository
    )
    {
        $this->memberRepository = $memberRepository;
        $this->propertyRepository = $propertyRepository;
        $this->cityRepository = $cityRepository;
        $this->cityAreaRepository = $cityAreaRepository;
          $this->countryRepository = $countryRepository;
          $this->stateRepository = $stateRepository;
        $this->categoryRepository = $categoryRepository;
        $this->activityLogRepository = $accountActivityLogRepository;
        $this->memberLogRepository = $memberActivityLogRepository;
        $this->projectRepository = $projectRepository;

        Assets::setConfig($config->get('plugins.real-estate.assets'));

    }

    /**
     * @param Request $request
     * @param AccountPropertyTable $propertyTable
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View|\Response
     * @throws \Throwable
     */
    protected function guard()
    {
        return auth('member');
    }

    public function index(AccountPropertyTable $propertyTable)
    {
        SeoHelper::setTitle(__('Properties'));

        return $propertyTable->render('plugins/real-estate::account.table.base');
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     * @throws \Throwable
     */
    public function create(FormBuilder $formBuilder)
    {
        if (auth('member')->user() && !auth('account')->user()) {
            return redirect()->route('public.member.properties.create');
        } else if (!auth('member')->user() && auth('account')->user()) {
            return redirect()->route('public.account.properties.create');
        }

        SeoHelper::setTitle(__('Add a property'));

        return $formBuilder->create(GeneralPropertyForm::class)->renderForm();
    }

    /**
     * @param PropertyRequest $request
     * @param BaseHttpResponse $response
     * @param AccountInterface $accountRepository
     * @param SaveFacilitiesService $saveFacilitiesService
     * @return BaseHttpResponse
     */
    public function store(PropertyRequest $request, BaseHttpResponse $response, AccountInterface $accountRepository, SaveFacilitiesService $saveFacilitiesService, MemberInterface $memberRepository)
    {
        $request->merge(['expire_date' => now()->addDays(config('plugins.real-estate.real-estate.property_expired_after_x_days'))]);
        $agent_id = $request['author_id_hidden'] ? $request['author_id_hidden'] : null;
        $member_id = null;
        $is_already_member = false;

        if ($request['member_status'] == "new_user") {
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|string|min:3|max:100|regex:^[a-zA-Z]{3,}(?: [a-zA-Z]+){0,2}$^',
                'new_email' => 'required|email|string',
                'mobile_number' => ['required', 'regex:/^\+?[1-9][0-9]{7,14}$/'],
                'new_password' => 'required|min:6',
                'terms' => 'required|accepted',
            ], [
                'terms.required' => 'Please accept GEM terms & conditions',
                'mobile_number.regex' => 'The phone number format is invalid. It must be a valid international number, e.g., +1234567890.',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->all(), 'message' => 'Invalid data format']);
            } else if (Member::where('email', $request['new_email'])->first()) {
                $is_already_member = true;
                $error = array('status' => false, 'message' => 'Email already exists.');
                echo json_encode($error);
                die;
            } else {
                $arr = array('full_name' => $request['full_name'], 'email' => $request['new_email'], 'mobile_no' => $request['mobile_number'], 'password' => Hash::make($request['new_password']));
            }
        } else {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|string',
                'password' => 'required|min:6',
                'terms' => 'required|accepted',
            ], [
                'terms.required' => 'Please accept GEM terms & conditions'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->all(), 'message' => 'Invalid data format']);
            }

            $existing_email = Member::where('email', $request['email'])->first();
            if (($existing_email) && (Hash::check($request['password'], $existing_email->password)) == true) {
                $member_id = $existing_email->id;
                $is_already_member = true;
            } else {
                $existing_email = array('status' => false, 'message' => 'Invalid email or password');
                echo json_encode($existing_email);
                die;

            }

        }

        $jsonArr = array();

        if ($request->hasFile('documents')) {
            $files = $request->file('documents');
            $i = 0;
            foreach ($files as $key => $file) {
                $document_id = $request['document_ids'][$key];
                $name = $document_id . '-document-' . time() . uniqid() . '.' . $file->extension();
                $file->storeAs('Documents', $name);
                $jsonArr[$i]['id'] = $document_id;
                $jsonArr[$i]['path'] = 'Documents/' . $name;
                $i++;

            }
        }
        $status = 'selling';
        if ($request['type'] == "rent")
            $status = 'renting';

        $request['documents'] = json_encode($jsonArr);
        $area_value = str_replace(',', '', $request['square']);
        $area_units = $request['area_units'];
        unset($request['square']);
        $sqFeet = getSqFeet($area_value, $area_units);
        $request['square'] = $sqFeet;
        if ($request && $request['images']) {
            if ($member_id != null) {
                $is_already_member = true;
            } else {
                $member_id = Member::create($arr)->id;
                $is_already_member = false;
            }
            $property = $this->propertyRepository->createOrUpdate(array_merge($request->input(), [
                'author_id' => $agent_id,
                'member_id' => $member_id,
                'status' => $status,
                'author_type' => $agent_id ? Account::class : Member::class,
            ]));
        }

        if ($property) {
            $property->features()->sync($request->input('features', []));

            $saveFacilitiesService->execute($property, $request->input('facilities', []));
        }

        event(new CreatedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $request, $property));

        if ($request['member_status'] == "new_user") {
            $arr = array('email' => $request['new_email']);
            $data_new = Member::where($arr)->first();
            if (Auth::guard('member')->attempt(['email' => $data_new->email, 'password' => $request->new_password]/*, $request->get('remember')*/)) {

                if ($property) {
                    $this->memberLogRepository->createOrUpdate([
                        'action' => 'new_member_create_property',
                        'reference_name' => $property->name,
                        'reference_url' => route('general-add-property'),
                    ]);

                    $data = array('route_name' => 'member.dashboard', 'status' => true, 'message' => 'Property Added Successfully!');
                    echo json_encode($data);
                    $response->setMessage(trans('core/base::notices.create_success_message'));
                }
            } else {
                $data = array('status' => false, 'message' => 'unable to add property.');
                echo json_encode($data);
                $response->setMessage(trans('core/base::notices.create_success_message'));
            }
        } else {
            if (Auth::guard('member')->attempt(['email' => $request->email, 'password' => $request->password]/*, $request->get('remember')*/)) {
                if ($property) {
                    $this->memberLogRepository->createOrUpdate([
                        'action' => 'new_member_create_property',
                        'reference_name' => $property->name,
                        'reference_url' => route('public.member.properties.edit', $property->id),
                    ]);
                    $data = array('route_name' => 'member.dashboard', 'status' => true, 'message' => 'Property Added Successfully!');
                    echo json_encode($data);
                    $response->setMessage(trans('core/base::notices.create_success_message'));
                }
            } else {
                $data = array('status' => false, 'message' => 'unable to add property.');
                echo json_encode($data);
                $response->setMessage(trans('core/base::notices.create_success_message'));
            }
        }

    }

    /**
     * @param int $id
     * @param FormBuilder $formBuilder
     * @param Request $request
     * @return string
     *
     * @throws \Throwable
     */
    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        $property = $this->propertyRepository->getFirstBy([
            'id' => $id,
            'member_id' => auth('member')->user()->getAuthIdentifier()
            // 'author_type' => Member::class,
        ]);

        if (!$property) {
            abort(404);
        }

        event(new BeforeEditContentEvent($request, $property));

        SeoHelper::setTitle(trans('plugins/real-estate::property.edit') . ' "' . $property->name . '"');

        return $formBuilder
            ->create(AccountPropertyForm::class, ['model' => $property])
            ->renderForm();
    }

    /**
     * @param int $id
     * @param PropertyRequest $request
     * @param BaseHttpResponse $response
     * @param SaveFacilitiesService $saveFacilitiesService
     * @return BaseHttpResponse
     *
     */
    public function update($id, PropertyRequest $request, BaseHttpResponse $response, SaveFacilitiesService $saveFacilitiesService)
    {
        $property = $this->propertyRepository->getFirstBy([
            'id' => $id,
            'member_id' => auth('member')->user()->getAuthIdentifier()
            // 'author_type' => Member::class,
        ]);

        if (!$property) {
            abort(404);
        }
        //dd($request);exit;

        $old_category_id = $property->category_id;
        $old_documents = json_decode($property->documents);
        $property->fill($request->except(['expire_date']));
        $area_value = $request['square'];
        $area_units = $request['area_units'];
        $old_arr = (array)$old_documents;
        $jsonArr = array();

        $ids = array_column($old_arr, 'id');
        if ($request->hasFile('documents')) {
            $files = $request->file('documents');
            $i = 0;

            foreach ($files as $key => $file) {
                //$key;
                $document_id = $request['document_ids'][$key];
                $array_index = array_search($document_id, $ids);


                if ($array_index != "") {
                    $path = $old_arr[$array_index]->path;
                    if (Storage::exists($path)) {
                        Storage::delete($path);
                        unset($old_arr[$array_index]);
                    }
                }
                //}

                /*$name = $ids[$key] . time() . uniqid().'.'.$file->extension();*/
                $name = $document_id . '-document-' . time() . uniqid() . '.' . $file->extension();
                $file->storeAs('Documents', $name);
                $jsonArr[$i]['id'] = $document_id;
                $jsonArr[$i]['path'] = 'Documents/' . $name;
                $i++;

            }


        }
        if ($old_category_id == $request['category_id']) {
            $update_arr = array_merge($old_arr, $jsonArr);
            $keys = array_column($update_arr, 'id');
            array_multisort($keys, SORT_ASC, $update_arr);
        } else {
            //updating checklist to empty
            $arr = array('document_checklist' => '', 'is_verify' => 0);
            $resupdate = table_properties_check_lists::where('property_id', $id)->update($arr);
            $update_arr = $jsonArr;
            $keys = array_column($update_arr, 'id');
            array_multisort($keys, SORT_ASC, $update_arr);
        }
        $status = 'selling';
        if ($request['type'] == "rent")
            $status = 'renting';
        else
            $status = 'selling';

        $property->status = $status;
        $property->documents = json_encode($update_arr);
        unset($request['square']);
        $sqFeet = getSqFeet($area_value, $area_units);
        $property->square = $sqFeet;
        $this->propertyRepository->createOrUpdate($property);

        $property->features()->sync($request->input('features', []));

        $saveFacilitiesService->execute($property, $request->input('facilities', []));

        event(new UpdatedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $request, $property));

        $this->memberLogRepository->createOrUpdate([
            'action' => 'update_property',
            'reference_name' => $property->name,
            'reference_url' => route('public.member.properties.edit', $property->id),
        ]);

        return $response
            ->setPreviousUrl(route('public.account.properties.index'))
            ->setNextUrl(route('public.account.properties.edit', $property->id))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function destroy($id, BaseHttpResponse $response)
    {
        $property = $this->propertyRepository->getFirstBy([
            'id' => $id,
            'member_id' => auth('member')->user()->getAuthIdentifier()
            // 'author_type' => Member::class,
        ]);

        if (!$property) {
            abort(404);
        }

        $this->propertyRepository->delete($property);

        $this->memberLogRepository->createOrUpdate([
            'action' => 'delete_property',
            'reference_name' => $property->name,
        ]);

        return $response->setMessage(__('Delete property successfully!'));
    }

    /**
     * @param $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function renew($id, BaseHttpResponse $response)
    {
        $job = $this->propertyRepository->findOrFail($id);

        $account = auth('member')->user();

        if ($account->credits < 1) {
            return $response->setError(true)->setMessage(__('You don\'t have enough credit to renew this property!'));
        }

        $job->expire_date = $job->expire_date->addDays(config('plugins.real-estate.real-estate.property_expired_after_x_days'));
        $job->save();

        $account->credits--;
        $account->save();

        return $response->setMessage(__('Renew property successfully'));
    }

    public function login()
    {
        SeoHelper::setTitle(trans('plugins/real-estate::account.login'));

        if (view()->exists(Theme::getThemeNamespace() . '::views.real-estate.member.auth.login')) {

            return Theme::scope('real-estate.member.auth.login')->render();
        }

        return view('plugins/real-estate::member.auth.login');
        //return view('plugins/real-estate::member.auth.login');

    }

    public function attemptLogin(Request $request)
    {
      $member = Member::where('email', $request->email)->first();

if (!$member) {
    return back()->withErrors(['Invalid email'])->withInput();
}

if (!$member->email_verified) {
    return back()->withErrors([
        'Please verify your email before logging in.'
    ]);
}

        if (Auth::guard('member')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect('/member/dashboard');
        }

        return back()->withErrors(['Invalid password'])->withInput();
    }

    public function register()
    {
        SeoHelper::setTitle(trans('plugins/real-estate::account.register'));

        if (view()->exists(Theme::getThemeNamespace() . '::views.real-estate.member.auth.register')) {

            return Theme::scope('real-estate.member.auth.register')->render();
        }

        return view('plugins/real-estate::member.auth.register');

    }

    protected function createMember(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|min:3|max:100|regex:^[a-zA-Z]{3,}(?: [a-zA-Z]+){0,2}$^',
            'email' => 'required|email|string',
            'mobile_no' => ['required', 'regex:/^\+?[1-9][0-9]{7,14}$/'],
            'password' => 'required|min:6',
        ], [
            'mobile_no.regex' => 'The phone number format is invalid. It must be a valid international number, e.g., +1234567890.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        if (Member::where('email', $request['email'])->first()) {
            return redirect()->back()->with(array('error_msg' => 'Email already exists.'));
        } else {
          $token = Str::random(64);

$member = Member::create([
    'full_name' => $request['full_name'],
    'email' => $request['email'],
    'mobile_no' => $request['mobile_no'],
    'password' => Hash::make($request['password']),
    'verification_token' => $token,
    'email_verified' => 0,
]);
        }

        // EmailHandler::setModule('real-estate')
        //     ->addVariables([
        //         'member_name' => 'Member Name',
        //         'login_url' => 'Login'
        //     ])
        //     ->setVariableValues([
        //         'member_name' => $member->full_name,
        //         'login_url' => route('member.login')
        //     ])
        //     ->sendUsingTemplate('memberregistered', $member->email, [], false, 'plugins', 'Account Created');
$link = url('/member/verify/' . $token);

Mail::send(
    'plugins/real-estate::account.emails.verify-email',
    [
        'link' => $link,
    ],
    function ($message) use ($member) {
        $message->to($member->email)
                ->subject('Verify Your Email');
    }
  );

  return redirect()->intended('member-login')
    ->with('success_msg', 'Registration successful. Please check your email to verify your account.');    }

    public function dashboard()
    {
        $user = auth('member')->user();

        SeoHelper::setTitle(auth('member')->user()->full_name);

        Assets::addScriptsDirectly('vendor/core/plugins/real-estate/js/components.js');

        return view('plugins/real-estate::member.dashboard.index', compact('user'));

    }

    public function properties(MemberPropertyTable $propertyTable)
    {
        SeoHelper::setTitle(__('Properties'));
        return $propertyTable->render('plugins/real-estate::member.table.base');
    }

    public function create_property(FormBuilder $formBuilder)
    {
        if (!auth('member')->user()->canPost()) {
            abort(403);
        }

        SeoHelper::setTitle(__('Add a property'));

        return $formBuilder->create(MemberPropertyForm::class)->renderForm();
    }

    public function save_property(PropertyRequest $request, BaseHttpResponse $response, AccountInterface $accountRepository, SaveFacilitiesService $saveFacilitiesService, MemberInterface $memberRepository)
    {
        if (!auth('member')->user()->canPost()) {
            abort(403);
        }

        $request->merge(['expire_date' => now()->addDays(config('plugins.real-estate.real-estate.property_expired_after_x_days'))]);

        /**
         * @var Property $property
         */
        $jsonArr = array();

        if ($request->hasFile('documents')) {
            $files = $request->file('documents');
            $i = 0;
            foreach ($files as $key => $file) {
                $document_id = $request['document_ids'][$key];
                $name = $document_id . '-document-' . time() . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('Documents', $name);
                $jsonArr[$i]['id'] = $document_id;
                $jsonArr[$i]['path'] = 'Documents/' . $name;
                $i++;
            }
        }

        $request['documents'] = json_encode($jsonArr);
        $area_value = $request['square'];
        $area_units = $request['area_units'];
        unset($request['square']);
        $sqFeet = getSqFeet($area_value, $area_units);
        $request['square'] = $sqFeet;

        $agent_id = $request['author_id'];
        $status = 'selling';
        if ($request['type'] == "rent")
            $status = 'renting';
        else
            $status = 'selling';

        $property = $this->propertyRepository->createOrUpdate(array_merge($request->input(), [
            'author_id' => $agent_id,
            'member_id' => auth('member')->user()->getAuthIdentifier(),
            'status' => $status,
            'author_type' => $agent_id > 0 ? Account::class : Member::class
        ]));

        if ($property) {
            $property->features()->sync($request->input('features', []));

            $saveFacilitiesService->execute($property, $request->input('facilities', []));
        }

        event(new CreatedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $request, $property));

        $this->memberLogRepository->createOrUpdate([
            'action' => 'create_property',
            'reference_name' => $property->name,
            'reference_url' => route('public.member.properties.edit', $property->id),
        ]);

        $member = $memberRepository->findOrFail(auth('member')->user()->getAuthIdentifier());

        //Send Email

        $variables = [
            'name' => 'Name',
            'property_url' => 'Property Url',
            'by' => 'By',
            'title' => 'Title',
            'action' => 'Action'
        ];

        //send to self
        EmailHandler::setModule('real-estate')
            ->addVariables($variables)
            ->setVariableValues([
                'name' => $member->full_name,
                'property_url' => route('public.member.properties.edit', ['id' => $property->id]),
                'by' => 'you',
                'title' => $property->name,
                'action' => 'created'
            ])
            ->sendUsingTemplate('propertymodify', $member->email, [], false, 'plugins', 'Property Created');

        //Email to Agent if Present
        if ($property->author_id) {
            $author = $accountRepository->findOrFail($property->author_id);

            EmailHandler::setModule('real-estate')
                ->addVariables($variables)
                ->setVariableValues([
                    'name' => $author->first_name . ' ' . $author->last_name,
                    'property_url' => route('public.account.properties.edit', ['property' => $property->id]),
                    'by' => $member->full_name,
                    'title' => $property->name,
                    'action' => 'created'
                ])
                ->sendUsingTemplate('propertymodify', $author->email, [], false, 'plugins', 'Property Created');
        }

        //send to admin
        EmailHandler::setModule('real-estate')
            ->addVariables($variables)
            ->setVariableValues([
                'name' => 'Admin',
                'property_url' => route('property.edit', ['property' => $property->id]),
                'by' => $member->full_name,
                'title' => $property->name,
                'action' => 'created'
            ])
            ->sendUsingTemplate('propertymodify', 'admin@botble.com', [], false, 'plugins', 'Property Created');

        return $response
            ->setPreviousUrl(route('public.member.properties.index'))
            ->setNextUrl(route('public.member.properties.edit', $property->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit_property($id, FormBuilder $formBuilder, Request $request)
    {
        $property = $this->propertyRepository->getFirstBy([
            'id' => $id,
            'member_id' => auth('member')->user()->getAuthIdentifier()
        ]);

        if (!$property) {
            abort(404);
        }

        event(new BeforeEditContentEvent($request, $property));

        SeoHelper::setTitle(trans('plugins/real-estate::property.edit') . ' "' . $property->name . '"');

        return $formBuilder
            ->create(MemberPropertyForm::class, ['model' => $property])
            ->renderForm();
    }

    public function update_property(
        $id,
        PropertyRequest $request,
        BaseHttpResponse $response,
        AccountInterface $accountRepository,
        SaveFacilitiesService $saveFacilitiesService,
        MemberInterface $memberRepository
    )
    {
        $property = $this->propertyRepository->getFirstBy([
            'id' => $id,
            'member_id' => auth('member')->user()->getAuthIdentifier()
            //'author_type' => Account::class,
        ]);

        if (!$property) {
            abort(404);
        }
        //dd($request);exit;
        $old_category_id = $property->category_id;
        $old_documents = json_decode($property->documents);

        $property->fill($request->except(['expire_date']));
        $old_arr = (array)$old_documents;
        $jsonArr = array();

        $ids = array_column($old_arr, 'id');
        if ($request->hasFile('documents')) {
            $files = $request->file('documents');
            $i = 0;

            foreach ($files as $key => $file) {
                $document_id = $request['document_ids'][$key];
                $array_index = array_search($document_id, $ids);
                if ($array_index != "") {
                    $path = $old_arr[$array_index]->path;
                    if (Storage::exists($path)) {
                        Storage::delete($path);
                        unset($old_arr[$array_index]);
                    }
                }

                $name = $document_id . '-document-' . time() . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('Documents', $name);
                $jsonArr[$i]['id'] = $document_id;
                $jsonArr[$i]['path'] = 'Documents/' . $name;
                $i++;

            }


        }
        if ($old_category_id == $request['category_id']) {
            $update_arr = array_merge($old_arr, $jsonArr);
            $keys = array_column($update_arr, 'id');
            array_multisort($keys, SORT_ASC, $update_arr);
        } else {
            //updating checklist to empty
            $arr = array('document_checklist' => '', 'is_verify' => 0);
            $resupdate = table_properties_check_lists::where('property_id', $id)->update($arr);
            $update_arr = $jsonArr;
            $keys = array_column($update_arr, 'id');
            array_multisort($keys, SORT_ASC, $update_arr);
        }
        $property->documents = json_encode($update_arr);
        $area_value = $request['square'];
        $area_units = $request['area_units'];
        unset($request['square']);
        $sqFeet = getSqFeet($area_value, $area_units);
        $property->square = $sqFeet;
        // $agent_id=null;
        $agent_id = $request['author_id'];
        $property->author_id = $agent_id;
        $property->author_type = $agent_id > 0 ? Account::class : Member::class;

        $status = 'selling';
        if ($request['type'] == "rent")
            $status = 'renting';
        else
            $status = 'selling';

        $property->status = $status;

        if ($request['renew_now'] == "1") {
            $property->expire_date = Carbon::now()->addDays(45);
        }

        $this->propertyRepository->createOrUpdate($property);

        if ($request['renew_now'] == "1") {
            //deduct credits
            if ($property->member_id) {
                $member = $memberRepository->findOrFail($property->member_id);
                $member->credits--;
                $member->save();
            } else {
                $account = $accountRepository->findOrFail($property->author_id);
                $account->credits--;
                $account->save();
            }
        }

        $property->features()->sync($request->input('features', []));

        $saveFacilitiesService->execute($property, $request->input('facilities', []));

        event(new UpdatedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $request, $property));

        $this->memberLogRepository->createOrUpdate([
            'action' => 'update_property',
            'reference_name' => $property->name,
            'reference_url' => route('public.member.properties.edit', $property->id),
        ]);

        //Send Email

        $member = $memberRepository->findOrFail(auth('member')->user()->getAuthIdentifier());

        //send to self
        $variables = [
            'name' => 'Name',
            'property_url' => 'Property Url',
            'by' => 'By',
            'title' => 'Title',
            'action' => 'Action'
        ];

        EmailHandler::setModule('real-estate')
            ->addVariables($variables)
            ->setVariableValues([
                'name' => $member->full_name,
                'property_url' => route('public.member.properties.edit', ['id' => $property->id]),
                'by' => 'you',
                'title' => $property->name,
                'action' => 'updated'
            ])
            ->sendUsingTemplate('propertymodify', $member->email, [], false, 'plugins', 'Property Updated');

        //Email to Agent if Present
        if ($property->author_id) {
            $author = $accountRepository->findOrFail($property->author_id);

            EmailHandler::setModule('real-estate')
                ->addVariables($variables)
                ->setVariableValues([
                    'name' => $author->first_name . ' ' . $author->last_name,
                    'property_url' => route('public.account.properties.edit', ['property' => $property->id]),
                    'by' => $member->full_name,
                    'title' => $property->name,
                    'action' => 'updated'
                ])
                ->sendUsingTemplate('propertymodify', $author->email, [], false, 'plugins', 'Property Updated');
        }

        //send to admin
        EmailHandler::setModule('real-estate')
            ->addVariables($variables)
            ->setVariableValues([
                'name' => 'Admin',
                'property_url' => route('property.edit', ['property' => $property->id]),
                'by' => $member->full_name,
                'title' => $property->name,
                'action' => 'updated'
            ])
            ->sendUsingTemplate('propertymodify', 'admin@botble.com', [], false, 'plugins', 'Property Updated');

        return $response
            ->setPreviousUrl(route('public.member.properties.index'))
            ->setNextUrl(route('public.member.properties.edit', $property->id))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function delete_property($id, BaseHttpResponse $response)
    {
        $property = $this->propertyRepository->getFirstBy([
            'id' => $id,
            'member_id' => auth('member')->user()->getAuthIdentifier()
            //'author_type' => Account::class,
        ]);

        if (!$property) {
            abort(404);
        }

        $this->propertyRepository->delete($property);

        $this->memberLogRepository->createOrUpdate([
            'action' => 'delete_property',
            'reference_name' => $property->name,
        ]);

        //Send Email

        $memberRepository = App::make(MemberInterface::class);
        $accountRepository = App::make(AccountInterface::class);

        $member = $memberRepository->findOrFail(auth('member')->user()->getAuthIdentifier());

        //send to self
        $variables = [
            'name' => 'Name',
            'property_url' => 'Property Url',
            'by' => 'By',
            'title' => 'Title',
            'action' => 'Action'
        ];

        EmailHandler::setModule('real-estate')
            ->addVariables($variables)
            ->setVariableValues([
                'name' => $member->full_name,
                'property_url' => route('public.member.properties.edit', ['id' => $property->id]),
                'by' => 'you',
                'title' => $property->name,
                'action' => 'deleted'
            ])
            ->sendUsingTemplate('propertymodify', $member->email, [], false, 'plugins', 'Property Deleted');

        //Email to Agent if Present
        if ($property->author_id) {
            $author = $accountRepository->findOrFail($property->author_id);

            EmailHandler::setModule('real-estate')
                ->addVariables($variables)
                ->setVariableValues([
                    'name' => $author->first_name . ' ' . $author->last_name,
                    'property_url' => route('public.account.properties.edit', ['property' => $property->id]),
                    'by' => 'Member: ' . $member->full_name,
                    'title' => $property->name,
                    'action' => 'deleted'
                ])
                ->sendUsingTemplate('propertymodify', $author->email, [], false, 'plugins', 'Property Deleted');
        }

        //send to admin
        EmailHandler::setModule('real-estate')
            ->addVariables($variables)
            ->setVariableValues([
                'name' => 'Admin',
                'property_url' => route('property.edit', ['property' => $property->id]),
                'by' => 'Member: ' . $member->full_name,
                'title' => $property->name,
                'action' => 'deleted'
            ])
            ->sendUsingTemplate('propertymodify', 'admin@botble.com', [], false, 'plugins', 'Property Deleted');

        return $response->setMessage(__('Delete property successfully!'));
    }

    public function logout(Request $request, BaseHttpResponse $response)
    {
        do_action(AUTH_ACTION_AFTER_LOGOUT_SYSTEM, $request, $request->user('member'));
        Auth::guard('member')->logout();
        $request->session()->invalidate();

        return $response
            ->setNextUrl(route('public.index'))
            ->setMessage(trans('core/acl::auth.login.logout_success'));
    }

    /**member log**/
    public function getAllLogs($accountId, $paginate = 10)
    {
        /* return Member::select('*')->where('member_id', $accountId)
             ->latest('created_at')
             ->paginate($paginate);*/


    }

    public function term_conditions()
    {
        $res = Page::where('id', 5)->get(); //for terms & conditons
        /*$returnHTML = Theme::scope('real-estate.member.wanted',$data)->render();*/
        return response()->json(array('success' => true, 'html' => $res[0]->content));
    }

    public function wanted()
    {
        SeoHelper::setTitle(trans('plugins/real-estate::wanted.name'));

        $categories = $this->categoryRepository->allBy(
            ['status' => BaseStatusEnum::PUBLISHED, 'parent_id' => 0],
            [],
            ['id', 'parent_id', 'name']
        );

        // Prepare subcategories for each category
        $categories = $categories->map(function ($cat) {
            $cat->subcategories = $this->categoryRepository->allBy(
                ['status' => BaseStatusEnum::PUBLISHED, 'parent_id' => $cat->id],
                [],
                ['id', 'parent_id', 'name']
            );
            return $cat;
        });

       $countries = $this->countryRepository->pluck(
    'countries.name',
    'countries.id'
);

$states = [];

$cityChoices = \Botble\Location\Models\City::where('country_id', 166)
    ->where('status', BaseStatusEnum::PUBLISHED)
    ->orderBy('name')
    ->pluck('name', 'id')
    ->toArray();
        $projects = $this->projectRepository->allBy(
            [],
            [],
            ['re_projects.name', 're_projects.id']
        );

        $projectChoices = [];
        foreach ($projects as $project) {
            $projectChoices[$project->id] = $project->name;
        }

        $data = [
            'categories' => $categories,
             'countries' => $countries,
              'states' => $states,
               'city' => $cityChoices,
            'projects' => $projectChoices,
        ];

        if (view()->exists(Theme::getThemeNamespace() . '::views.real-estate.member.wanted')) {
            return Theme::scope('real-estate.member.wanted', $data)->render();
        }

        return view('plugins/real-estate::member.wanted', $data);
    }


    public function getSettings()
    {
        SeoHelper::setTitle(trans('plugins/real-estate::account.account_settings'));

        $user = auth('member')->user();

        return view('plugins/real-estate::member.settings.index', compact('user'));
    }

    public function postSettings(MemberSettingRequest $request, BaseHttpResponse $response)
    {

        Member::where('id', auth('member')->user()->getAuthIdentifier())
            ->update($request->except('email', '_token'));
        /*$this->activityLogRepository->createOrUpdate(['action' => 'update_setting']);*/
        return $response
            ->setNextUrl(route('member.settings'))
            ->setMessage(trans('plugins/real-estate::account.update_profile_success'));
    }

    public function getSecurity()
    {
        SeoHelper::setTitle(trans('plugins/real-estate::account.security'));

        return view('plugins/real-estate::member.settings.security');
    }

    public function postSecurity(UpdatePasswordRequest $request, BaseHttpResponse $response)
    {

        Member::where('id', auth('member')->user()->getAuthIdentifier())
            ->update(['password' => bcrypt($request->input('password'))]);
        /*$this->activityLogRepository->createOrUpdate(['action' => 'update_security']);*/
        return $response
            ->setNextUrl(route('public.member.security'))
            ->setMessage(trans('plugins/real-estate::account.update_password_success'));

    }

    //////////package management//////
    public function getPackages()
    {
        SeoHelper::setTitle(trans('plugins/real-estate::account.packages'));

        //        Assets::addScriptsDirectly('vendor/core/plugins/real-estate/js/components.js');
        Assets::addScriptsDirectly('js/app.js');

        return view('plugins/real-estate::member.settings.package');
    }

    public function ajaxGetPackages(PackageInterface $packageRepository, BaseHttpResponse $response)
    {
        $member = $this->memberRepository->findOrFail(
            auth('member')->user()->getAuthIdentifier(),
            ['packages']
        );

        $packages = $packageRepository->getModel()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->get();

        $packages = $packages->filter(function ($package) use ($member) {
            return $package->account_limit === null || $member->packages->where(
                    'id',
                    $package->id
                )->count() < $package->account_limit;
        });

        return $response->setData([
            'packages' => PackageResource::collection($packages),
            'account' => new MemberResource($member),
        ]);
    }

    public function ajaxGetTransactions(Request $request, TransactionInterface $transactionRepository, BaseHttpResponse $response)
    {
        $page = request()->query('page', 1);
        $transactions = $transactionRepository->advancedGet([
            'condition' => [
                'account_id' => auth('member')->user()->id,
            ],
            'paginate' => [
                'per_page' => 2,
                'current_paged' => $page,
            ],
            'order_by' => ['created_at' => 'DESC'],
            'with' => ['payment', 'user'],
        ]);

        return $response->setData(TransactionResource::collection($transactions))->toApiResponse();
    }

    public function ajaxSubscribePackage(
        Request              $request,
        PackageInterface     $packageRepository,
        BaseHttpResponse     $response,
        TransactionInterface $transactionRepository
    )
    {
        $package = $packageRepository->findOrFail($request->input('id'));
        $member = $this->memberRepository->findOrFail(auth('member')->user()->getAuthIdentifier());

        if (
            $package->account_limit && $member->packages()->where(
                'package_id',
                $package->id
            )->count() >= $package->account_limit
        ) {
            abort(403);
        }

        if ($package->price > 0) {
            return $response->setData(['next_page' => route('public.member.package.subscribe', $package->id)]);
        }

        $this->savePayment($package, null, $transactionRepository, true);

        return $response
            ->setData(new MemberResource($member->refresh()))
            ->setMessage(trans('plugins/real-estate::package.add_credit_success'));
    }

    protected function savePayment(Package $package, ?string $chargeId, TransactionInterface $transactionRepository, bool $force = false)
    {
        $payment = app(PaymentInterface::class)->getFirstBy(['charge_id' => $chargeId]);
        if (!$payment && !$force) {
            return false;
        }

        if (($payment && $payment->status == PaymentStatusEnum::COMPLETED) || $force) {
            $member = auth('member')->user();
            $member->credits += $package->number_of_listings;
            $member->save();

            $member->packages()->attach($package);
        }

        $transactionRepository->createOrUpdate([
            'user_id' => 0,
            'account_id' => auth('member')->user()->getAuthIdentifier(),
            'credits' => $package->number_of_listings,
            'payment_id' => $payment ? $payment->id : null,
        ]);

        return true;
    }

    public function getSubscribePackage(
        $id,
        PackageInterface $packageRepository,
        PaymentInterface $paymentRepository
    )
    {
        $package = $packageRepository->findOrFail($id);
        $total_price = $package->price;
        $voucher = false;
        if (session('discount')) {
            $package->price = $total_price - session('discount');
        }

        //Create Payment With Pending Status
        $member = auth('member')->user();
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
            'user_type' => 'member'
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
        return view('plugins/real-estate::member.checkout', compact(
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

    public function genericPackageCallback(BaseHttpResponse $response)
    {
        $member = auth('member')->user();

        if ($member) {
            return $response
                ->setNextUrl(route('public.member.package.callback', [
                    'O' => $_GET['O'],
                    'TS' => $_GET['TS'],
                ]));
        } else if (auth('account')->user()) {
            return $response
                ->setNextUrl(route('public.account.package.callback', [
                    'O' => $_GET['O'],
                    'TS' => $_GET['TS'],
                ]));
        } else {
            return $response
                ->setNextUrl(route('public.index'));
        }
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

                //Temporarry, need to change before comminging
                $payment = $paymentRepository->getFirstBy(['charge_id' => $orderId]);
                // $payment = $paymentRepository->getLastRecord();
                $package = $packageRepository->findById($payment->package_id);

                $member = auth('member')->user();
                $member->credits += $package->number_of_listings;
                $member->save();

                $member->packages()->attach($package);

                //Update payment data
                $dataToUpdate = ['status' => PaymentStatusEnum::COMPLETED];
                $paymentRepository->getModel()->update($dataToUpdate);

                $transactionRepository->createOrUpdate([
                    'user_id' => $member->id,
                    'account_id' => auth('member')->user()->getAuthIdentifier(),
                    'credits' => $package->number_of_listings,
                    'payment_id' => $payment ? $payment->id : null,
                    'user_type' => 'member'
                ]);

                $message = 'Your payment has been received. Credits have been added to your account';

                event(new CreatedContentEvent(PACKAGE_MODULE_SCREEN_NAME, $payment, $package));

                return $response
                    ->setNextUrl(route('public.member.packages'))
                    ->setMessage($message);
            } else {
                $message = 'Something went wrong with the payment. Please try again';

                return $response
                    ->setNextUrl(route('public.member.packages'))
                    ->setError()
                    ->setMessage($message);
            }
        } catch (Exception $e) {
            $message = 'Something went wrong. Please try again';

            return $response
                ->setNextUrl(route('public.member.packages'))
                ->setError()
                ->setMessage($e->getMessage());
        }
    }

    public function packageNotify()
    {
        echo "notification rec";
    }

    public function getPackageSubscribeCallback(
        $packageId,
        Request $request,
        PayPalPaymentService $payPalService,
        PackageInterface $packageRepository,
        TransactionInterface $transactionRepository,
        BaseHttpResponse $response
    )
    {
        $package = $packageRepository->findOrFail($packageId);

        if ($request->input('type') == PaymentMethodEnum::PAYPAL) {
            $validator = Validator::make($request->input(), [
                'amount' => 'required|numeric',
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
                    ->setNextUrl(route('public.member.packages'))
                    ->setMessage(trans('plugins/real-estate::package.add_credit_success'));
            }

            return $response
                ->setError()
                ->setNextUrl(route('public.member.packages'))
                ->setMessage($payPalService->getErrorMessage());
        }

        $this->savePayment($package, $request->input('charge_id'), $transactionRepository);

        return $response
            ->setNextUrl(route('public.member.packages'))
            ->setMessage(trans('plugins/real-estate::package.add_credit_success'));
    }


    public function getActivityLogs(BaseHttpResponse $response)
    {
        $activities = $this->memberLogRepository->getAllLogs(auth('member')->user()->getAuthIdentifier());

        Assets::addScriptsDirectly('vendor/core/plugins/real-estate/js/components.js');
        Assets::addScriptsDirectly('js/app.js');
        return $response->setData(ActivityLogResource::collection($activities))->toApiResponse();
    }

    public function checkout($id, Request $request)
    {
        $package = Package::findOrFail($id);
        $total_price = $package->price;
        $voucher = false;
        return Theme::scope('real-estate.member.checkout', compact('package', 'total_price', 'voucher'))->render();
        // return view('checkout',compact('package','total_price','voucher'));
    }

    public function postcheckout(Request $request, MemberInterface $memberRepository)
    {
        $package = Package::findOrFail($request->id);
        $total_price = $package->price;
        $voucher = false;
        if (isset($request->voucher)) {
            // echo $request->voucher;exit;
            try {
                $voucher = auth('member')->user()->redeemCode($request->voucher);
                $member = $memberRepository->findOrFail(auth('member')->user()->getAuthIdentifier());
                $member->credits++;
                $member->save();
                $total_price = round($total_price * (1 - $voucher->data->get('discount_percent') / 100), 2);

            } catch (\Exception $ex) {
                session()->flash('error', $ex->getMessage());
            }
        }
        return Theme::scope('real-estate.member.checkout', compact('package', 'total_price', 'voucher'))->render();

        // return view('checkout',compact('package','total_price','voucher'));
    }

    public function discountPackage(Request $request, MemberInterface $memberRepository, BaseHttpResponse $response)
    {
        $package = Package::findOrFail($request->id);
        $total_price = $package->price;
        $voucher = false;
        if (isset($request->voucher)) {
            $ceck = Voucher::where('code', $request->voucher)->where('model_id', $request->id)->get();

            if (count($ceck) > 0) {
                try {

                    $voucher = auth('member')->user()->redeemCode($request->voucher);
                    // dd($voucher);exit;
                    $member = $memberRepository->findOrFail(auth('member')->user()->getAuthIdentifier());
                    $discount_price = round($total_price * ($voucher->data->get('discount_percent') / 100), 2);
                    // echo $total_price.$discount_price;exit;
                    if ($total_price == $discount_price) {
                        //echo ('public.member.packages');exit;die;
                        $member->credits++;
                        $member->save();
                        $arr = array('status' => true, 'message' => 'Discount Applied Success', 'url' => route('public.member.packages'));
                        echo json_encode($arr);
                        /* return $response
                             ->setNextUrl(route('public.member.packages'))
                             ->setMessage(trans('plugins/real-estate::package.add_credit_success'));*/
                    } else {
                        // session('discount',$discount_price);
                        session(['discount' => $discount_price, 'discount_percent' => $voucher->data->get('discount_percent')]);
                        session()->flash('success', 'Discount Applied Success');
                        $arr = array('status' => true, 'message' => 'Discount Applied Success', 'url' => route('public.member.package.subscribe', $package->id), 'data' => array('discount' => $discount_price, 'discount_percent' => $voucher->data->get('discount_percent')));
                        echo json_encode($arr);
                        /* return $response
                             ->setNextUrl(route('public.member.package.subscribe', $package->id));*/
                    }

                } catch (\Exception $ex) {
                    session()->flash('error', $ex->getMessage());
                    $arr = array('status' => false, 'message' => $ex->getMessage(), 'url' => route('public.member.package.subscribe', $package->id));
                    echo json_encode($arr);
                    /*return $response
                        ->setNextUrl(route('public.member.package.subscribe', $package->id));*/
                }
            } else {
                session()->flash('error', 'This Code is invalid for this package');
                $arr = array('status' => false, 'message' => 'This Code is invalid for this package', 'url' => route('public.member.package.subscribe', $package->id));
                echo json_encode($arr);
                /*return $response
                    ->setNextUrl(route('public.member.package.subscribe', $package->id));*/

            }
        } else {
            $arr = array('status' => false, 'message' => 'No voucher code add', 'url' => route('public.member.package.subscribe', $package->id));
            echo json_encode($arr);
            /*return $response
                ->setNextUrl(route('public.member.package.subscribe', $package->id));*/
        }
    }
    /*    ->setMessage(trans('plugins/real-estate::package.add_credit_success')*/
    // return Theme::scope('real-estate.member.checkout',compact('package','total_price','voucher'))->render();
    // return view('checkout',compact('package','total_price','voucher'));
    public function rateSave(Request $request)
    {
        try {
            $request['user_id'] = auth('member')->user()->getAuthIdentifier();
            $rate = Rating::where('user_id', $request['user_id'])->where('agent_id', $request['agent_id'])->where('property_id', $request['property_id'])->get();
            if (count($rate) > 0) {

                $data = array('status' => false, 'message' => 'Already added!');
                echo json_encode($data);
            } else {
                $res = Rating::create($request->input());

                $data = array('status' => true, 'message' => 'Rating Added Successfully!');
                echo json_encode($data);
            }

        } catch (Exception\Exception $ex) {
            $data = array('status' => false, 'message' => $ex->getMessage());
            echo json_encode($data);
        }
    }

    public function getAgent(Request $request, AccountInterface $accountRepository)
    {
        $id = $_GET['id'];

        try {

            $account = $accountRepository->findOrFail($id);
            $account->url = $account->avatar_url;
            $response = [
                'status' => true,
                'data' => [
                    'fname' => $account->first_name,
                    'lname' => $account->last_name,
                ],
                'message' => 'Agent retrieved successfully!',
            ];
            return json_encode($response);

        } catch (Exception\Exception $ex) {
            $data = array('status' => false, 'message' => $ex->getMessage());
            return json_encode($data);
        }
    }

    public function getAgentFro(Request $request, AccountInterface $accountRepository)
    {
        $id = $_GET['id'];

        try {

            $account = $accountRepository->findOrFail($id);
            $account->url = $account->avatar_url;
            $res = array('status' => true, 'data' => $account, 'message' => 'Agent  Successfully!');
            echo json_encode($res);

        } catch (Exception\Exception $ex) {
            $data = array('status' => false, 'message' => $ex->getMessage());
            echo json_encode($data);
        }
    }

   public function area_unit_update(SettingStore $settingStore)
{
    try {
        $area_unit = $_GET['area_unit'] ?? null;

        if (!$area_unit) {
            return response()->json([
                'status' => false,
                'message' => 'Area unit is missing.',
            ]);
        }

        $settingStore->set('real_estate_square_unit', $area_unit);
        $settingStore->save();

        return response()->json([
            'status' => true,
            'message' => 'Area unit update Success',
        ]);

    } catch (Exception\Exception $ex) {
        return response()->json([
            'status' => false,
            'message' => $ex->getMessage(),
        ]);
    }
}
    public function currency_unit_update(SettingStore $settingStore)
    {
        try {
            $currency_unit = $_GET['currency_unit'];
            $settingStore->set('currencies_is_default', $currency_unit);
            $settingStore->save();
            //set all 0 first..
            Currency::where('is_default', '=', 1)->update(['is_default' => 0]);
            //update defaulr currency
            Currency::where('id', $currency_unit)->update(['is_default' => 1]);
            $res = array('status' => true, 'message' => 'currency unit update Success');
            echo json_encode($res);
        } catch (Exception\Exception $ex) {
            $data = array('status' => false, 'message' => $ex->getMessage());
            echo json_encode($data);
        }
    }

    public function postUpload(Request $request, BaseHttpResponse $response)
    {
        if (setting('media_chunk_enabled') != '1') {
            $validator = Validator::make($request->all(), [
                'file.0' => 'required|image|mimes:jpg,jpeg,png,webp,gif,bmp',
            ]);

            if ($validator->fails()) {
                return $response->setError()->setMessage($validator->getMessageBag()->first());
            }

            $result = RvMedia::handleUpload(Arr::first($request->file('file')), 0, 'members');

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
                'done' => $handler->getPercentageDone(),
                'status' => true,
            ]);
        } catch (Exception $exception) {
            return $response->setError(true)->setMessage($exception->getMessage());
        }
    }
    public function getStates(Request $request)
{
    return State::where('country_id', $request->country_id)
        ->where('status', BaseStatusEnum::PUBLISHED)
        ->orderBy('name')
        ->get([
            'id',
            'name'
        ]);
}
public function getCities(Request $request)
{
    return City::where('state_id', $request->state_id)
        ->where('status', BaseStatusEnum::PUBLISHED)
        ->orderBy('name')
        ->get([
            'id',
            'name'
        ]);
}
}
