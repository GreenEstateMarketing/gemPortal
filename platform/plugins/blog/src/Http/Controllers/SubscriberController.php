<?php

namespace Botble\Blog\Http\Controllers;

use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Traits\HasDeleteManyItemsTrait;
use Botble\Blog\Repositories\Interfaces\SubscriberInterface;
use Botble\Blog\Tables\SubscriberTable;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class SubscriberController extends BaseController
{
    use HasDeleteManyItemsTrait;

    /**
     * @var SubscriberInterface
     */
    protected $subscriberRepository;

    public function __construct(SubscriberInterface $subscriberRepository)
    {
        $this->subscriberRepository = $subscriberRepository;
    }

    /**
     * @param SubscriberTable $dataTable
     * @return Factory|View
     *
     * @throws Throwable
     */
    public function index(SubscriberTable $dataTable)
    {
        page_title()->setTitle(trans('plugins/blog::subscribers.menu'));

        return $dataTable->renderTable();
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
            $subscriber = $this->subscriberRepository->findOrFail($id);
            $this->subscriberRepository->delete($subscriber);

            event(new DeletedContentEvent('blog_subscribers', $request, $subscriber));

            return $response->setMessage(trans('plugins/blog::subscribers.deleted'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/blog::subscribers.cannot_delete'));
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     *
     * @throws Exception
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        return $this->executeDeleteItems($request, $response, $this->subscriberRepository, 'blog_subscribers');
    }
}
