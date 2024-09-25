<?php

namespace Botble\AuditLog\Listeners;

use Botble\AuditLog\Events\AuditHandlerEvent;
use Botble\AuditLog\Repositories\Interfaces\AuditLogInterface;
use Illuminate\Http\Request;

class AuditHandlerListener
{
    use EncryptionTrait;

    /**
     * @var AuditLogInterface
     */
    public $auditLogRepository;

    /**
     * @var Request
     */
    protected $request;

    /**
     * AuditHandlerListener constructor.
     * @param AuditLogInterface $auditLogRepository
     * @param Request $request
     */
    public function __construct(AuditLogInterface $auditLogRepository, Request $request)
    {
        $this->auditLogRepository = $auditLogRepository;
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param AuditHandlerEvent $event
     * @return void
     */
    public function handle(AuditHandlerEvent $event)
    {
        $data = [
            'user_agent' => $this->encryptWithPublicKey($this->request->userAgent()),
            'ip_address' => $this->encryptWithPublicKey($this->request->ip()),
            'module' => $this->encryptWithPublicKey($event->module),
            'action' => $this->encryptWithPublicKey($event->action),
            'user_id' => $this->request->user() ? $this->request->user()->getKey() : 0,
            'reference_user' => $this->encryptWithPublicKey($event->referenceUser),
            'reference_id' => $this->encryptWithPublicKey($event->referenceId),
            'reference_name' => $this->encryptWithPublicKey($event->referenceName),
            'type' => $this->encryptWithPublicKey($event->type),
        ];

        if (!in_array($event->action, ['loggedin', 'password'])) {
            $data['request'] = json_encode($this->request->input());
        }

        $this->auditLogRepository->createOrUpdate($data);
    }
}
