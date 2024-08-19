<?php

namespace Botble\Location\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Location\Forms\CityAreaForm;
use Botble\Location\Http\Requests\CityAreaRequest;
use Botble\Location\Repositories\Interfaces\CityAreaInterface;
use Botble\Location\Tables\CityAreaTable;
use Illuminate\Http\Request;
use Exception;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Forms\FormBuilder;

class CityAreaController extends BaseController
{
    protected $cityAreaRepository;

    public function __construct(CityAreaInterface $cityAreaRepository)
    {
        $this->cityAreaRepository = $cityAreaRepository;
    }

    public function index(CityAreaTable $table)
    {
        page_title()->setTitle(trans('plugins/location::cityarea.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/location::cityarea.create'));

        return $formBuilder->create(CityAreaForm::class)->renderForm();
    }

    public function store(CityAreaRequest $request, BaseHttpResponse $response)
    {
        $cityArea = $this->cityAreaRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(CITY_MODULE_SCREEN_NAME, $request, $cityArea));

        return $response
            ->setPreviousUrl(route('cityarea.index'))
            ->setNextUrl(route('cityarea.edit', $cityArea->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        $cityArea = $this->cityAreaRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $cityArea));

        page_title()->setTitle(trans('plugins/location::cityarea.edit') . ' "' . $cityArea->name . '"');

        return $formBuilder->create(CityAreaForm::class, ['model' => $cityArea])->renderForm();
    }

    public function update($id, CityAreaRequest $request, BaseHttpResponse $response)
    {
        $cityArea = $this->cityAreaRepository->findOrFail($id);

        $cityArea->fill($request->input());

        $this->cityAreaRepository->createOrUpdate($cityArea);

        event(new UpdatedContentEvent(CITY_MODULE_SCREEN_NAME, $request, $cityArea));

        return $response
            ->setPreviousUrl(route('cityarea.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $cityArea = $this->cityAreaRepository->findOrFail($id);

            $this->cityAreaRepository->delete($cityArea);

            event(new DeletedContentEvent(CITY_MODULE_SCREEN_NAME, $request, $cityArea));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $cityArea = $this->cityAreaRepository->findOrFail($id);
            $this->cityAreaRepository->delete($cityArea);
            event(new DeletedContentEvent(CITY_MODULE_SCREEN_NAME, $request, $cityArea));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}