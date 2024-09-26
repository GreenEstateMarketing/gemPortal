<?php

namespace Botble\AuditLog\Http\Controllers;

use Botble\AuditLog\ActivitiesTransformer;
use Botble\AuditLog\Repositories\Interfaces\AuditLogInterface;
use Botble\AuditLog\Tables\AuditLogTable;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Traits\HasDeleteManyItemsTrait;
use Cache;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Log;
use Throwable;

class AuditLogController extends BaseController
{

    use HasDeleteManyItemsTrait;

    /**
     * @var AuditLogInterface
     */
    protected $auditLogRepository;

    protected $transformer;

    /**
     * AuditLogController constructor.
     * @param AuditLogInterface $auditLogRepository
     */
    public function __construct(AuditLogInterface $auditLogRepository, ActivitiesTransformer $activitiesTransformer)
    {
        $this->auditLogRepository = $auditLogRepository;
        $this->transformer = $activitiesTransformer;
    }

    /**
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function getWidgetActivities(BaseHttpResponse $response)
    {
        try {
            $limit = request()->input('paginate', 10);
            $histories = $this->auditLogRepository
                ->getModel()
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate($limit);

            if (Cache::has('decryption_key') && Cache::get('decryption_key') != null) {
                $histories = $this->transformer->transform($histories);
            }

            return $response
                ->setData(view('plugins/audit-log::widgets.activities', compact('histories', 'limit'))->render());
        } catch (Exception $e) {
            Cache::forget('decryption_key');
            return $response
                ->setError()
                ->setMessage($e->getMessage());
        }

    }

    /**
     * @param AuditLogTable $dataTable
     * @return Factory|View
     * @throws Throwable
     */
    public function index(AuditLogTable $dataTable)
    {
        page_title()->setTitle(trans('plugins/audit-log::history.name'));

        return $dataTable->renderTable();
    }

    /**
     * @param Request $request
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $log = $this->auditLogRepository->findOrFail($id);
            $this->auditLogRepository->delete($log);

            event(new DeletedContentEvent(AUDIT_LOG_MODULE_SCREEN_NAME, $request, $log));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $ex) {
            return $response
                ->setError()
                ->setMessage($ex->getMessage());
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
        return $this->executeDeleteItems($request, $response, $this->auditLogRepository, AUDIT_LOG_MODULE_SCREEN_NAME);
    }

    /**
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function deleteAll(BaseHttpResponse $response)
    {
        $this->auditLogRepository->getModel()->truncate();

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    public function decrypt(Request $request, BaseHttpResponse $response)
    {
        try {
            $decryption_key = $request->get('decryption_key');

            if ($decryption_key == '' || $decryption_key == null) {
                return $response
                    ->setError()
                    ->setMessage('Please enter valid decryption key.');
            }

            Cache::put('decryption_key', $decryption_key, 120);

            return $response
                ->setPreviousUrl(route('dashboard.index'))
                ->setNextUrl(route('dashboard.index'));
        } catch (Exception $e) {
            return $response
                ->setError()
                ->setMessage('Something went wrong');
        }

    }
}
