<?php

namespace Botble\SocialLogin\Http\Controllers;

use Assets;
use Botble\RealEstate\Repositories\Interfaces\MemberInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Setting\Supports\SettingStore;
use Botble\SocialLogin\Http\Requests\SocialLoginRequest;
use Exception;
use Illuminate\Support\Str;
use Socialite;

class SocialLoginController extends BaseController
{

    /**
     * Redirect the user to the {provider} authentication page.
     *
     * Agents don't get social login at all right now - only members do,
     * regardless of the admin's per-provider settings - so anything that
     * doesn't explicitly ask for the member flow is refused here before
     * ever reaching the provider.
     *
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param string $provider
     * @return mixed
     */
    public function redirectToProvider(Request $request, BaseHttpResponse $response, $provider)
    {
        if ($request->query('type') !== 'member') {
            return $response
                ->setError()
                ->setNextUrl(route('public.account.login'))
                ->setMessage(__('Social login is not available for agents.'));
        }

        session(['social_login_type' => 'member']);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from {provider}.
     * @param string $provider
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function handleProviderCallback($provider, BaseHttpResponse $response)
    {
        $type = session()->pull('social_login_type');

        if ($type !== 'member') {
            return $response
                ->setError()
                ->setNextUrl(route('public.account.login'))
                ->setMessage(__('Social login is not available for agents.'));
        }

        try {
            /**
             * @var \Laravel\Socialite\AbstractUser $oAuth
             */
            $oAuth = Socialite::driver($provider)->user();
        } catch (Exception $ex) {
            return $response
                ->setError()
                ->setNextUrl(route('member.login'))
                ->setMessage($ex->getMessage());
        }

        if (!$oAuth->getEmail()) {
            return $response
                ->setError()
                ->setNextUrl(route('member.login'))
                ->setMessage(__('Cannot login, no email provided!'));
        }

        return $this->loginMemberViaSocial($oAuth, $response);
    }

    /**
     * @param \Laravel\Socialite\AbstractUser $oAuth
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    protected function loginMemberViaSocial($oAuth, BaseHttpResponse $response)
    {
        $user = app(MemberInterface::class)->getFirstBy(['email' => $oAuth->getEmail()]);

        if (!$user) {
            $user = app(MemberInterface::class)->createOrUpdate([
                'full_name'      => $oAuth->getName(),
                'email'          => $oAuth->getEmail(),
                'mobile_no'      => '',
                'password'       => bcrypt(Str::random(36)),
                'email_verified' => true,
            ]);
        }

        Auth::guard('member')->login($user, true);

        return $response
            ->setNextUrl(route('member.dashboard'))
            ->setMessage(trans('core/acl::auth.login.success'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSettings()
    {
        page_title()->setTitle(trans('plugins/social-login::social-login.settings.title'));

        Assets::addScriptsDirectly('vendor/core/plugins/social-login/js/social-login.js');

        return view('plugins/social-login::settings');
    }

    /**
     * @param SocialLoginRequest $request
     * @param BaseHttpResponse $response
     * @param SettingStore $setting
     * @return BaseHttpResponse
     */
    public function postSettings(SocialLoginRequest $request, BaseHttpResponse $response, SettingStore $setting)
    {
        foreach ($request->except(['_token']) as $settingKey => $settingValue) {
            $setting->set($settingKey, $settingValue);
        }

        $setting->save();

        return $response
            ->setPreviousUrl(route('social-login.settings'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }
}
