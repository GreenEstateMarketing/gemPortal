<?php

namespace Botble\Location\Http\Middleware;

use Botble\Location\Services\GeoIpService;
use Closure;
use Illuminate\Http\Request;

/**
 * Resolves session('visitor_location') once per session, from the visitor's
 * IP address, before any location-bound view renders. This is the fallback
 * path: if the visitor later grants browser geolocation, GeoController
 * overwrites this with a more precise, browser-sourced value.
 */
class ResolveVisitorLocation
{
    protected GeoIpService $geoIpService;

    public function __construct(GeoIpService $geoIpService)
    {
        $this->geoIpService = $geoIpService;
    }

    public function handle(Request $request, Closure $next)
    {
        if ($request->hasSession() && ! $request->session()->has('visitor_location')) {
            $request->session()->put('visitor_location', $this->geoIpService->resolveForRequest($request));
        }

        return $next($request);
    }
}
