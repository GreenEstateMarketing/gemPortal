<?php

use App\Exceptions\Handler;
use App\Http\Middleware\Member;
use App\Http\Middleware\PreventBackHistory;
use Botble\ACL\Http\Middleware\Authenticate;
use Botble\ACL\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        // web:/api: deliberately omitted — app/Providers/RouteServiceProvider.php keeps
        // registering routes/web.php and routes/api.php itself, exactly as it did on L10.
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(headers:
            Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_AWS_ELB
        );

        $middleware->trimStrings(except: ['password', 'password_confirmation']);

        // L11's withMiddleware() unconditionally defaults guests to route('login'),
        // which doesn't exist here (Botble names it 'access.login'). Without this,
        // every unauthenticated request throws RouteNotFoundException before
        // Botble\Base\Exceptions\Handler::unauthenticated() ever gets a chance to
        // run its own route('access.login') redirect.
        $middleware->redirectGuestsTo(fn () => route('access.login'));

        // app/Http/Kernel.php had AuthenticateSession active in the 'web' group
        // (not a Laravel default) — this is the purpose-built fluent equivalent.
        $middleware->authenticateSessions();

        // Custom 'member' group (not a default Laravel group).
        $middleware->group('member', [Member::class]);

        // Replaces $routeMiddleware. Merges into L11's default alias set.
        $middleware->alias([
            'auth' => Authenticate::class,
            'guest' => RedirectIfAuthenticated::class,
            'member' => Member::class,
            'preventBackHistory' => PreventBackHistory::class,
        ]);
    })
    ->create();

// Exception handler kept as a manual singleton bind, not migrated to withExceptions().
// platform/core/base/src/Providers/BaseServiceProvider.php still rebinds this same
// contract to Botble\Base\Exceptions\Handler at boot — unchanged from L10.
$app->singleton(ExceptionHandler::class, Handler::class);

return $app;
