<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Forms\CurrencyForm;
use Botble\RealEstate\Http\Requests\CurrencyRequest;
use Botble\RealEstate\Repositories\Interfaces\CurrencyInterface;
use Botble\Base\Forms\FormBuilder;
use Botble\RealEstate\Tables\CurrencyTable;
use Illuminate\Http\Request;

class CurrenciesController extends BaseController
{
    protected $currencyRepo;

    public function __construct(CurrencyInterface $currencyRepo)
    {
        $this->currencyRepo = $currencyRepo;
    }

    public function index(CurrencyTable $table)
    {
        page_title()->setTitle(trans('plugins/real-estate::currency.currencies'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/real-estate::currency.create'));

        return $formBuilder->create(CurrencyForm::class)->renderForm();
    }

    public function store(CurrencyRequest $request, BaseHttpResponse $response)
    {
        $currency = $this->currencyRepo->createOrUpdate($request->input());

        if ($currency->is_default) {
            $this->currencyRepo->markRestAsNotDefault($currency->id);
        }

        event(new CreatedContentEvent(CURRENCY_MODULE_SCREEN_NAME, $request, $currency));

        return $response
            ->setPreviousUrl(route('currencies.index'))
            ->setNextUrl(route('currencies.edit', $currency->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        $currency = $this->currencyRepo->findOrFail($id);

        event(new BeforeEditContentEvent($request, $currency));

        page_title()->setTitle(trans('plugins/real-estate::document.edit') . ' "' . $currency->id . '"');

        return $formBuilder->create(CurrencyForm::class, ['model' => $currency])->renderForm();
    }

    public function update($id, CurrencyRequest $request, BaseHttpResponse $response)
    {
        $currency = $this->currencyRepo->findOrFail($id);

        $currency->fill($request->input());

        $this->currencyRepo->createOrUpdate($currency);

        if ($request->input()['is_default'] == '1') {
            $this->currencyRepo->markRestAsNotDefault($currency->id);
        }

        event(new UpdatedContentEvent(CURRENCY_MODULE_SCREEN_NAME, $request, $currency));

        return $response
            ->setPreviousUrl(route('currencies.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $currency = $this->currencyRepo->findOrFail($id);

            $this->currencyRepo->delete($currency);

            event(new DeletedContentEvent(CURRENCY_MODULE_SCREEN_NAME, $request, $currency));

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
            $currency = $this->currencyRepo->findOrFail($id);
            $this->currencyRepo->delete($currency);
            event(new DeletedContentEvent(CURRENCY_MODULE_SCREEN_NAME, $request, $currency));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}