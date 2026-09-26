<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Forms\SpokenLanguageForm;
use Botble\RealEstate\Http\Requests\SpokenLanguageRequest;
use Botble\RealEstate\Repositories\Interfaces\SpokenLanguageInterface;
use Botble\RealEstate\Tables\SpokenLanguageTable;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class SpokenLanguageController extends BaseController
{
    /**
     * @var SpokenLanguageInterface
     */
    protected $spokenLanguageRepository;

    public function __construct(SpokenLanguageInterface $spokenLanguageRepository)
    {
        $this->spokenLanguageRepository = $spokenLanguageRepository;
    }

    /**
     * @param SpokenLanguageTable $dataTable
     * @return JsonResponse|View
     * @throws Throwable
     */
    public function index(SpokenLanguageTable $dataTable)
    {
        page_title()->setTitle(trans('plugins/real-estate::spoken-language.name'));

        return $dataTable->renderTable();
    }

    /**
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        return $formBuilder->create(SpokenLanguageForm::class)->renderForm();
    }

    /**
     * @param SpokenLanguageRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(SpokenLanguageRequest $request, BaseHttpResponse $response)
    {
        $spokenLanguage = $this->spokenLanguageRepository->create($request->all());

        event(new CreatedContentEvent(SPOKEN_LANGUAGE_MODULE_SCREEN_NAME, $request, $spokenLanguage));

        return $response
            ->setPreviousUrl(route('agent_spoken_language.index'))
            ->setNextUrl(route('agent_spoken_language.edit', $spokenLanguage->id))
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
        $spokenLanguage = $this->spokenLanguageRepository->findOrFail($id);
        page_title()->setTitle(trans('plugins/real-estate::spoken-language.edit') . ' "' . $spokenLanguage->name . '"');

        event(new BeforeEditContentEvent($request, $spokenLanguage));

        return $formBuilder->create(SpokenLanguageForm::class, ['model' => $spokenLanguage])->renderForm();
    }

    /**
     * @param int $id
     * @param SpokenLanguageRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, SpokenLanguageRequest $request, BaseHttpResponse $response)
    {
        $spokenLanguage = $this->spokenLanguageRepository->findOrFail($id);

        $spokenLanguage->fill($request->input());
        $this->spokenLanguageRepository->createOrUpdate($spokenLanguage);

        event(new UpdatedContentEvent(SPOKEN_LANGUAGE_MODULE_SCREEN_NAME, $request, $spokenLanguage));

        return $response
            ->setPreviousUrl(route('agent_spoken_language.index'))
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
            $spokenLanguage = $this->spokenLanguageRepository->findOrFail($id);
            $this->spokenLanguageRepository->delete($spokenLanguage);

            event(new DeletedContentEvent(SPOKEN_LANGUAGE_MODULE_SCREEN_NAME, $request, $spokenLanguage));

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
            $spokenLanguage = $this->spokenLanguageRepository->findOrFail($id);
            $this->spokenLanguageRepository->delete($spokenLanguage);

            event(new DeletedContentEvent(SPOKEN_LANGUAGE_MODULE_SCREEN_NAME, $request, $spokenLanguage));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
