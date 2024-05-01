<?php

namespace Botble\RealEstate\Http\Controllers;

use App\Http\Controllers\Controller;
use Botble\ACL\Traits\RegistersUsers;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use SeoHelper;
use Theme;
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
     * Show the application registration form.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function showRegistrationForm()
    {
        SeoHelper::setTitle(__('Register'));

        if (view()->exists(Theme::getThemeNamespace() . '::views.real-estate.account.auth.register')) {
            return Theme::scope('real-estate.account.auth.register')->render();
        }

        return view('plugins/real-estate::account.auth.register');
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
     * Handle a registration request for the application.
     *
     * @param \Illuminate\Http\Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function register(Request $request, BaseHttpResponse $response)
    {
        $this->validator($request->input())->validate();

        event(new Registered($account = $this->create($request->input())));

        if (setting('verify_account_email', config('plugins.real-estate.real-estate.verify_email'))) {
            $this->sendConfirmationToUser($account);
            return $this->registered($request, $account)
                ?: $response->setNextUrl($this->redirectPath())
                    ->setMessage(trans('plugins/real-estate::account.confirmation_info'));
        }

        $account->confirmed_at = Carbon::now()->format('Y-m-d H:i:s');
        $this->accountRepository->createOrUpdate($account);
        $this->guard()->login($account);

        return $response->setNextUrl($this->redirectPath())->setMessage(__('Registered successfully!'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|max:120',
            'last_name'  => 'required|max:120',
            'username'   => 'required|max:60|min:2|unique:re_accounts,username',
            'email'      => 'required|email|max:255|unique:re_accounts',
            'password'   => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return Account
     */
    protected function create(array $data)
    {
        return $this->accountRepository->create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'username'   => $data['username'],
            'email'      => $data['email'],
            'password'   => bcrypt($data['password']),
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getVerify()
    {
        return view('plugins/real-estate::account.auth.verify');
    }
}
