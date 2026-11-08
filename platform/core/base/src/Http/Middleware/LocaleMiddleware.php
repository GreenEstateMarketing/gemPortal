<?php

namespace Botble\Base\Http\Middleware;

use Botble\Base\Supports\Language;
use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

class LocaleMiddleware
{

    /**
     * @var \Illuminate\Foundation\Application|mixed
     */
    protected $app;

    /**
     * LocaleMiddleware constructor.
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->app = $application;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->app->setLocale(config('app.locale'));

        if ($request->session()->has('site-locale')) {
            $sessionLocale = $request->session()->get('site-locale');

            if (array_key_exists($sessionLocale, Language::getAvailableLocales()) && is_in_admin()) {
                $this->app->setLocale($sessionLocale);
                $request->setLocale($sessionLocale);

                return $next($request);
            }
        }

        if (!is_in_admin()) {
            $frontendLocale = $this->resolveFrontendLocale($request);

            if ($frontendLocale) {
                $this->app->setLocale($frontendLocale);
                $request->setLocale($frontendLocale);
            }
        }

        return $next($request);
    }

    /**
     * Resolve the preferred front-end locale for a non-admin request: the
     * authenticated member/account's saved preference takes precedence over
     * the "app_locale" cookie set by the public language switcher, so a
     * logged-in user's choice follows them across devices/browsers.
     *
     * @param Request $request
     * @return string|null
     */
    protected function resolveFrontendLocale(Request $request): ?string
    {
        $allowed = ['en', 'ur'];

        $guard = auth('member')->check() ? 'member' : (auth('account')->check() ? 'account' : null);

        if ($guard) {
            $accountLocale = auth($guard)->user()->locale ?? null;

            if ($accountLocale && in_array($accountLocale, $allowed, true)) {
                return $accountLocale;
            }
        }

        $cookieLocale = $request->cookie('app_locale');

        if ($cookieLocale && in_array($cookieLocale, $allowed, true)) {
            return $cookieLocale;
        }

        return null;
    }
}
