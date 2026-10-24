<?php

namespace Botble\RealEstate\Http\Controllers;

use App\Http\Controllers\Controller;
use Botble\ACL\Traits\LogoutGuardTrait;
use Botble\ACL\Traits\AuthenticatesUsers;
use Botble\RealEstate\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Log;
use SeoHelper;
use Theme;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, LogoutGuardTrait {
        attemptLogin as baseAttemptLogin;
    }

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo;

    /**
     * Create a new controller instance.
     *
     * This used to also stamp session('url.intended') from URL::previous()
     * here, on every request including the login POST itself. That
     * clobbered whatever a genuine redirect-to-login (e.g. Laravel's own
     * auth middleware bouncing an unauthenticated agent off a protected
     * page) had already stored, with just the referrer of whichever
     * request happened to hit this controller next - which, for a POST
     * submit, is always the login page itself. See authenticated() below
     * for where the post-login redirect is actually decided now.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redirectTo = route('public.account.dashboard');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function showLoginForm()
    {
         SeoHelper::setTitle(trans('plugins/real-estate::account.login'));

        if (view()->exists(Theme::getThemeNamespace() . '::views.real-estate.account.auth.login')) {
            return Theme::scope('real-estate.account.auth.login')->render();
        }

        return view('plugins/real-estate::account.auth.login');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return auth('account');
    }

    /**
     * Decide where to send an agent right after a successful login.
     *
     * The base sendLoginResponse() (AuthenticatesUsers trait) calls
     * redirect()->intended($this->redirectPath()), and Laravel's
     * intended() always prefers session('url.intended') over the
     * $this->redirectTo computed in the constructor above when that
     * session key is set. Since the constructor stamps url.intended from
     * the referring page on every request - including the login POST
     * itself, whose Referer is always the login page the form lives on -
     * that session value is essentially always one of login/home/register
     * by the time login succeeds, which silently defeats the dashboard
     * redirect the constructor computed. Agents ended up bounced back to
     * whichever trivial page they came from instead of their dashboard.
     *
     * Forgetting url.intended here when it's one of those trivial pages
     * lets intended() fall back to the dashboard, while a genuinely
     * useful intended URL (e.g. Laravel's own auth middleware redirecting
     * from a protected page to login) is still honored.
     *
     * Trailing slashes are stripped before comparing - browsers send the
     * homepage's Referer as "http://host/" while route('public.single')
     * resolves to "http://host" (no trailing slash), so a naive exact
     * match never recognizes the homepage as "trivial" and this would
     * silently fail to fix the bug it's here for.
     *
     * @param \Illuminate\Http\Request $request
     * @param Account $user
     * @return \Illuminate\Http\RedirectResponse|null
     */
    protected function authenticated(Request $request, $user)
    {
        $pages = array_map(fn ($url) => rtrim($url, '/'), [
            route('public.account.login'),
            route('public.single'),
            route('public.account.register'),
        ]);

        if (in_array(rtrim((string) session('url.intended'), '/'), $pages)) {
            session()->forget('url.intended');
        }

        return redirect()->intended(route('public.account.dashboard'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        
        $request->merge([$this->username() => $request->input('email')]);
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     * @throws ValidationException
     */
    protected function attemptLogin(Request $request)
    {
        $account = Account::where($this->username(), $request->input($this->username()))->first();

        if (!$account) {
            throw ValidationException::withMessages([
                'email' => 'Invalid ' . $this->username(),
            ]);
        }

        if ($this->guard()->validate($this->credentials($request))) {
            $account = $this->guard()->getLastAttempted();

            if (setting('verify_account_email',
                    config('plugins.real-estate.real-estate.verify_email')) && empty($account->confirmed_at)) {
                throw ValidationException::withMessages([
                    'confirmation' => [
                        trans('plugins/real-estate::account.not_confirmed', [
                            'resend_link' => route('public.account.resend_confirmation', ['email' => $account->email]),
                        ]),
                    ],
                ]);
            }

            return $this->baseAttemptLogin($request);
        }

        return false;
    }

    /**
     * @return string
     */
    public function username()
    {
        return filter_var(request()->input('email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $activeGuards = 0;
        $this->guard()->logout();

        foreach (config('auth.guards', []) as $guard => $guardConfig) {
            if ($guardConfig['driver'] !== 'session') {
                continue;
            }
            if ($this->isActiveGuard($request, $guard)) {
                $activeGuards++;
            }
        }

        if (!$activeGuards) {
            $request->session()->flush();
            $request->session()->regenerate();
        }

        return $this->loggedOut($request) ?: redirect('/');
    }

    protected function sendFailedLoginResponse()
    {
        throw ValidationException::withMessages([
            'password' => 'Invalid Password',
        ]);
    }
}
