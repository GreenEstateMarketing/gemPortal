<?php

namespace Botble\Location\Services;

use Botble\Location\Models\City;
use Botble\Location\Models\Country;
use GeoIp2\Database\Reader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Resolves a visitor's country/city either from their IP address (via a local
 * MaxMind GeoLite2 City database) or from browser-reported coordinates, and
 * shapes the result into the flat array stored in session('visitor_location').
 *
 * When nothing can be resolved (no database file, private/dev IP, no match in
 * the `countries` table) this falls back to country_id 166 (Pakistan) - the
 * value every location-bound query in this app was hardcoded to before this
 * service existed, so an unresolved visitor sees exactly the old behaviour.
 */
class GeoIpService
{
    /**
     * The app's pre-existing hardcoded default market - used as the last-resort
     * fallback when IP/browser location can't be resolved to a known country.
     */
    protected const DEFAULT_COUNTRY_ID = 166;

    /**
     * MaxMind GeoLite2's English country name => this app's `countries.name`,
     * for the cases where the two well-known datasets disagree. Not
     * exhaustive - add entries here as unmatched names show up in the log.
     */
    protected const COUNTRY_NAME_ALIASES = [
        'Russian Federation' => 'Russia',
        'Czechia' => 'Czech Republic',
        'Ivory Coast' => "Cote D'Ivoire",
        'Congo (Kinshasa)' => 'Democratic Republic of the Congo',
        'Congo (Brazzaville)' => 'Congo',
        'The Bahamas' => 'Bahamas',
        'Myanmar (Burma)' => 'Myanmar',
        'North Macedonia' => 'Macedonia',
        'Cabo Verde' => 'Cape Verde',
        'Eswatini' => 'Swaziland',
        'Vatican City' => 'Holy See (Vatican City State)',
        'South Korea' => 'Korea, South',
        'North Korea' => "Korea, North",
    ];

    public function resolveForRequest(Request $request): array
    {
        $geo = $this->lookupIp((string) $request->ip());

        if ($geo) {
            $country = $this->matchCountry($geo['country_name']);

            if ($country) {
                $city = $geo['city_name'] ? $this->matchCity($geo['city_name'], $country->id) : null;

                return $this->buildLocation('ip', $country, $city, $geo['lat'], $geo['lng']);
            }
        }

        return $this->defaultLocation();
    }

    public function resolveFromBrowser(?string $countryName, ?string $cityName, float $lat, float $lng): array
    {
        $country = $countryName ? $this->matchCountry($countryName) : null;

        if (! $country) {
            return $this->defaultLocation('browser', $lat, $lng);
        }

        $city = $cityName ? $this->matchCity($cityName, $country->id) : null;

        return $this->buildLocation('browser', $country, $city, $lat, $lng);
    }

    public function defaultLocation(string $source = 'default', ?float $lat = null, ?float $lng = null): array
    {
        $country = Country::find(self::DEFAULT_COUNTRY_ID);

        return $this->buildLocation($source, $country, null, $lat, $lng);
    }

    protected function lookupIp(string $ip): ?array
    {
        $path = $this->databasePath();

        if (! $path || ! is_readable($path)) {
            return null;
        }

        if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            // Private/reserved/local IP (e.g. dev environments) - can't be geolocated.
            return null;
        }

        try {
            $record = (new Reader($path))->city($ip);
        } catch (Throwable $e) {
            Log::info('GeoIP lookup failed for ' . $ip . ': ' . $e->getMessage());

            return null;
        }

        return [
            'country_name' => $record->country->name,
            'city_name' => $record->city->name,
            'lat' => $record->location->latitude,
            'lng' => $record->location->longitude,
        ];
    }

    public function databasePath(): string
    {
        return storage_path('app/geoip/GeoLite2-City.mmdb');
    }

    public function matchCountry(?string $name): ?Country
    {
        if (! $name) {
            return null;
        }

        $aliased = isset(self::COUNTRY_NAME_ALIASES[$name]) ? self::COUNTRY_NAME_ALIASES[$name] : $name;

        $country = Country::whereRaw('LOWER(name) = ?', [strtolower($aliased)])->first();

        if (! $country) {
            Log::info("GeoIP: no countries row matches resolved country name [$name] - consider adding an alias in GeoIpService::COUNTRY_NAME_ALIASES.");
        }

        return $country;
    }

    public function matchCity(?string $name, int $countryId): ?City
    {
        if (! $name) {
            return null;
        }

        // Prefer a city whose state actually exists. Some countries' city
        // data in this app has duplicate name rows left over from an earlier,
        // incomplete import whose states were never linked (a real example:
        // Pakistan has 455 such orphaned rows alongside 3 correctly-linked
        // ones - see [[project_visitor_geolocation]]) - without this, a
        // resolved city name can silently match the orphaned twin instead of
        // the one existing properties/projects actually use.
        return City::query()
            ->select('cities.*')
            ->leftJoin('states', 'states.id', '=', 'cities.state_id')
            ->where('cities.country_id', $countryId)
            ->whereRaw('LOWER(cities.name) = ?', [strtolower($name)])
            ->orderByRaw('states.id IS NULL')
            ->first();
    }

    protected function buildLocation(string $source, ?Country $country, ?City $city, ?float $lat, ?float $lng): array
    {
        return [
            'source' => $source,
            'country_id' => $country ? $country->id : self::DEFAULT_COUNTRY_ID,
            'country_name' => $country ? $country->name : null,
            'city_id' => $city ? $city->id : null,
            'city_name' => $city ? $city->name : null,
            'state_id' => $city ? $city->state_id : null,
            'lat' => $lat,
            'lng' => $lng,
        ];
    }
}
