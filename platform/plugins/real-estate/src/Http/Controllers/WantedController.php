<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\RealEstate\Repositories\Interfaces\WantedInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Exception;
use Botble\RealEstate\Tables\WantedTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Forms\WantedForm;
use Botble\Base\Forms\FormBuilder;
use Illuminate\View\View;
use Throwable;

class WantedController extends BaseController
{
    /**
     * @var WantedInterface
     */
    protected $wantedRepository;

    /**
     * WantedController constructor.
     * @param WantedInterface $wantedRepository
     */
    public function __construct(WantedInterface $wantedRepository)
    {
        $this->wantedRepository = $wantedRepository;
    }

    /**
     * @param WantedTable $dataTable
     * @return Factory|View
     * @throws Throwable
     */
    public function index(WantedTable $table)
    {
        page_title()->setTitle(trans('plugins/real-estate::wanted.name'));

        return $table->renderTable();
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
        $wanted = $this->wantedRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $wanted));

        page_title()->setTitle(trans('plugins/real-estate::wanted.edit') . ' "' . $wanted->name . '"');

        return $formBuilder->create(WantedForm::class, ['model' => $wanted])->renderForm();
    }

    /**
     * @param $id
     * @param Request $request
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $wanted = $this->wantedRepository->findOrFail($id);

            $this->wantedRepository->delete($wanted);

            event(new DeletedContentEvent(WANTED_MODULE_SCREEN_NAME, $request, $wanted));

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
            $wanted = $this->wantedRepository->findOrFail($id);
            $this->wantedRepository->delete($wanted);
            event(new DeletedContentEvent(WANTED_MODULE_SCREEN_NAME, $request, $wanted));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
