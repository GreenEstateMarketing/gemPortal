<?php

namespace Botble\RealEstate\Http\Controllers;

use App\Http\Controllers\Controller;
use Botble\ACL\Traits\SendsPasswordResetEmails;
use Botble\RealEstate\Http\Requests\API\ForgotPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use SeoHelper;
use Theme;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function showLinkRequestForm(Request $request)
    {
        $type = $request->has('type') ? $request->get('type') : null;
        
        SeoHelper::setTitle(trans('plugins/real-estate::account.forgot_password'));

        if (view()->exists(Theme::getThemeNamespace() . '::views.real-estate.account.auth.passwords.email')) {
            return Theme::scope('real-estate.account.auth.passwords.email', compact('type'))->render();
        }

        return view('plugins/real-estate::account.auth.passwords.email');
    }

    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        $type = $request->has('type') ? $request->get('type') : null;
        $this->validateEmail($request);
        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker($type)->sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($request, $response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker($type)
    {
        if($type == 'agent') {
            return Password::broker('accounts');
        }

        return Password::broker('members');
    }


}
