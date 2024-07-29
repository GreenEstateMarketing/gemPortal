<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\RealEstate\Http\Requests\PackageRequest;
use Botble\RealEstate\Repositories\Interfaces\PackageInterface;
use Botble\Base\Http\Controllers\BaseController;
use Botble\RealEstate\Repositories\Interfaces\DocumentInterface;
use Botble\RealEstate\Tables\DocumentTable;
use Illuminate\Http\Request;
use Exception;
use Botble\RealEstate\Tables\PackageTable;
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
     * PackageController constructor.
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
//        page_title()->setTitle(trans('plugins/real-estate::package.create'));
//
//        return $formBuilder->create(PackageForm::class)->renderForm();
    }

    /**
     * @param PackageRequest $request
     * @return BaseHttpResponse
     */
    public function store(PackageRequest $request, BaseHttpResponse $response)
    {
//        $package = $this->packageRepository->createOrUpdate($request->input());
//
//        event(new CreatedContentEvent(PACKAGE_MODULE_SCREEN_NAME, $request, $package));
//
//        return $response
//            ->setPreviousUrl(route('package.index'))
//            ->setNextUrl(route('package.edit', $package->id))
//            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
//        $package = $this->packageRepository->findOrFail($id);
//
//        event(new BeforeEditContentEvent($request, $package));
//
//        page_title()->setTitle(trans('plugins/real-estate::package.edit') . ' "' . $package->name . '"');
//
//        return $formBuilder->create(PackageForm::class, ['model' => $package])->renderForm();
    }

    /**
     * @param $id
     * @param PackageRequest $request
     * @return BaseHttpResponse
     */
    public function update($id, PackageRequest $request, BaseHttpResponse $response)
    {
//        $package = $this->packageRepository->findOrFail($id);
//
//        $package->fill($request->input());
//
//        $this->packageRepository->createOrUpdate($package);
//
//        event(new UpdatedContentEvent(PACKAGE_MODULE_SCREEN_NAME, $request, $package));
//
//        return $response
//            ->setPreviousUrl(route('package.index'))
//            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param $id
     * @param Request $request
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
//        try {
//            $package = $this->packageRepository->findOrFail($id);
//
//            $this->packageRepository->delete($package);
//
//            event(new DeletedContentEvent(PACKAGE_MODULE_SCREEN_NAME, $request, $package));
//
//            return $response->setMessage(trans('core/base::notices.delete_success_message'));
//        } catch (Exception $exception) {
//            return $response
//                ->setError()
//                ->setMessage(trans('core/base::notices.cannot_delete'));
//        }
    }
}
