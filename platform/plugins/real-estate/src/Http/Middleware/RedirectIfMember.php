<?php

namespace Botble\RealEstate\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfMember
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'member')
    {
        if (Auth::guard($guard)->check()) {
            return redirect(route('public.account.dashboard'));
        }

        return $next($request);
    }
}
