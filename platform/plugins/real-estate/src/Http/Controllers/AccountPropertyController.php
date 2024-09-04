<?php

namespace Botble\RealEstate\Http\Controllers;

use App\Models\table_properties_check_lists;
use Assets;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Forms\AccountPropertyForm;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\RealEstate\Http\Requests\PropertyRequest;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Repositories\Interfaces\AccountActivityLogInterface;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Botble\RealEstate\Services\SaveFacilitiesService;
use Botble\RealEstate\Tables\AccountPropertyTable;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use SeoHelper;

class AccountPropertyController extends Controller
{
    /**
     * @var AccountInterface
     */
    protected $accountRepository;

    /**
     * @var PropertyInterface
     */
    protected $propertyRepository;

    /**
     * @var AccountActivityLogInterface
     */
    protected $activityLogRepository;

    /**
     * PublicController constructor.
     * @param Repository $config
     * @param AccountInterface $accountRepository
     * @param PropertyInterface $propertyRepository
     * @param AccountActivityLogInterface $accountActivityLogRepository
     */
    public function __construct(
        Repository $config,
        AccountInterface $accountRepository,
        PropertyInterface $propertyRepository,
        AccountActivityLogInterface $accountActivityLogRepository
    ) {
        $this->accountRepository = $accountRepository;
        $this->propertyRepository = $propertyRepository;
        $this->activityLogRepository = $accountActivityLogRepository;

        Assets::setConfig($config->get('plugins.real-estate.assets'));
    }

    /**
     * @param Request $request
     * @param AccountPropertyTable $propertyTable
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View|\Response
     * @throws \Throwable
     */
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
        if (!auth('account')->user()->canPost()) {
            abort(403);
        }

        SeoHelper::setTitle(__('Add a property'));

        return $formBuilder->create(AccountPropertyForm::class)->renderForm();
    }

    /**
     * @param PropertyRequest $request
     * @param BaseHttpResponse $response
     * @param AccountInterface $accountRepository
     * @param SaveFacilitiesService $saveFacilitiesService
     * @return BaseHttpResponse
     */
    public function store(PropertyRequest $request, BaseHttpResponse $response, AccountInterface $accountRepository, SaveFacilitiesService $saveFacilitiesService)
    {
        if (!auth('account')->user()->canPost()) {
            abort(403);
        }

        $request->merge(['expire_date' => now()->addDays(config('plugins.real-estate.real-estate.property_expired_after_x_days'))]);

        /**
         * @var Property $property
         */
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
        $property = $this->propertyRepository->createOrUpdate(array_merge($request->input(), [
            'author_id' => auth('account')->user()->getAuthIdentifier(),
            'author_type' => Account::class,
        ]));

        if ($property) {
            $property->features()->sync($request->input('features', []));

            $saveFacilitiesService->execute($property, $request->input('facilities', []));
        }

        event(new CreatedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $request, $property));

        $this->activityLogRepository->createOrUpdate([
            'action' => 'create_property',
            'reference_name' => $property->name,
            'reference_url' => route('public.account.properties.edit', $property->id),
        ]);

        // $account = $accountRepository->findOrFail(auth('account')->user()->getAuthIdentifier());
        // $account->credits--;
        // $account->save();

        return $response
            ->setPreviousUrl(route('public.account.properties.index'))
            ->setNextUrl(route('public.account.properties.edit', $property->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
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
            'author_id' => auth('account')->user()->getAuthIdentifier(),
            'author_type' => Account::class,
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
            'author_id' => auth('account')->user()->getAuthIdentifier(),
            'author_type' => Account::class,
        ]);

        if (!$property) {
            abort(404);
        }

        $old_documents = json_decode($property->documents);
        $old_category_id = $property->category_id;
        $property->fill($request->except(['expire_date']));
        $area_value = $request['square'];
        $area_units = $request['area_units'];
        $old_arr = (array) $old_documents;
        $jsonArr = array();

        $ids = array_column($old_arr, 'id');
        if ($request->hasFile('documents')) {
            $files = $request->file('documents');
            $i = 0;

            foreach ($files as $key => $file) {
                //$key;
                $document_id = $request['document_ids'][$key];
                $array_index = array_search(2, $ids);
                // echo $array_index;exit;
                $path = $old_arr[$array_index]->path;

                if ($array_index >= 0) {
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
        unset($request['square']);
        $sqFeet = getSqFeet($area_value, $area_units);
        $property->square = $sqFeet;
        $this->propertyRepository->createOrUpdate($property);

        $property->features()->sync($request->input('features', []));

        $saveFacilitiesService->execute($property, $request->input('facilities', []));

        event(new UpdatedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $request, $property));

        $this->activityLogRepository->createOrUpdate([
            'action' => 'update_property',
            'reference_name' => $property->name,
            'reference_url' => route('public.account.properties.edit', $property->id),
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
            'author_id' => auth('account')->user()->getAuthIdentifier(),
            'author_type' => Account::class,
        ]);

        if (!$property) {
            abort(404);
        }

        $this->propertyRepository->delete($property);

        $this->activityLogRepository->createOrUpdate([
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

        $account = auth('account')->user();

        if ($account->credits < 1) {
            return $response->setError(true)->setMessage(__('You don\'t have enough credit to renew this property!'));
        }

        $job->expire_date = $job->expire_date->addDays(config('plugins.real-estate.real-estate.property_expired_after_x_days'));
        $job->save();

        $account->credits--;
        $account->save();

        return $response->setMessage(__('Renew property successfully'));
    }
}
