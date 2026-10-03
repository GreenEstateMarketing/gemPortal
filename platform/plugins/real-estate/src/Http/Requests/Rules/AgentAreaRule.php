<?php

namespace Botble\RealEstate\Http\Requests\Rules;

use Illuminate\Contracts\Validation\Rule;

class AgentAreaRule implements Rule
{
    public function passes($attribute, $value)
    {
        return static::hasPolygon($value);
    }

    public function message()
    {
        return "Please draw the agent's coverage area on the map.";
    }

    /**
     * Detect whether a submitted agent_area/agent_area_edit value contains
     * at least one real polygon. Accepts both formats the map UI can emit:
     * the JS-drawn format (array of polygons, each an array of {lat,lng}
     * points) and the raw GeoJSON format the server renders for an
     * untouched existing polygon ({"type": "Polygon"|"MultiPolygon", ...}).
     */
    public static function hasPolygon($value): bool
    {
        $decoded = json_decode((string) $value, true);

        if (empty($decoded) || !is_array($decoded)) {
            return false;
        }

        if (isset($decoded['type'], $decoded['coordinates'])) {
            if ($decoded['type'] === 'Polygon') {
                return !empty($decoded['coordinates'][0]) && count($decoded['coordinates'][0]) >= 3;
            }

            if ($decoded['type'] === 'MultiPolygon') {
                foreach ($decoded['coordinates'] as $polygon) {
                    if (!empty($polygon[0]) && count($polygon[0]) >= 3) {
                        return true;
                    }
                }
            }

            return false;
        }

        foreach ($decoded as $polygon) {
            if (is_array($polygon) && count($polygon) >= 3) {
                return true;
            }
        }

        return false;
    }
}
