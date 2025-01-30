<?php

namespace Botble\RealEstate\Http\Controllers;

use App\Models\description_template;
use App\Models\table_properties_check_lists;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Forms\PropertyForm;
use Botble\RealEstate\Http\Requests\PropertyRequest;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Botble\RealEstate\Repositories\Interfaces\MemberInterface;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\RealEstate\Repositories\Interfaces\FeatureInterface;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\RealEstate\Services\SaveFacilitiesService;
use Botble\RealEstate\Tables\PropertyTable;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Category;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;
use Log;
use PhpParser\Node\Stmt\Switch_;
use Throwable;
use Illuminate\Support\Facades\Storage;
use SeoHelper;
use EmailHandler;

use Theme;

class PropertyController extends BaseController
{
    /**
     * @var PropertyInterface $propertyRepository
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
     * PropertyController constructor.
     * @param PropertyInterface $propertyRepository
     * @param ProjectInterface $projectRepository
     * @param FeatureInterface $featureRepository
     */
    public function __construct(
        PropertyInterface $propertyRepository,
        ProjectInterface $projectRepository,
        FeatureInterface $featureRepository
    ) {
        $this->propertyRepository = $propertyRepository;
        $this->projectRepository = $projectRepository;
        $this->featureRepository = $featureRepository;
    }

    /**
     * @param PropertyTable $dataTable
     * @return JsonResponse|View
     * @throws Throwable
     */
    public function index(PropertyTable $dataTable)
    {
        page_title()->setTitle(trans('plugins/real-estate::property.name'));

        return $dataTable->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/real-estate::property.create'));

        return $formBuilder->create(PropertyForm::class)->renderForm();
    }

    /**
     * @param PropertyRequest $request
     * @param BaseHttpResponse $response
     * @param SaveFacilitiesService $saveFacilitiesService
     * @return BaseHttpResponse
     * @throws FileNotFoundException
     */
    public function store(PropertyRequest $request, BaseHttpResponse $response, SaveFacilitiesService $saveFacilitiesService)
    {
        $request->merge([
            'expire_date' => now()->addDays(config('plugins.real-estate.real-estate.property_expired_after_x_days')),
            'images' => json_encode($request->input('images', [])),
            'author_type' => Account::class

        ]);

        $property = $this->propertyRepository->getModel();

        $jsonArr = array();

        //run actions with files

        if ($request->hasFile('documents')) {
            $files = $request->file('documents');
            // print_r($_FILES);exit;
            $i = 0;
            foreach ($files as $key => $file) {
                $document_id = $request['document_ids'][$key];
                $name = $document_id . '-document-' . time() . uniqid() . '.' . $file->extension();
                $file->storeAs('Documents', $name);
                $jsonArr[$i]['id'] = $key;
                $jsonArr[$i]['path'] = 'Documents/' . $name;
                $i++;

            }
        }

        $status = 'selling';
        if ($request['type'] == "rent")
            $status = 'renting';
        else
            $status = 'selling';
        $request['documents'] = json_encode($jsonArr);
        $area_value = $request['square'];
        $area_units = $request['area_units'];
        unset($request['square']);
        $sqFeet = getSqFeet($area_value, $area_units);
        $property = $property->fill($request->input());
        $property->moderation_status = $request->input('moderation_status');
        $property->latitude = $request->input('latitude');
        $property->longitude = $request->input('longitude');
        $property->never_expired = $request->input('never_expired');
        $property->square = $sqFeet;
        $property->status = $status;
        $property->save();

        event(new CreatedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $request, $property));

        if ($property) {
            $property->features()->sync($request->input('features', []));

            $saveFacilitiesService->execute($property, $request->input('facilities', []));
        }

        //send to self
        $variables = [
            'name' => 'Name',
            'property_url' => 'Property Url',
            'by' => 'By',
            'title' => 'Title',
            'action' => 'Action'
        ];

        EmailHandler::setModule('property')
            ->addVariables($variables)
            ->setVariableValues([
                'name' => 'Admin',
                'property_url' => route('property.edit', ['property' => $property->id]),
                'by' => 'you',
                'title' => $property->name,
                'action' => 'created'
            ])
            ->sendUsingTemplate('propertymodify', 'admin@botble.com', [], false, 'plugins', 'Property Created');

        return $response
            ->setPreviousUrl(route('property.index'))
            ->setNextUrl(route('property.edit', $property->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, Request $request, FormBuilder $formBuilder)
    {
        $property = $this->propertyRepository->findOrFail($id, ['features', 'author']);

        $parent_cat = Category::select('parent_id')->where('id', $property->category_id)->first();
        $property->setAttribute('parent_id', $parent_cat->parent_id);
        page_title()->setTitle(trans('plugins/real-estate::property.edit') . ' "' . $property->name . '"');

        event(new BeforeEditContentEvent($request, $property));

        return $formBuilder->create(PropertyForm::class, ['model' => $property])->renderForm();
    }

    /**
     * @param int $id
     * @param PropertyRequest $request
     * @param BaseHttpResponse $response
     * @param SaveFacilitiesService $facilitiesService
     * @return BaseHttpResponse
     * @throws FileNotFoundException
     */
    public function update(
        $id,
        PropertyRequest $request,
        BaseHttpResponse $response,
        SaveFacilitiesService $saveFacilitiesService,
        AccountInterface $accountRepository,
        MemberInterface $memberRepository
    ) {
        $property = $this->propertyRepository->findOrFail($id);
        $alreadySavedModStatus = $property->moderation_status;
        $old_category_id = $property->category_id;
        $old_documents = json_decode($property->documents);
        $property->fill($request->except(['expire_date', 'square']));
        $area_value = $request['square'];
        $area_units = $request['area_units'];
        $sqFeet = getSqFeet($area_value, $area_units);
        $property->author_type = Account::class;
        $property->images = json_encode($request->input('images', []));
        $old_arr = (array) $old_documents;
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
        $property->documents = json_encode($update_arr);
        //$property->moderation_status = $request->input('moderation_status');
        ///if all checklist checked  then approved other wise pending
        $property->moderation_status = $request->input('moderation_status');
        if ($request->input('moderation_status') == ModerationStatusEnum::APPROVED) {
            if (!$property->date_published) {
                $property->date_published = Carbon::now();
            }
        }
        $property->never_expired = $request->input('never_expired');
        $property->square = $sqFeet;
        $status = 'selling';
        if ($request['type'] == "rent")
            $status = 'renting';
        else
            $status = 'selling';

        $property->status = $status;
        $this->propertyRepository->createOrUpdate($property);

        if ($alreadySavedModStatus != ModerationStatusEnum::APPROVED && $request->input('moderation_status') == ModerationStatusEnum::APPROVED) {
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

        event(new UpdatedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $request, $property));

        $property->features()->sync($request->input('features', []));

        $saveFacilitiesService->execute($property, $request->input('facilities', []));

        //Send Email

        $variables = [
            'name' => 'Name',
            'property_url' => 'Property Url',
            'by' => 'By',
            'title' => 'Title',
            'action' => 'Action'
        ];

        $action = 'updated';

        if ($request->input('moderation_status') != ModerationStatusEnum::PENDING) {
            $action = strtolower($request->input('moderation_status'));
        }

        EmailHandler::setModule('property')
            ->addVariables($variables)
            ->setVariableValues([
                'name' => 'Admin',
                'property_url' => route('property.edit', ['property' => $property->id]),
                'by' => 'You',
                'title' => $property->name,
                'action' => $action
            ])
            ->sendUsingTemplate('propertymodify', 'admin@botble.com', [], false, 'plugins', 'Property ' . ucfirst($action));

        if ($property->member_id) {
            $member = $memberRepository->findOrFail($property->member_id);

            EmailHandler::setModule('property')
                ->addVariables($variables)
                ->setVariableValues([
                    'name' => $member->full_name,
                    'property_url' => route('public.member.properties.edit', ['id' => $property->id]),
                    'by' => 'Admin',
                    'title' => $property->name,
                    'action' => $action
                ])
                ->sendUsingTemplate('propertymodify', $member->email, [], false, 'plugins', 'Property ' . ucfirst($action));
        }

        if ($property->author_id) {
            $account = $accountRepository->findOrFail($property->author_id);

            EmailHandler::setModule('property')
                ->addVariables($variables)
                ->setVariableValues([
                    'name' => $account->first_name . ' ' . $account->last_name,
                    'property_url' => route('public.account.properties.edit', ['property' => $property->id]),
                    'by' => 'Admin',
                    'title' => $property->name,
                    'action' => $action
                ])
                ->sendUsingTemplate('propertymodify', $account->email, [], false, 'plugins', 'Property ' . ucfirst($action));
        }

        return $response
            ->setPreviousUrl(route('property.index'))
            ->setNextUrl(route('property.edit', $property->id))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy($id, Request $request, BaseHttpResponse $response)
    {
        try {
            $property = $this->propertyRepository->findOrFail($id);
            $property->features()->detach();
            $this->propertyRepository->delete($property);

            event(new DeletedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $request, $property));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.cannot_delete'));
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $property = $this->propertyRepository->findOrFail($id);
            $property->features()->detach();
            $this->propertyRepository->delete($property);

            event(new DeletedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $request, $property));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    public function agent_search()
    {

        /* if (view()->exists(Theme::getThemeNamespace() . '::views.real-estate.agent-search')) {

             return Theme::scope('real-estate.agent-search')->render();
         }

         return view('plugins/real-estate::agent-search');*/
        SeoHelper::setTitle(trans('plugins/real-estate::account.wanted'));
        $data = array();
        // print_r($cityChoices);exit;
        if (view()->exists(Theme::getThemeNamespace() . '::views.real-estate.agent-search')) {

            return Theme::scope('real-estate.agent-search', $data)->render();

            return view('plugins/real-estate::agent-search', $data);
        }
    }

    public function mailForPayment(Request $request, AccountInterface $accountRepo, MemberInterface $memberRepo, BaseHttpResponse $response)
    {
        try {
            if ($request->has('id') && $request->has('type')) {
                $type = $request->input('type');
                $id = $request->get('id');
                $propertyId = $request->get('property_id');
                $title = $request->get('title');

                $variables = [
                    'name' => 'Name',
                    'property_url' => 'Property Url',
                    'title' => 'Title',
                    'credits_url' => 'Credits Url'
                ];

                if ($type == 'agent') {
                    $account = $accountRepo->findOrFail($id);
                    if ($account) {
                        EmailHandler::setModule('property')
                            ->addVariables($variables)
                            ->setVariableValues([
                                'name' => $account->first_name . ' ' . $account->last_name,
                                'property_url' => route('public.account.properties.edit', ['property' => $propertyId]),
                                'title' => $title,
                                'credits_url' => route('public.account.packages'),
                            ])
                            ->sendUsingTemplate('paymentmail', $account->email, [], false, 'plugins', 'GEM - Payment Pending');

                        return $response
                            ->setPreviousUrl(route('property.edit', ['property' => $propertyId]))
                            ->setNextUrl(route('property.edit', ['property' => $propertyId]))
                            ->setMessage('Email has been sent.');
                    }
                } else if ($type == 'member') {
                    $member = $memberRepo->findOrFail($id);
                    if ($member) {
                        EmailHandler::setModule('property')
                            ->addVariables($variables)
                            ->setVariableValues([
                                'name' => $member->full_name,
                                'property_url' => route('public.member.properties.edit', ['id' => $propertyId]),
                                'title' => $title,
                                'credits_url' => route('public.member.packages'),
                            ])
                            ->sendUsingTemplate('paymentmail', $member->email, [], false, 'plugins', 'GEM - Payment Pending');

                        return $response
                            ->setPreviousUrl(route('public.account.properties.edit', ['property' => $propertyId]))
                            ->setNextUrl(route('public.account.properties.edit', ['property' => $propertyId]))
                            ->setMessage('Email has been sent.');
                    }
                } else {
                    return $response
                        ->setError()
                        ->setMessage('Something went wrong. Cannot send email111.');
                }
            }
        } catch (Exception $exception) {
            Log::debug('message', [$exception->getMessage()]);
            return $response
                ->setError()
                ->setMessage('Something went wrong. Cannot send email');
        }

    }
}
