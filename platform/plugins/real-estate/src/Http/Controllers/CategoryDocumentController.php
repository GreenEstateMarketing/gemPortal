<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Forms\CategoryDocumentForm;
use Botble\RealEstate\Http\Requests\CategoryDocumentRequest;
use Botble\RealEstate\Repositories\Interfaces\CategoryDocumentInterface;
use Botble\RealEstate\Tables\CategoryDocumentTable;
use Botble\Base\Forms\FormBuilder;
use Illuminate\Http\Request;

class CategoryDocumentController extends BaseController
{
    protected $categoryDocumentRepo;

    public function __construct(CategoryDocumentInterface $categoryDocumentRepo)
    {
        $this->categoryDocumentRepo = $categoryDocumentRepo;
    }

    public function index(CategoryDocumentTable $table)
    {
        page_title()->setTitle(trans('plugins/real-estate::category-document.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/real-estate::category-document.create'));

        return $formBuilder->create(CategoryDocumentForm::class)->renderForm();
    }

    public function store(CategoryDocumentRequest $request, BaseHttpResponse $response)
    {
        $categoryDocument = $this->categoryDocumentRepo->createOrUpdate($request->input());

        event(new CreatedContentEvent(CATEGORY_DOCUMENT_MODULE_SCREEN_NAME, $request, $categoryDocument));

        return $response
            ->setPreviousUrl(route('category-document.index'))
            ->setNextUrl(route('category-document.edit', $categoryDocument->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        $categoryDocument = $this->categoryDocumentRepo->findOrFail($id);

        event(new BeforeEditContentEvent($request, $categoryDocument));

        page_title()->setTitle(trans('plugins/real-estate::document.edit') . ' "' . $categoryDocument->document_id . '"');

        return $formBuilder->create(CategoryDocumentForm::class, ['model' => $categoryDocument])->renderForm();
    }

    public function update($id, CategoryDocumentRequest $request, BaseHttpResponse $response)
    {
        $categoryDocument = $this->categoryDocumentRepo->findOrFail($id);

        $categoryDocument->fill($request->input());

        $this->categoryDocumentRepo->createOrUpdate($categoryDocument);

        event(new UpdatedContentEvent(CATEGORY_DOCUMENT_MODULE_SCREEN_NAME, $request, $categoryDocument));

        return $response
            ->setPreviousUrl(route('category-document.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $categoryDocument = $this->categoryDocumentRepo->findOrFail($id);

            $this->categoryDocumentRepo->delete($categoryDocument);

            event(new DeletedContentEvent(CATEGORY_DOCUMENT_MODULE_SCREEN_NAME, $request, $categoryDocument));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (\Exception $exception) {
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
            $categoryDocument = $this->categoryDocumentRepo->findOrFail($id);
            $this->categoryDocumentRepo->delete($categoryDocument);
            event(new DeletedContentEvent(CATEGORY_DOCUMENT_MODULE_SCREEN_NAME, $request, $categoryDocument));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}