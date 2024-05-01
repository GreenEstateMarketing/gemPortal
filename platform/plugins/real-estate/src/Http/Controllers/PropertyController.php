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
use Botble\RealEstate\Forms\PropertyForm;
use Botble\RealEstate\Http\Requests\PropertyRequest;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\RealEstate\Repositories\Interfaces\FeatureInterface;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\RealEstate\Services\SaveFacilitiesService;
use Botble\RealEstate\Tables\PropertyTable;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Category;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;
use PhpParser\Node\Stmt\Switch_;
use Throwable;
use Illuminate\Support\Facades\Storage;
use SeoHelper;

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
        //
       // dd($res);


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
            'images'      => json_encode($request->input('images', [])),
           'author_type' => Account::class

        ]);

        $property = $this->propertyRepository->getModel();

        $jsonArr=array();

            //run actions with files

        if($request->hasFile('documents'))
        {
            $files = $request->file('documents');
           // print_r($_FILES);exit;
            $i=0;
            foreach ($files as $key => $file) {
                $document_id=$request['document_ids'][$key];
                $name = $document_id.'-document-'.time().uniqid().'.'.$file->extension();
                $file->storeAs('Documents', $name);
                $jsonArr[$i]['id'] = $key;
                $jsonArr[$i]['path'] = 'Documents/' . $name;
                $i++;

            }
        }

        $status = 'selling';
        if($request['type'] == "rent")
            $status = 'renting';
        else
            $status = 'selling';
        $request['documents']=json_encode($jsonArr);
        $area_value=$request['square'];
        $area_units=$request['area_units'];
        unset($request['square']);
        $sqFeet=getSqFeet($area_value,$area_units);
        $property = $property->fill($request->input());
        $property->moderation_status = $request->input('moderation_status');
        $property->latitude = $request->input('latitude');
        $property->longitude = $request->input('longitude');
        $property->never_expired = $request->input('never_expired');
        $property->square=$sqFeet;
        $property->status=$status;
        $property->save();

        event(new CreatedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $request, $property));

        if ($property) {
            $property->features()->sync($request->input('features', []));

            $saveFacilitiesService->execute($property, $request->input('facilities', []));
        }

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
        // echo '<pre>';
        
    //    $property->setAttribute('parent_id');
       
        // exit;
        $parent_cat = Category::select('parent_id')->where('id',$property->category_id)->first();
        // print_r($parent_id->parent_id);exit;
        $property->setAttribute('parent_id', $parent_cat->parent_id);
        // print_r($property->getAttributes();exit;
        // $property .= $parent_id;
        // dd($parent_id);
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
    public function update($id, PropertyRequest $request, BaseHttpResponse $response, SaveFacilitiesService $saveFacilitiesService)
    {
        $property = $this->propertyRepository->findOrFail($id);
        $old_category_id=$property->category_id;
        $old_documents=json_decode($property->documents);
        $property->fill($request->except(['expire_date','square']));
        $area_value=$request['square'];
        $area_units=$request['area_units'];
        $sqFeet=getSqFeet($area_value,$area_units);
        $property->author_type = Account::class;
        $property->images = json_encode($request->input('images', []));
        $old_arr=(array)$old_documents;
        $jsonArr=array();

        $ids= array_column($old_arr, 'id');
        if($request->hasFile('documents'))
        {
            $files = $request->file('documents');
            $i=0;

            foreach ($files as $key => $file) {
                //$key;
                $document_id=$request['document_ids'][$key];
                $array_index = array_search($document_id,$ids);
               // echo $array_index;exit;
                if($array_index!="") {
                    $path=$old_arr[$array_index]->path;
                        if (Storage::exists($path)) {
                        Storage::delete($path);
                        unset($old_arr[$array_index]);
                    }
                }
                //}

                /*$name = $ids[$key] . time() . uniqid().'.'.$file->extension();*/
                $name = $document_id.'-document-'.time().uniqid().'.'.$file->extension();
                $file->storeAs('Documents', $name);
                $jsonArr[$i]['id'] =$document_id;
                $jsonArr[$i]['path'] = 'Documents/' . $name;
                $i++;

            }



        }
        if($old_category_id==$request['category_id'])
        {
            $update_arr = array_merge($old_arr, $jsonArr);
            $keys = array_column($update_arr, 'id');
            array_multisort($keys, SORT_ASC, $update_arr);
        }
        else
        {
            //updating checklist to empty
            $arr = array('document_checklist' =>'','is_verify'=>0);
            $resupdate= table_properties_check_lists::where('property_id',$id)->update($arr);
            $update_arr =$jsonArr;
            $keys = array_column($update_arr, 'id');
            array_multisort($keys, SORT_ASC, $update_arr);
        }
        $property->documents=json_encode($update_arr);
        //$property->moderation_status = $request->input('moderation_status');
        ///if all checklist checked  then approved other wise pending
        $property->moderation_status = $request->input('moderation_status');
        $property->never_expired = $request->input('never_expired');
        $property->square=$sqFeet;
        $status = 'selling';
        if($request['type'] == "rent")
            $status = 'renting';
        else
            $status = 'selling';
        
        $property->status = $status;
        $this->propertyRepository->createOrUpdate($property);

        event(new UpdatedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $request, $property));

        $property->features()->sync($request->input('features', []));

        $saveFacilitiesService->execute($property, $request->input('facilities', []));

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
    public function  agent_search()
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

}
