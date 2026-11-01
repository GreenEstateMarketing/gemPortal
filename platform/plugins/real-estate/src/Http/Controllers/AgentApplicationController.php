<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Enums\AgentApplicationStatusEnum;
use Botble\RealEstate\Forms\AgentApplicationForm;
use Botble\RealEstate\Models\AgentApplication;
use Botble\RealEstate\Tables\AgentApplicationTable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AgentApplicationController extends BaseController
{
    /**
     * @param AgentApplicationTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(AgentApplicationTable $table)
    {
        page_title()->setTitle(trans('plugins/real-estate::agent-application.name'));

        return $table->renderTable();
    }

    /**
     * @param int $id
     * @param FormBuilder $formBuilder
     * @param Request $request
     * @return string
     */
    public function edit(int $id, FormBuilder $formBuilder, Request $request)
    {
        $application = AgentApplication::findOrFail($id);

        event(new BeforeEditContentEvent($request, $application));

        $application->fill(['status' => AgentApplicationStatusEnum::REVIEWED]);
        $application->save();

        page_title()->setTitle(trans('plugins/real-estate::agent-application.edit') . ' "' . $application->name . '"');

        return $formBuilder->create(AgentApplicationForm::class, ['model' => $application])->renderForm();
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(int $id, BaseHttpResponse $response)
    {
        try {
            $application = AgentApplication::findOrFail($id);

            Storage::disk('local')->delete($application->resume_path);
            $application->delete();

            event(new DeletedContentEvent(AGENT_APPLICATION_MODULE_SCREEN_NAME, request(), $application));

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
            $application = AgentApplication::findOrFail($id);
            Storage::disk('local')->delete($application->resume_path);
            $application->delete();
            event(new DeletedContentEvent(AGENT_APPLICATION_MODULE_SCREEN_NAME, $request, $application));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    /**
     * @param int $id
     * @return StreamedResponse
     */
    public function downloadResume(int $id)
    {
        $application = AgentApplication::findOrFail($id);

        return Storage::disk('local')->download($application->resume_path, $application->resume_original_name);
    }
}
