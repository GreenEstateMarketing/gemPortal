<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\RealEstate\Http\Requests\ConsultRequest;
use Botble\RealEstate\Models\Consult;
use Botble\RealEstate\Repositories\Interfaces\ConsultInterface;
use Botble\Base\Http\Controllers\BaseController;
use Botble\RealEstate\Tables\AccountConsultTable;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Exception;
use Botble\RealEstate\Tables\ConsultTable;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Forms\AccountConsultForm;
use Botble\Base\Forms\FormBuilder;
use Illuminate\View\View;
use Throwable;
use Assets;
use SeoHelper;
class AccountConsultController extends BaseController
{
    /**
     * @var ConsultInterface
     */
    protected $consultRepository;

    /**
     * ConsultController constructor.
     * @param ConsultInterface $consultRepository
     */
    public function __construct(  Repository $config,ConsultInterface $consultRepository)
    {
        $this->consultRepository = $consultRepository;
        Assets::setConfig($config->get('plugins.real-estate.assets'));
    }

    /**
     * Display all consults
     * @param ConsultTable $dataTable
     * @return Factory|View
     * @throws Throwable
     */
    public function index(AccountConsultTable $table)
    {
        Assets::addScriptsDirectly([

            '/js/real-estate-agent.js'

        ]);
        page_title()->setTitle(trans('plugins/real-estate::consult.name'));
        return $table->render('plugins/real-estate::account.table.base');
       //return $table->renderTable();
    }
    public  function propertyConsults($id,AccountConsultTable $table)
    {
      $property_id=$id;
      return $table->with('property_id', $property_id)->render('plugins/real-estate::account.table.base');
    }

    /**
     * Show edit form
     *
     * @param $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        $consult = $this->consultRepository->findOrFail($id, ['project', 'property']);
        event(new BeforeEditContentEvent($request, $consult));
        $consult->fill(array('status'=>'read'));
        $this->consultRepository->createOrUpdate($consult);
        event(new UpdatedContentEvent(CONSULT_MODULE_SCREEN_NAME, $request, $consult));
        page_title()->setTitle(trans('plugins/real-estate::consult.edit') . ' "' . $consult->name . '"');

        return $formBuilder->create(AccountConsultForm::class, ['model' => $consult])->renderForm();
    }

    /**
     * @param $id
     * @param ConsultRequest $request
     * @return BaseHttpResponse
     */
    public function update($id, ConsultRequest $request, BaseHttpResponse $response)
    {
        $consult = $this->consultRepository->findOrFail($id);

        $consult->fill($request->input());

        $this->consultRepository->createOrUpdate($consult);

        event(new UpdatedContentEvent(CONSULT_MODULE_SCREEN_NAME, $request, $consult));

        return $response
            ->setPreviousUrl(route('public.account.consult.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param $id
     * @param Request $request
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {

       // try {
            $consult = $this->consultRepository->findOrFail($id);

            $this->consultRepository->delete($consult);
        return $response->setMessage(__('Delete consult successfully!'));
            // return $response->setMessage(__('Deleted consult successfully!'));

        //}
        /*catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.cannot_delete'));
        }*/
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
            $consult = $this->consultRepository->findOrFail($id);
            $this->consultRepository->delete($consult);
            event(new DeletedContentEvent(CONSULT_MODULE_SCREEN_NAME, $request, $consult));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
