<?php

namespace Botble\RealEstate\Repositories\Eloquent;

use Botble\RealEstate\Enums\ProjectStatusEnum;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use DB;

class ProjectRepository extends RepositoriesAbstract implements ProjectInterface
{
    /**
     * {@inheritdoc}
     */
    public function getProjects($filters = [], $params = [])
    {
        $filters = array_merge([
            'keyword' => null,
            'city' => null,
            'min_floor' => null,
            'max_floor' => null,
            'blocks' => null,
            'min_flat' => null,
            'max_flat' => null,
            'category_id' => null,
            'city_id' => null,
            'location' => null,
            'sort_by' => null,
        ], $filters);

        switch ($filters['sort_by']) {
            case 'date_asc':
                $orderBy = [
                    're_projects.created_at' => 'asc',
                ];
                break;
            case 'date_desc':
                $orderBy = [
                    're_projects.created_at' => 'desc',
                ];
                break;
            case 'price_asc':
                $orderBy = [
                    're_projects.price_from' => 'asc',
                ];
                break;
            case 'price_desc':
                $orderBy = [
                    're_projects.price_from' => 'desc',
                ];
                break;
            case 'name_asc':
                $orderBy = [
                    're_projects.name' => 'asc',
                ];
                break;
            case 'name_desc':
                $orderBy = [
                    're_projects.name' => 'desc',
                ];
                break;
            default:
                $orderBy = [
                    're_projects.created_at' => 'DESC',
                ];
                break;
        }

        $params = array_merge([
            'condition' => [],
            'order_by' => [
                're_projects.created_at' => 'DESC',
            ],
            'take' => null,
            'paginate' => [
                'per_page' => 10,
                'current_paged' => 1,
            ],
            'select' => [
                're_projects.*',
            ],
            'with' => [],
        ], $params);

        $params['order_by'] = $orderBy;

        $this->model = $this->originalModel;

        /* if ($filters['keyword'] !== null) {
             $this->model = $this->model
                 ->where(function (Builder $query) use ($filters) {
                     return $query
                         ->whereIn('re_projects.name', 'LIKE', '%' . $filters['keyword'] . '%')
                         ->orWhere('re_projects.location', 'LIKE', '%' . $filters['keyword'] . '%');
                 });
         }*/

        if ($filters['city'] !== null) {
            $this->model = $this->model
                ->join('cities', 'cities.id', '=', 're_projects.city_id')
                ->where('cities.slug', $filters['city']);
        }

        if ($filters['blocks']) {
            if ($filters['blocks'] < 5) {
                $this->model = $this->model->where('re_projects.number_block', $filters['blocks']);
            } else {
                $this->model = $this->model->where('re_projects.number_block', '>=', $filters['blocks']);
            }
        }

        if ($filters['min_floor'] !== null || $filters['max_floor'] !== null) {
            $this->model = $this->model
                ->where(function ($query) use ($filters) {
                    $minFloor = Arr::get($filters, 'min_floor');
                    $maxFloor = Arr::get($filters, 'max_floor');

                    /**
                     * @var \Illuminate\Database\Query\Builder $query
                     */
                    if ($minFloor !== null) {
                        $query = $query->where('re_projects.number_floor', '>=', $minFloor);
                    }

                    if ($maxFloor !== null) {
                        $query = $query->where('re_projects.number_floor', '<=', $maxFloor);
                    }

                    return $query;
                });
        }

        if ($filters['min_flat'] !== null || $filters['max_flat'] !== null) {
            $this->model = $this->model
                ->where(function ($query) use ($filters) {
                    $minFlat = Arr::get($filters, 'min_flat');
                    $maxFlat = Arr::get($filters, 'max_flat');

                    /**
                     * @var \Illuminate\Database\Query\Builder $query
                     */
                    if ($minFlat !== null) {
                        $query = $query->where('re_projects.number_flat', '>=', $minFlat);
                    }

                    if ($maxFlat !== null) {
                        $query = $query->where('re_projects.number_flat', '<=', $maxFlat);
                    }

                    return $query;
                });
        }

        if ($filters['category_id'] !== null) {
            $this->model = $this->model->where('re_projects.category_id', $filters['category_id']);
        }

        if ($filters['city_id']) {
            $this->model = $this->model->where('re_projects.city_id', $filters['city_id']);
        } elseif ($filters['location']) {
            $locationData = explode(',', $filters['location']);
            if (count($locationData) > 1) {
                $this->model = $this->model
                    ->join('cities', 'cities.id', '=', 're_projects.city_id')
                    ->where('cities.name', 'LIKE', '%' . trim($locationData[0]) . '%');
            } else {
                $this->model = $this->model
                    ->join('cities', 'cities.id', '=', 're_projects.city_id')
                    ->where('cities.name', 'LIKE', '%' . $filters['location'] . '%');
            }
        }

        $this->model->whereNotIn('re_projects.status', [ProjectStatusEnum::NOT_AVAILABLE]);

        return $this->advancedGet($params);
    }

    /**
     * {@inheritdoc}
     */
    public function getRelatedProjects(int $projectId, $limit = 4)
    {
        $this->model = $this->originalModel;
        $this->model = $this->model
            ->where('id', '<>', $projectId);

        $params = [
            'condition' => [],
            'order_by' => [
                'created_at' => 'DESC',
            ],
            'take' => $limit,
            'paginate' => [
                'per_page' => 12,
                'current_paged' => 1,
            ],
            'select' => [
                're_projects.*',
            ],
            'with' => [],
        ];

        return $this->advancedGet($params);
    }

    public function getProjectsMaps($filters = [], $params = [])
    {
        
        $filters = array_merge([
            'keyword' => null,
            'city' => null,
            'min_floor' => null,
            'max_floor' => null,
            'blocks' => null,
            'min_flat' => null,
            'max_flat' => null,
            'category_id' => null,
            'city_id' => null,
            'location' => null,
            'sort_by' => null,
            'min_price' => null,
            'max_price' => null,
            'floor' => null,
            'flat' => null
        ], $filters);

        switch ($filters['sort_by']) {
            case 'date_asc':
                $orderBy = [
                    're_projects.created_at' => 'asc',
                ];
                break;
            case 'date_desc':
                $orderBy = [
                    're_projects.created_at' => 'desc',
                ];
                break;
            case 'price_asc':
                $orderBy = [
                    're_projects.price_from' => 'asc',
                ];
                break;
            case 'price_desc':
                $orderBy = [
                    're_projects.price_from' => 'desc',
                ];
                break;
            case 'name_asc':
                $orderBy = [
                    're_projects.name' => 'asc',
                ];
                break;
            case 'name_desc':
                $orderBy = [
                    're_projects.name' => 'desc',
                ];
                break;
            default:
                $orderBy = [
                    're_projects.created_at' => 'DESC',
                ];
                break;
        }

        $params = array_merge([
            'condition' => [],
            'order_by' => [
                're_projects.created_at' => 'DESC',
            ],
            'take' => null,
            'paginate' => [
                'per_page' => 10,
                'current_paged' => 1,
            ],
            'select' => [
                're_projects.*',
            ],
            'with' => [],
        ], $params);

        $params['order_by'] = $orderBy;

        $this->model = $this->originalModel;

        if ($filters['keyword'] !== null) {
            //$areaData = explode(',',$filters['keyword']);
            $areaData = $filters['keyword'];

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
            $cityAreasChild = explode(',',$cityAreasChild);

            $this->model = $this->model
                ->whereIn('re_projects.city_area_id', $cityAreasChild);
            //print_r($areaData);exit;
            /* if (count($areaData) >= 1) {
                 foreach ($areaData as $key=>$val)
                 {
                     //  $this->model= $this->model->where('cities.name', 'LIKE', '%' . trim($areaData[0]) . '%');
                     if($key==0)
                     {
                         $this->model = $this->model
                             ->where(function (Builder $query) use ($val) {
                                 return $query
                                     ->where('re_projects.name', 'LIKE', '%' . trim($val) . '%')
                                     ->orWhere('re_projects.location', 'LIKE', '%' . trim($val) . '%');
                             });
                     }
                     else
                     {
                         $this->model = $this->model
                             ->orwhere(function (Builder $query) use ($val) {
                                 return $query
                                     ->where('re_projects.name', 'LIKE', '%' . trim($val) . '%')
                                     ->orWhere('re_projects.location', 'LIKE', '%' . trim($val) . '%');
                             });
                     }
                 }
             } else {
                 $this->model = $this->model
                     ->where(function (Builder $query) use ($filters) {
                         return $query
                             ->where('re_projects.name', 'LIKE', '%' . $filters['keyword'] . '%')
                             ->orWhere('re_projects.location', 'LIKE', '%' . $filters['keyword'] . '%');
                     });
             }*/
        }

        /*  if ($filters['city'] !== null) {
              $this->model = $this->model
                  ->join('cities', 'cities.id', '=', 're_projects.city_id')
                  ->where('cities.slug', $filters['city']);
          }*/

        if ($filters['blocks']) {
            if ($filters['blocks'] < 5) {
                $this->model = $this->model->where('re_projects.number_block', $filters['blocks']);
            } else {
                $this->model = $this->model->where('re_projects.number_block', '>=', $filters['blocks']);
            }
        }

        if ($filters['min_floor'] !== null || $filters['max_floor'] !== null) {
            $this->model = $this->model
                ->where(function ($query) use ($filters) {
                    $minFloor = Arr::get($filters, 'min_floor');
                    $maxFloor = Arr::get($filters, 'max_floor');

                    /**
                     * @var \Illuminate\Database\Query\Builder $query
                     */
                    if ($minFloor !== null) {
                        $query = $query->where('re_projects.number_floor', '>=', $minFloor);
                    }

                    if ($maxFloor !== null) {
                        $query = $query->where('re_projects.number_floor', '<=', $maxFloor);
                    }

                    return $query;
                });
        }
        if ($filters['flat']) {
            if ($filters['flat'] < 50) {
                $this->model = $this->model->where('re_projects.number_flat', $filters['flat']);
            } else {
                $this->model = $this->model->where('re_projects.number_flat', '>=', $filters['flat']);
            }
        }

        if ($filters['floor']) {
            if ($filters['floor'] < 5) {
                $this->model = $this->model->where('re_projects.number_floor', $filters['floor']);
            } else {
                $this->model = $this->model->where('re_projects.number_floor', '>=', $filters['floor']);
            }
        }

        if ($filters['min_flat'] !== null || $filters['max_flat'] !== null) {
            $this->model = $this->model
                ->where(function ($query) use ($filters) {
                    $minFlat = Arr::get($filters, 'min_flat');
                    $maxFlat = Arr::get($filters, 'max_flat');

                    /**
                     * @var \Illuminate\Database\Query\Builder $query
                     */
                    if ($minFlat !== null) {
                        $query = $query->where('re_projects.number_flat', '>=', $minFlat);
                    }

                    if ($maxFlat !== null) {
                        $query = $query->where('re_projects.number_flat', '<=', $maxFlat);
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
                        $query = $query->where('re_projects.price_from', '>=', $minPrice);
                    }

                    if ($maxPrice !== null) {
                        $query = $query->where('re_projects.price_to', '<=', $maxPrice);
                    }

                    return $query;
                });
        }

        if ($filters['category_id'] !== null) {
            $this->model = $this->model->where('re_projects.category_id', $filters['category_id']);
        }

        if ($filters['city_id']) {
            $this->model = $this->model->where('re_projects.city_id', $filters['city_id']);
        }

        /*elseif ($filters['location']) {
            $locationData = explode(',', $filters['location']);
            if (count($locationData) > 1) {
                $this->model = $this->model
                    ->join('cities', 'cities.id', '=', 're_projects.city_id')
                    ->where('cities.name', 'LIKE', '%' . trim($locationData[0]) . '%');
            } else {
                $this->model = $this->model
                    ->join('cities', 'cities.id', '=', 're_projects.city_id')
                    ->where('cities.name', 'LIKE', '%' . $filters['location'] . '%');
            }
        }*/

        $this->model->whereNotIn('re_projects.status', [ProjectStatusEnum::NOT_AVAILABLE]);

        return $this->advancedGet($params);
    }
}
