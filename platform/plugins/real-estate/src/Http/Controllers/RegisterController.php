<?php

namespace Botble\RealEstate\Http\Controllers;

use App\Http\Controllers\Controller;
use Botble\ACL\Traits\RegistersUsers;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Illuminate\Http\Request;
use URL;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = null;

    /**
     * @var AccountInterface
     */
    protected $accountRepository;

    /**
     * Create a new controller instance.
     *
     * @param AccountInterface $accountRepository
     */
    public function __construct(AccountInterface $accountRepository)
    {
        $this->accountRepository = $accountRepository;
        $this->redirectTo = route('public.account.register');
    }

    /**
     * Agents can only be added by an admin - self-registration is disabled.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showRegistrationForm()
    {
        return redirect()
            ->route('public.account.login')
            ->with('error_msg', __('Agent registration is not available. Please contact an administrator to get an account.'));
    }

    /**
     * Confirm a user with a given confirmation code.
     *
     * @param $email
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param AccountInterface $accountRepository
     * @return BaseHttpResponse
     */
    public function confirm($email, Request $request, BaseHttpResponse $response, AccountInterface $accountRepository)
    {
        if (!URL::hasValidSignature($request)) {
            abort(404);
        }

        $account = $accountRepository->getFirstBy(['email' => $email]);

        if (!$account) {
            abort(404);
        }

        $account->confirmed_at = now();
        $this->accountRepository->createOrUpdate($account);

        $this->guard()->login($account);

        return $response
            ->setNextUrl(route('public.account.dashboard'))
            ->setMessage(trans('plugins/real-estate::account.confirmation_successful'));
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return auth('account');
    }

    /**
     * Resend a confirmation code to a user.
     *
     * @param \Illuminate\Http\Request $request
     * @param AccountInterface $accountRepository
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function resendConfirmation(
        Request $request,
        AccountInterface $accountRepository,
        BaseHttpResponse $response
    ) {
        $account = $accountRepository->getFirstBy(['email' => $request->input('email')]);
        if (!$account) {
            return $response
                ->setError()
                ->setMessage(__('Cannot find this account!'));
        }
\Log::info('Sending confirmation email to: ' . $account->email);
        $this->sendConfirmationToUser($account);

        return $response
            ->setMessage(trans('plugins/real-estate::account.confirmation_resent'));
    }

    /**
     * Send the confirmation code to a user.
     *
     * @param Account $account
     */
    protected function sendConfirmationToUser($account)
    {
        // Notify the user
        $notificationConfig = config('plugins.real-estate.real-estate.notification');
        if ($notificationConfig) {
            $notification = app($notificationConfig);
            $account->notify($notification);
        }
    }

    /**
     * Agents can only be added by an admin - self-registration is disabled.
     *
     * @param \Illuminate\Http\Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function register(Request $request, BaseHttpResponse $response)
    {
        return $response
            ->setError()
            ->setNextUrl(route('public.account.login'))
            ->setMessage(__('Agent registration is not available. Please contact an administrator to get an account.'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getVerify()
    {
        return view('plugins/real-estate::account.auth.verify');
    }
}
