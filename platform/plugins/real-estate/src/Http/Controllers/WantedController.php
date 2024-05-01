<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\RealEstate\Http\Requests\CategoryRequest;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Repositories\Interfaces\CategoryInterface;
use Botble\RealEstate\Repositories\Interfaces\WantedInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Exception;
use Botble\RealEstate\Tables\CategoryTable;
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
     * @var CategoryInterface
     */
    protected $wantedRepository;

    /**
     * WantedController constructor.
     * @param CategoryInterface $wantedRepository
     */
    public function __construct(WantedInterface $wantedRepository)
    {
        $this->wantedRepository =$wantedRepository;
    }

    /**
     * @param CategoryTable $dataTable
     * @return Factory|View
     * @throws Throwable
     */
    public function index(WantedTable $table)
    {

        page_title()->setTitle(trans('plugins/real-estate::wanted.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/real-estate::category.create'));

        return $formBuilder->create(CategoryForm::class)->renderForm();
    }

    /**
     * Insert new Category into database
     *
     * @param CategoryRequest $request
     * @return BaseHttpResponse
     */
    public function store(CategoryRequest $request, BaseHttpResponse $response)
    {
        $category = $this->categoryRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(PROPERTY_CATEGORY_MODULE_SCREEN_NAME, $request, $category));

        return $response
            ->setPreviousUrl(route('property_category.index'))
            ->setNextUrl(route('property_category.edit', $category->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
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
        $category = $this->categoryRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $category));

        page_title()->setTitle(trans('plugins/real-estate::category.edit') . ' "' . $category->name . '"');

        return $formBuilder->create(CategoryForm::class, ['model' => $category])->renderForm();
    }
    public function view($id, FormBuilder $formBuilder, Request $request)
    {
        $wanted = $this->wantedRepository->findOrFail($id);
        $category_id=$wanted->category_id;
       event(new BeforeEditContentEvent($request, $wanted));

        page_title()->setTitle(trans('plugins/real-estate::wanted.name'));

        return $formBuilder->create(WantedForm::class, ['model' => $wanted])->renderForm();
    }

    /**
     * @param $id
     * @param CategoryRequest $request
     * @return BaseHttpResponse
     */
    public function update($id, CategoryRequest $request, BaseHttpResponse $response)
    {
        $category = $this->categoryRepository->findOrFail($id);

        $category->fill($request->input());

        $this->categoryRepository->createOrUpdate($category);

        event(new UpdatedContentEvent(PROPERTY_CATEGORY_MODULE_SCREEN_NAME, $request, $category));

        return $response
            ->setPreviousUrl(route('property_category.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
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

            event(new DeletedContentEvent(PROPERTY_CATEGORY_MODULE_SCREEN_NAME, $request, $wanted));

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
            $category = $this->categoryRepository->findOrFail($id);
            $this->categoryRepository->delete($category);
            event(new DeletedContentEvent(PROPERTY_CATEGORY_MODULE_SCREEN_NAME, $request, $category));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
