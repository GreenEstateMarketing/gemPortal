<?php

namespace Botble\RealEstate\Repositories\Eloquent;

use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\DeclareDeclare;

class AccountRepository extends RepositoriesAbstract implements AccountInterface
{
    /**
     * {@inheritDoc}
     */
    public function createUsername($name, $id = null)
    {
        $username = Str::slug($name);
        $index = 1;
        $baseSlug = $username;
        while ($this->model->where('username', $username)->where('id', '!=', $id)->count() > 0) {
            $username = $baseSlug . '-' . $index++;
        }

        if (empty($username)) {
            $username = $baseSlug . '-' . time();
        }

        $this->resetModel();

        return $username;
    }
    public function agents()
    {
        $res = $this->model->where('confirmed_at', '!=', null)->get();
        return $res;
    }

    /**
     * Filter-driven agent search, following the same shape as
     * PropertyRepository::getProperties(): build the non-trivial pieces
     * (whereHas, joins, raw distance select) directly against $this->model,
     * then delegate pagination/order-by/withCount to advancedGet().
     *
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function searchAgents(array $filters = [])
    {
        $filters = array_merge([
            'keyword' => null,
            'country_id' => null,
            'city_id' => null,
            'language_ids' => [],
            'category_ids' => [],
            'min_experience' => null,
            'max_experience' => null,
            'lat' => null,
            'lng' => null,
            'sort_by' => null,
            'per_page' => 10,
            'current_paged' => 1,
        ], $filters);

        $this->model = $this->originalModel;

        $this->model = $this->model->where('confirmed_at', '!=', null);

        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $this->model = $this->model->where(function ($query) use ($keyword) {
                $query->where('first_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $keyword . '%');
            });
        }

        if (!empty($filters['city_id'])) {
            $this->model = $this->model->where('city_id', $filters['city_id']);
        } elseif (!empty($filters['country_id'])) {
            $this->model = $this->model->whereIn('city_id', function ($query) use ($filters) {
                $query->select('id')
                    ->from('cities')
                    ->where('country_id', $filters['country_id']);
            });
        }

        if (!empty($filters['language_ids'])) {
            $this->model = $this->model->whereHas('spokenLanguages', function ($query) use ($filters) {
                $query->whereIn('re_spoken_languages.id', $filters['language_ids']);
            });
        }

        if (!empty($filters['category_ids'])) {
            $this->model = $this->model->whereHas('specialties', function ($query) use ($filters) {
                $query->whereIn('re_categories.id', $filters['category_ids']);
            });
        }

        if ($filters['min_experience'] !== null && $filters['max_experience'] !== null) {
            $this->model = $this->model->whereBetween('years_of_experience', [
                (int) $filters['min_experience'],
                (int) $filters['max_experience'],
            ]);
        }

        $nearMe = $filters['lat'] !== null && $filters['lng'] !== null;

        if ($nearMe) {
            // Agents have no standalone lat/lng - reuse the centroid of the
            // coverage polygon they already draw via the agent map tool
            // (same axis-order=long-lat convention as
            // Account::scopeCoveringPoint, so ST_X/ST_Y line up with how
            // that polygon was written). ST_Centroid() itself doesn't
            // support geographic SRID 4326 in MySQL ("has not been
            // implemented for geographic spatial reference systems") -
            // ST_SRID(agent_area, 0) reinterprets the same long/lat values
            // under the flat Cartesian SRID so ST_Centroid can run; fine as
            // an approximation at agent-coverage-area scale. Agents with no
            // coverage area yet sort after everyone with a computed
            // distance, instead of dropping out of the results.
            $centroid = 'ST_Centroid(ST_SRID(agent_area, 0))';
            $this->model = $this->model->selectRaw(
                're_accounts.*, ' .
                'CASE WHEN agent_area IS NOT NULL THEN (' .
                '6371 * ACOS(LEAST(1, GREATEST(-1, ' .
                "COS(RADIANS(?)) * COS(RADIANS(ST_Y($centroid))) * " .
                "COS(RADIANS(ST_X($centroid)) - RADIANS(?)) + " .
                "SIN(RADIANS(?)) * SIN(RADIANS(ST_Y($centroid)))" .
                ')))) ELSE NULL END AS distance',
                [$filters['lat'], $filters['lng'], $filters['lat']]
            );

            // MySQL sorts NULL first on ASC by default - push agents with no
            // computed distance (no agent_area) to the end instead.
            $this->model = $this->model->orderByRaw('distance IS NULL');
        }

        $orderBy = [];

        if ($nearMe) {
            $orderBy = ['distance' => 'asc'];
        } elseif ($filters['sort_by'] === 'name_asc') {
            $orderBy = ['re_accounts.first_name' => 'asc'];
        } elseif ($filters['sort_by'] === 'name_desc') {
            $orderBy = ['re_accounts.first_name' => 'desc'];
        } else {
            $orderBy = ['properties_count' => 'desc'];
        }

        return $this->advancedGet([
            'condition' => [],
            'order_by' => $orderBy,
            'with' => ['spokenLanguages', 'specialties'],
            'withCount' => [
                'properties as properties_count' => function ($query) {
                    $query->where('moderation_status', ModerationStatusEnum::APPROVED);
                },
            ],
            'paginate' => [
                'per_page' => (int) $filters['per_page'],
                'current_paged' => (int) $filters['current_paged'],
            ],
            // Left empty on purpose: when $nearMe applied a selectRaw() to
            // $this->model above, advancedGet()'s own ->select() call would
            // otherwise clobber it (Eloquent's select() replaces rather than
            // merges). An empty/falsy value here makes advancedGet skip
            // calling select() at all, defaulting to SELECT * either way.
            'select' => [],
        ]);
    }
    function getPolygon($id)
    {
        $res = $this->model->selectRaw('ST_AsGeoJson(agent_area) as poly_coord')->where('id', '=', $id)->get();
        if(env('SWAP_CORD', 'true')) {
            return $this->swapCoordinates($res[0]->poly_coord);
        }

        return $res[0]->poly_coord;
    }

    private function swapCoordinates($geoJson)
    {
        $data = json_decode($geoJson, true);
        if($data) {
            if ($data['type'] === 'Polygon') {
                foreach ($data['coordinates'] as &$polygon) {
                    foreach ($polygon as &$ring) {
                        $ring = array_reverse($ring);
                    }
                }
            } else if ($data['type'] === 'MultiPolygon') {
                foreach ($data['coordinates'] as &$outerArray) {
                    foreach ($outerArray as &$polygon) {
                        foreach ($polygon as &$ring) {
                            $ring = array_reverse($ring);
                        }
                    }
                }
            }
            return json_encode($data);
        }
    }
}
