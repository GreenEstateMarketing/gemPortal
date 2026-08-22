<?php

namespace Botble\RealEstate\Repositories\Eloquent;

use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use DB;

class PropertyRepository extends RepositoriesAbstract implements PropertyInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRelatedProperties($filters = [], $limit = 4)
    {

        $this->model = $this->originalModel;
        $this->model = $this->model->where('id', '<>', $filters['property_id']);
        $this->model = $this->model->where('category_id', '=', $filters['category_id']);
        $this->model = $this->model->where('city_id', '=', $filters['city_id'])
            ->notExpired();

        $params = [
            'condition' => [
                ['re_properties.status', 'NOT_IN', [PropertyStatusEnum::NOT_AVAILABLE]],
                're_properties.moderation_status' => ModerationStatusEnum::APPROVED,
            ],
            'order_by' => [
                're_properties.created_at' => 'DESC',
            ],
            'take' => $limit,
            'paginate' => [
                'per_page' => 12,
                'current_paged' => 1,
            ],
            'select' => [
                're_properties.*',
            ],
            'with' => [],
        ];

        return $this->advancedGet($params);
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties($filters = [], $params = [])
    {
        $filters = array_merge([
            'keyword' => null,
            'type' => null,
            'bedroom' => null,
            'bathroom' => null,
            'floor' => null,
            'min_square' => null,
            'max_square' => null,
            'min_price' => null,
            'max_price' => null,
            'project' => null,
            'category_id' => null,
            'city_id' => null,
            'city' => null,
            'location' => null,
            'sort_by' => null,
            'latitude' => null,
            'longitude' => null,
            'latLngs-1' => null,
            'latLngs-2' => null,
            'latLngs-3' => null,
            'latLngs-4' => null,
        ], $filters);

        switch ($filters['sort_by']) {
            case 'date_asc':
                $orderBy = [
                    're_properties.created_at' => 'asc',
                ];
                break;
            case 'date_desc':
                $orderBy = [
                    're_properties.created_at' => 'desc',
                ];
                break;
            case 'price_asc':
                $orderBy = [
                    're_properties.price' => 'asc',
                ];
                break;
            case 'price_desc':
                $orderBy = [
                    're_properties.price' => 'desc',
                ];
                break;
            case 'name_asc':
                $orderBy = [
                    're_properties.name' => 'asc',
                ];
                break;
            case 'name_desc':
                $orderBy = [
                    're_properties.name' => 'desc',
                ];
                break;
            default:
                $orderBy = [
                    're_properties.created_at' => 'DESC',
                ];
                break;
        }

        $params = array_merge([
            'condition' => [
                ['re_properties.status', 'NOT_IN', [PropertyStatusEnum::NOT_AVAILABLE]],
                're_properties.moderation_status' => ModerationStatusEnum::APPROVED,
            ],
            'order_by' => [
                're_properties.created_at' => 'DESC',
            ],
            'take' => null,
            'paginate' => [
                'per_page' => 10,
                'current_paged' => 1,
            ],
            'select' => [
                're_properties.*',
            ],
            'with' => [],
        ], $params);

        $params['order_by'] = $orderBy;

        $this->model = $this->originalModel;

        $this->model = $this->model->notExpired();

        if ($filters['keyword'] !== null) {
            //$areaData = explode(',',$filters['keyword']);
            $areaData = $filters['keyword'];
            //print_r($areaData);exit;
            if (count($areaData) >= 1) {

                $areaData = implode(',', $areaData);

                $cityAreasChild = 'WITH RECURSIVE tree AS (
                                               SELECT id,
                                                      city_area_name,
                                                      parent_id,
                                                      1 as level
                                               FROM city_area
                                               WHERE id in ( ' . $areaData . ')

                                               UNION ALL

                                               SELECT p.id,
                                                      p.city_area_name,
                                                      p.parent_id,
                                                      t.level + 1
                                               FROM city_area p
                                                 JOIN tree t ON t.id = p.parent_id
                                            )
                                            SELECT DISTINCT id
                                            FROM tree';
                $cityAreasChild = DB::select($cityAreasChild);

                $cityAreasChild = implode(',', array_column($cityAreasChild, 'id'));
                $cityAreasChild = explode(',', $cityAreasChild);

                $this->model = $this->model
                    ->whereIn('re_properties.city_area_id', $cityAreasChild);

                /*foreach ($areaData as $key=>$val)
                {
                    //  $this->model= $this->model->where('cities.name', 'LIKE', '%' . trim($areaData[0]) . '%');
                    if($key==0)
                    {
                        $this->model = $this->model
                            ->where(function (Builder $query) use ($val) {
                                return $query
                                    ->where('re_properties.name', 'LIKE', '%' . trim($val) . '%')
                                    ->orWhere('re_properties.location', 'LIKE', '%' . trim($val) . '%');
                            });
                    }
                    else
                    {
                        $this->model = $this->model
                            ->orwhere(function (Builder $query) use ($val) {
                                return $query
                                    ->where('re_properties.name', 'LIKE', '%' . trim($val) . '%')
                                    ->orWhere('re_properties.location', 'LIKE', '%' . trim($val) . '%');
                            });
                    }
                }*/
            } /*else {
         $this->model = $this->model
             ->where(function (Builder $query) use ($filters) {
                 return $query
                     ->where('re_properties.name', 'LIKE', '%' . $filters['keyword'] . '%')
                     ->orWhere('re_properties.location', 'LIKE', '%' . $filters['keyword'] . '%');
             });
     }*/


        }

        if ($filters['type'] !== null) {
            if ($filters['type'] == PropertyTypeEnum::SALE) {
                $this->model = $this->model->where('re_properties.type', $filters['type'])
                    ->where('re_properties.status', PropertyStatusEnum::SELLING);
            } else {
                $this->model = $this->model->where('re_properties.type', $filters['type'])
                    ->where('re_properties.status', PropertyStatusEnum::RENTING);
            }
        }

        if ($filters['bedroom']) {
            if ($filters['bedroom'] < 5) {
                $this->model = $this->model->where('re_properties.number_bedroom', $filters['bedroom']);
            } else {
                $this->model = $this->model->where('re_properties.number_bedroom', '>=', $filters['bedroom']);
            }
        }

        if ($filters['bathroom']) {
            if ($filters['bathroom'] < 5) {
                $this->model = $this->model->where('re_properties.number_bathroom', $filters['bathroom']);
            } else {
                $this->model = $this->model->where('re_properties.number_bathroom', '>=', $filters['bathroom']);
            }
        }

        if ($filters['floor']) {
            if ($filters['floor'] < 5) {
                $this->model = $this->model->where('re_properties.number_floor', $filters['floor']);
            } else {
                $this->model = $this->model->where('re_properties.number_floor', '>=', $filters['floor']);
            }
        }

        if ($filters['min_square'] !== null || $filters['max_square'] !== null) {
            $this->model = $this->model
                ->where(function ($query) use ($filters) {
                    $minSquare = Arr::get($filters, 'min_square');
                    $maxSquare = Arr::get($filters, 'max_square');

                    /**
                     * @var \Illuminate\Database\Query\Builder $query
                     */
                    if ($minSquare !== null) {
                        $query = $query->where('re_properties.square', '>=', $minSquare);
                    }

                    if ($maxSquare !== null) {
                        $query = $query->where('re_properties.square', '<=', $maxSquare);
                    }

                    return $query;
                });
        }

        if ($filters['min_price'] !== null || $filters['max_price'] !== null) {
            $this->model = $this->model
                ->where(function ($query) use ($filters) {

                    $minPrice = Arr::get($filters, 'min_price');
                    $maxPrice = Arr::get($filters, 'max_price');

                    /**
                     * @var Builder $query
                     */
                    if ($minPrice !== null) {
                        $query = $query->where('re_properties.price', '>=', $minPrice);
                    }

                    if ($maxPrice !== null) {
                        $query = $query->where('re_properties.price', '<=', $maxPrice);
                    }

                    return $query;
                });
        }
        if ($filters['latitude'] !== null || $filters['longitude'] !== null) {
            $this->model = $this->model
                ->where(function ($query) use ($filters) {

                    $latitude = Arr::get($filters, 'latitude');
                    $longitude = Arr::get($filters, 'longitude');

                    /**
                     * @var Builder $query
                     */
                    if ($latitude !== null) {
                        $query = $query->where('re_properties.latitude', '=', $latitude);
                    }

                    if ($longitude !== null) {
                        $query = $query->where('re_properties.longitude', '=', $longitude);
                    }
                    return $query;
                });
        }
        if ($filters['latLngs-1'] !== null && $filters['latLngs-2'] !== null && $filters['latLngs-3'] !== null && $filters['latLngs-4'] !== null) {
            $this->model = $this->model
                ->where(function ($query) use ($filters) {

                    $latLngs1 = Arr::get($filters, 'latLngs-1');
                    $latLngs2 = Arr::get($filters, 'latLngs-2');
                    $latLngs3 = Arr::get($filters, 'latLngs-3');
                    $latLngs4 = Arr::get($filters, 'latLngs-4');
                    $latLngs1 = explode(',', $latLngs1);
                    $latLngs2 = explode(',', $latLngs2);
                    $latLngs3 = explode(',', $latLngs3);
                    $latLngs4 = explode(',', $latLngs4);
                    /**
                     * @var Builder $query
                     */
                    if ($latLngs1 !== null && $latLngs2 != null && $latLngs3 != null && $latLngs4 != null) {
                        $latitude[0] = $latLngs1[0];
                        $latitude[1] = $latLngs2[0];
                        $longitude[0] = $latLngs1[1];
                        $longitude[1] = $latLngs4[1];
                        $query = $query->whereBetween('re_properties.latitude', $latitude);
                        $query = $query->whereBetween('re_properties.longitude', $longitude);
                        /*$bindings = $query->getBindings();
                        $sql = str_replace('?', '%s', $query->toSql());
                        $sql = sprintf($sql, ...$bindings);
                        dd($sql);
                        echo $query->toSql();exit;*/
                    }
                    return $query;
                });
        }

        if ($filters['city'] !== null) {
            $this->model = $this->model
                ->join('cities', 'cities.id', '=', 're_properties.city_id')
                ->where('cities.slug', $filters['city']);
        }

        if ($filters['project'] !== null) {
            $this->model = $this->model->where('re_properties.project_id', $filters['project']);
        }

        if ($filters['category_id'] !== null) {
            $parent_category = app(\Botble\RealEstate\Repositories\Interfaces\CategoryInterface::class)->select(['re_categories.id'])->where('parent_id', $filters['category_id'])->get()->toArray();
            $sub_id = array_column($parent_category, 'id');
            if (!empty($parent_category)) {
                $sub_id[] = $filters['category_id'];
                $this->model = $this->model->whereIn('re_properties.category_id', $sub_id);
            } else {
                $this->model = $this->model->where('re_properties.category_id', $filters['category_id']);
            }
        }

        if ($filters['city_id']) {
            $this->model = $this->model->where('re_properties.city_id', $filters['city_id']);
        } elseif ($filters['location']) {
            $locationData = explode(',', $filters['location']);
            if (count($locationData) > 1) {
                $this->model = $this->model
                    ->join('cities', 'cities.id', '=', 're_properties.city_id')
                    ->where('cities.name', 'LIKE', '%' . trim($locationData[0]) . '%');
            } else {
                $this->model = $this->model
                    ->join('cities', 'cities.id', '=', 're_properties.city_id')
                    ->where('cities.name', 'LIKE', '%' . $filters['location'] . '%');
            }
        }
        return $this->advancedGet($params);
    }

    /**
     * {@inheritDoc}
     */
    public function getProperty(int $propertyId, array $with = [])
    {
        $params = [
            'condition' => [
                're_properties.id' => $propertyId,
                're_properties.moderation_status' => ModerationStatusEnum::APPROVED,
            ],
            'with' => $with,
            'take' => 1,
        ];

        $this->model = $this->originalModel;

        $this->model = $this->model->notExpired();

        return $this->advancedGet($params);
    }

    /**
     * {@inheritDoc}
     */
    public function getPropertiesByConditions(array $condition, $limit, array $with = [])
    {
        $this->model = $this->originalModel;

        $this->model = $this->model->notExpired();

        $params = [
            'condition' => $condition,
            'with' => $with,
            'take' => $limit,
            'order_by' => ['re_properties.created_at' => 'DESC'],
        ];

        return $this->advancedGet($params);
    }
    public function getPropertiesByMap($filters = [], $params = [])
    {
        $filters = array_merge([
            'keyword' => null,
            'type' => null,
            'bedroom' => null,
            'bathroom' => null,
            'floor' => null,
            'min_square' => null,
            'max_square' => null,
            'unit' => null,
            'min_price' => null,
            'max_price' => null,
            'project' => null,
            'category_id' => null,
            'city_id' => null,
            'city' => null,
            'location' => null,
            'sort_by' => null,
            'latitude' => null,
            'longitude' => null,
            'points' => null,
        ], $filters);

        switch ($filters['sort_by']) {
            case 'date_asc':
                $orderBy = [
                    're_properties.created_at' => 'asc',
                ];
                break;
            case 'date_desc':
                $orderBy = [
                    're_properties.created_at' => 'desc',
                ];
                break;
            case 'price_asc':
                $orderBy = [
                    're_properties.price' => 'asc',
                ];
                break;
            case 'price_desc':
                $orderBy = [
                    're_properties.price' => 'desc',
                ];
                break;
            case 'name_asc':
                $orderBy = [
                    're_properties.name' => 'asc',
                ];
                break;
            case 'name_desc':
                $orderBy = [
                    're_properties.name' => 'desc',
                ];
                break;
            default:
                $orderBy = [
                    're_properties.created_at' => 'DESC',
                ];
                break;
        }

        $params = array_merge([
            'condition' => [
                ['re_properties.status', 'NOT_IN', [PropertyStatusEnum::NOT_AVAILABLE]],
                're_properties.moderation_status' => ModerationStatusEnum::APPROVED,
            ],
            'order_by' => [
                're_properties.created_at' => 'DESC',
            ],
            'take' => null,
            'paginate' => [
                'per_page' => 10,
                'current_paged' => 1,
            ],
            'select' => [
                're_properties.*',
            ],
            'with' => [],
        ], $params);

        $params['order_by'] = $orderBy;

        $this->model = $this->originalModel;

        $this->model = $this->model->notExpired();

        if ($filters['keyword'] !== null) {

            $areaData = $filters['keyword'];

            if (count($areaData) >= 1) {

                $areaData = implode(',', $areaData);

                $cityAreasChild = 'WITH RECURSIVE tree AS (
                                               SELECT id,
                                                      city_area_name,
                                                      parent_id,
                                                      1 as level
                                               FROM city_area
                                               WHERE id in ( ' . $areaData . ')

                                               UNION ALL

                                               SELECT p.id,
                                                      p.city_area_name,
                                                      p.parent_id,
                                                      t.level + 1
                                               FROM city_area p
                                                 JOIN tree t ON t.id = p.parent_id
                                            )
                                            SELECT DISTINCT id
                                            FROM tree';
                $cityAreasChild = DB::select($cityAreasChild);

                $cityAreasChild = implode(',', array_column($cityAreasChild, 'id'));
                $cityAreasChild = explode(',', $cityAreasChild);

                $this->model = $this->model
                    ->whereIn('re_properties.city_area_id', $cityAreasChild);
            }
        }

        if ($filters['type'] !== null) {
            if ($filters['type'] == PropertyTypeEnum::SALE) {
                $this->model = $this->model->where('re_properties.type', $filters['type'])
                    ->where('re_properties.status', PropertyStatusEnum::SELLING);
            } else {
                $this->model = $this->model->where('re_properties.type', $filters['type'])
                    ->where('re_properties.status', PropertyStatusEnum::RENTING);
            }
        }

        if ($filters['bedroom']) {
            if ($filters['bedroom'] < 5) {
                $this->model = $this->model->where('re_properties.number_bedroom', $filters['bedroom']);
            } else {
                $this->model = $this->model->where('re_properties.number_bedroom', '>=', $filters['bedroom']);
            }
        }

        if ($filters['bathroom']) {
            if ($filters['bathroom'] < 5) {
                $this->model = $this->model->where('re_properties.number_bathroom', $filters['bathroom']);
            } else {
                $this->model = $this->model->where('re_properties.number_bathroom', '>=', $filters['bathroom']);
            }
        }

        if ($filters['floor']) {
            if ($filters['floor'] < 5) {
                $this->model = $this->model->where('re_properties.number_floor', $filters['floor']);
            } else {
                $this->model = $this->model->where('re_properties.number_floor', '>=', $filters['floor']);
            }
        }

        //changing min and max square values according to square feet
        if ($filters['min_square'] !== null || $filters['max_square'] !== null || $filters['unit'] !== null) {
            switch ($filters['unit']) {
                case 'ft²':
                    $filters['min_square'] = $filters['min_square'];
                    $filters['max_square'] = $filters['max_square'];
                    break;
                case 'm²':
                    $filters['min_square'] = $filters['min_square'] * 10.764;
                    $filters['max_square'] = $filters['max_square'] * 10.764;
                    break;
                case 'marla':
                    $filters['min_square'] = $filters['min_square'] * 225;
                    $filters['max_square'] = $filters['max_square'] * 225;
                    break;
                case 'yard':
                    $filters['min_square'] = $filters['min_square'] * 9;
                    $filters['max_square'] = $filters['max_square'] * 9;
                    break;
                case 'kanal':
                    $filters['min_square'] = $filters['min_square'] * 4500;
                    $filters['max_square'] = $filters['max_square'] * 4500;
                    break;
            }

            $this->model = $this->model
                ->where(function ($query) use ($filters) {
                    //                    $minSquare = Arr::get($filters, 'min_square');
//                    $maxSquare = Arr::get($filters, 'max_square');
                    /**
                     * @var \Illuminate\Database\Query\Builder $query
                     */
                    if ($filters['min_square'] > 0) {
                        $query = $query->where('re_properties.square', '>=', (int) $filters['min_square']);
                    }

                    if ($filters['max_square'] > 0) {

                        $query = $query->where('re_properties.square', '<=', (int) $filters['max_square']);
                        //                        dd($query->get());
                    }


                    return $query;
                });
        }

        if ($filters['min_price'] !== null || $filters['max_price'] !== null) {
            $this->model = $this->model
                ->where(function ($query) use ($filters) {

                    $minPrice = Arr::get($filters, 'min_price');
                    $maxPrice = Arr::get($filters, 'max_price');

                    /**
                     * @var Builder $query
                     */
                    if ($minPrice !== null) {
                        $query = $query->where('re_properties.price', '>=', $minPrice);
                    }

                    if ($maxPrice !== null) {
                        $query = $query->where('re_properties.price', '<=', $maxPrice);
                    }

                    return $query;
                });
        }
        if ($filters['latitude'] !== null || $filters['longitude'] !== null) {
            $this->model = $this->model
                ->where(function ($query) use ($filters) {

                    $latitude = Arr::get($filters, 'latitude');
                    $longitude = Arr::get($filters, 'longitude');

                    /**
                     * @var Builder $query
                     */
                    if ($latitude !== null) {
                        $query = $query->where('re_properties.latitude', '=', $latitude);
                    }

                    if ($longitude !== null) {
                        $query = $query->where('re_properties.longitude', '=', $longitude);
                    }
                    return $query;
                });
        }
        if (isset($filters['coordinates']) && count($filters['coordinates']) > 0) {
            $this->model = $this->model
                ->where(function ($query) use ($filters) {

                    $coordinates = $filters['coordinates'];

                    // Round coordinates to 6 decimal places for better comparison
                    $polygon = [];
                    foreach ($coordinates as $coordinate) {
                        $polygon[] = [round($coordinate['lng'], 6), round($coordinate['lat'], 6)]; // GeoJSON format: [longitude, latitude]
                    }

                    // Add the first point again to close the polygon
                    $polygon[] = [round($coordinates[0]['lng'], 6), round($coordinates[0]['lat'], 6)];

                    // Debug the generated polygon
                    $polygonText = "POLYGON((" . implode(',', array_map(function ($point) {
                        return $point[0] . ' ' . $point[1];
                    }, $polygon)) . "))";

                    // Use a spatial query to check if the point is inside the polygon
                    $query->whereRaw('ST_Contains(
                        ST_GeomFromText("' . $polygonText . '"), 
                        POINT(longitude, latitude)
                    )');

                    return $query;
                });
        }

        if ($filters['city'] !== null) {
            $this->model = $this->model
                ->join('cities', 'cities.id', '=', 're_properties.city_id')
                ->where('cities.slug', $filters['city']);
        }

        if ($filters['project'] !== null) {
            $this->model = $this->model->where('re_properties.project_id', $filters['project']);
        }

        if ($filters['category_id'] !== null) {
            $parent_category = app(\Botble\RealEstate\Repositories\Interfaces\CategoryInterface::class)->select(['re_categories.id'])->where('parent_id', $filters['category_id'])->get()->toArray();
            $sub_id = array_column($parent_category, 'id');
            if (!empty($parent_category)) {
                $sub_id[] = $filters['category_id'];
                $this->model = $this->model->whereIn('re_properties.category_id', $sub_id);
            } else {
                $this->model = $this->model->where('re_properties.category_id', $filters['category_id']);
            }
        }

        if ($filters['city_id']) {
            $this->model = $this->model->where('re_properties.city_id', $filters['city_id']);
        } elseif ($filters['location']) {
            $locationData = explode(',', $filters['location']);
            if (count($locationData) > 1) {
                $this->model = $this->model
                    ->join('cities', 'cities.id', '=', 're_properties.city_id')
                    ->where('cities.name', 'LIKE', '%' . trim($locationData[0]) . '%');
            } else {
                $this->model = $this->model
                    ->join('cities', 'cities.id', '=', 're_properties.city_id')
                    ->where('cities.name', 'LIKE', '%' . $filters['location'] . '%');
            }
        }

        //$bindings = $this->model->getBindings();
        // $sql = str_replace('?', '%s', $this->model->toSql());
        // $sql = sprintf($sql, ...$bindings);
        //echo $sql;exit;
        return $this->advancedGet($params);
    }

    public function delete(Model $model)
    {
        $model->is_deleted = 1;
        $model->save();
    }
}
