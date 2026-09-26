<?php

namespace Botble\Location\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Location\Services\GeoIpService;
use Illuminate\Http\Request;

class GeoController extends BaseController
{
    /**
     * Called once the homepage's (or search page's) browser geolocation
     * prompt is granted and reverse-geocoded client-side. Upgrades the
     * IP-resolved session('visitor_location') to browser precision.
     */
    public function setBrowserLocation(Request $request, GeoIpService $geoIpService, BaseHttpResponse $response)
    {
        $data = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'city' => 'nullable|string|max:190',
            'country' => 'nullable|string|max:190',
        ]);

        $location = $geoIpService->resolveFromBrowser(
            $data['country'] ?? null,
            $data['city'] ?? null,
            (float) $data['lat'],
            (float) $data['lng']
        );

        $request->session()->put('visitor_location', $location);

        return $response->setData($location);
    }
}
