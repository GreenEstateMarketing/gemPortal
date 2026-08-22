<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\RealEstate\Forms\TemplateForm;
use Botble\RealEstate\Http\Requests\TemplateRequest;
use Botble\RealEstate\Repositories\Interfaces\TemplateInterface;
use Botble\Base\Forms\FormBuilder;
use Botble\RealEstate\Tables\TemplateTable;
use Illuminate\Http\Request;
use Botble\Base\Http\Responses\BaseHttpResponse;

class TemplatesController extends BaseController
{
    /**
     * @var TemplateInterface
     */
    protected $templateRepo;

    /**
     * @param TemplateInterface $documentRepo
     */
    public function __construct(TemplateInterface $documentRepo)
    {
        $this->templateRepo = $documentRepo;
    }

    public function index(TemplateTable $table)
    {
        page_title()->setTitle(trans('plugins/real-estate::template.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/real-estate::template.create'));

        return $formBuilder->create(TemplateForm::class)->renderForm();
    }

    public function store(TemplateRequest $request, BaseHttpResponse $response)
    {
        $template = $this->templateRepo->createOrUpdate($request->input());

        event(new CreatedContentEvent(DESCRIPTION_TEMPLATE_MODULE_SCREEN_NAME, $request, $template));

        return $response
            ->setPreviousUrl(route('template.index'))
            ->setNextUrl(route('template.edit', $template->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        $template = $this->templateRepo->findOrFail($id);

        event(new BeforeEditContentEvent($request, $template));

        page_title()->setTitle(trans('plugins/real-estate::template.edit') . ' "' . $template->name . '"');

        return $formBuilder->create(TemplateForm::class, ['model' => $template])->renderForm();
    }

    public function update($id, TemplateRequest $request, BaseHttpResponse $response)
    {
        $template = $this->templateRepo->findOrFail($id);

        $template->fill($request->input());

        $this->templateRepo->createOrUpdate($template);

        event(new UpdatedContentEvent(DESCRIPTION_TEMPLATE_MODULE_SCREEN_NAME, $request, $template));

        return $response
            ->setPreviousUrl(route('template.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $template = $this->templateRepo->findOrFail($id);

            $this->templateRepo->delete($template);

            event(new DeletedContentEvent(DESCRIPTION_TEMPLATE_MODULE_SCREEN_NAME, $request, $template));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.cannot_delete'));
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
            $template = $this->templateRepo->findOrFail($id);
            $this->templateRepo->delete($template);
            event(new DeletedContentEvent(DESCRIPTION_TEMPLATE_MODULE_SCREEN_NAME, $request, $template));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}