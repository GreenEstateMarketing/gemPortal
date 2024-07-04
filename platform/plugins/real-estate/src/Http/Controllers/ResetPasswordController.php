<?php

namespace Botble\RealEstate\Http\Controllers;

use App\Http\Controllers\Controller;
use Botble\ACL\Traits\ResetsPasswords;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Member;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use SeoHelper;
use Illuminate\Support\Str;
use Theme;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = null;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redirectTo = route('public.account.dashboard');
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string|null $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function showResetForm(Request $request, $token = null)
    {
        SeoHelper::setTitle(__('Reset Password'));

        if (view()->exists(Theme::getThemeNamespace() . '::views.real-estate.account.auth.passwords.reset')) {
            return Theme::scope('real-estate.account.auth.passwords.reset', ['token' => $token, 'email' => $request->email])->render();
        }

        return view('plugins/real-estate::account.auth.passwords.reset', ['token' => $token, 'email' => $request->email]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $type = null;
        $email = $request->get('email');
        $agent = Account::where('email', $email)->first();
        if ($agent) {
            $type = 'agent';
        }

        if ($type == 'agent') {
            $status = Password::broker('accounts')->reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (Account $account, string $password) {
                    $account->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));

                    $account->save();

                    event(new PasswordReset($account));
                }
            );

            return $status === Password::PASSWORD_RESET
                ? redirect('/login')->with('status', __($status))
                : back()->withErrors(['email' => [__($status)]]);
        } else {
            $status = Password::broker('members')->reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (Member $member, string $password) {
                    $member->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));

                    $member->save();

                    event(new PasswordReset($member));
                }
            );

            return $status === Password::PASSWORD_RESET
                ? redirect()->route('member.login')->with('status', __($status))
                : back()->withErrors(['email' => [__($status)]]);
        }


    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker($type)
    {
        return Password::broker('members');
    }

    /**
     * Get the guard to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return auth('member');
    }
}
