<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\RealEstate\Forms\DocumentForm;
use Botble\RealEstate\Http\Requests\DocumentRequest;
use Botble\Base\Http\Controllers\BaseController;
use Botble\RealEstate\Repositories\Interfaces\DocumentInterface;
use Botble\RealEstate\Tables\DocumentTable;
use Illuminate\Http\Request;
use Exception;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Forms\FormBuilder;

class DocumentController extends BaseController
{
    /**
     * @var DocumentInterface
     */
    protected $documentRepo;

    /**
     * @param DocumentInterface $documentRepo
     */
    public function __construct(DocumentInterface $documentRepo)
    {
        $this->documentRepo = $documentRepo;
    }
    
    public function index(DocumentTable $table)
    {
        page_title()->setTitle(trans('plugins/real-estate::document.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/real-estate::document.create'));

        return $formBuilder->create(DocumentForm::class)->renderForm();
    }

    public function store(DocumentRequest $request, BaseHttpResponse $response)
    {
        $data = [
            'name' => $request->input('name'),
            'type' => implode(',', $request->input('type')),
        ];

        $document = $this->documentRepo->createOrUpdate($data);

        event(new CreatedContentEvent(DOCUMENT_MODULE_SCREEN_NAME, $request, $document));

        return $response
            ->setPreviousUrl(route('document.index'))
            ->setNextUrl(route('document.edit', $document->id))
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
        $document = $this->documentRepo->findOrFail($id);

        event(new BeforeEditContentEvent($request, $document));

        page_title()->setTitle(trans('plugins/real-estate::document.edit') . ' "' . $document->name . '"');

        return $formBuilder->create(DocumentForm::class, ['model' => $document])->renderForm();
    }

    public function update($id, DocumentRequest $request, BaseHttpResponse $response)
    {
        $document = $this->documentRepo->findOrFail($id);

        $data = [
            'name' => $request->input('name'),
            'type' => implode(',', $request->input('type')),
        ];

        $document->fill($data);

        $this->documentRepo->createOrUpdate($document);

        event(new UpdatedContentEvent(DOCUMENT_MODULE_SCREEN_NAME, $request, $document));

        return $response
            ->setPreviousUrl(route('document.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $document = $this->documentRepo->findOrFail($id);

            $this->documentRepo->delete($document);

            event(new DeletedContentEvent(DOCUMENT_MODULE_SCREEN_NAME, $request, $document));

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
            $document = $this->documentRepo->findOrFail($id);
            $this->documentRepo->delete($document);
            event(new DeletedContentEvent(DOCUMENT_MODULE_SCREEN_NAME, $request, $document));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
