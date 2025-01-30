<?php

namespace Botble\AuditLog\Listeners;

use Botble\ACL\Models\User;
use Botble\AuditLog\Models\AuditHistory;
use Illuminate\Auth\Events\Login;

class LoginListener
{
    use EncryptionTrait;

    /**
     * @var AuditHistory
     */
    public $auditHistory;

    /**
     * AuditHandlerListener constructor.
     * @param AuditHistory $auditHistory
     */
    public function __construct(AuditHistory $auditHistory)
    {
        $this->auditHistory = $auditHistory;
    }

    /**
     * Handle the event.
     *
     * @param Login $event
     * @return void
     */
    public function handle(Login $event)
    {
        /**
         * @var User $user
         */
        $user = $event->user;

        if ($user instanceof User) {
            $this->auditHistory->user_agent = $this->encryptWithPublicKey(request()->userAgent());
            $this->auditHistory->ip_address = $this->encryptWithPublicKey(request()->ip());
            $this->auditHistory->module = $this->encryptWithPublicKey('to the system');
            $this->auditHistory->action = $this->encryptWithPublicKey('logged in');
            $this->auditHistory->user_id = $user->id;
            $this->auditHistory->reference_user = $this->encryptWithPublicKey(0);
            $this->auditHistory->reference_id = $this->encryptWithPublicKey($user->id);
            $this->auditHistory->reference_name = $this->encryptWithPublicKey($user->getFullName());
            $this->auditHistory->type = $this->encryptWithPublicKey('info');

            $this->auditHistory->save();
        }
    }
}
