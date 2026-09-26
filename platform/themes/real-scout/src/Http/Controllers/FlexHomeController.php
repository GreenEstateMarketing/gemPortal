<?php

namespace Theme\FlexHome\Http\Controllers;

use App;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\Location\Repositories\Eloquent\CityAreaRepository;
use Botble\Location\Repositories\Interfaces\CityInterface;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\Location\Models\Country;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Models\City;
use Botble\RealEstate\Models\SpokenLanguage;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Botble\RealEstate\Repositories\Interfaces\CategoryInterface;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\Location\Repositories\Interfaces\CityAreaInterface;
use Botble\Theme\Http\Controllers\PublicController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use SeoHelper;
use Theme;
use Theme\FlexHome\Http\Resources\AgentSearchResource;
use Theme\FlexHome\Http\Resources\PostResource;
use Theme\FlexHome\Http\Resources\PropertyResource;
use App\Models\area;
use DB;
use Theme\FlexHome\Http\Resources\ProjectResource;

class FlexHomeController extends PublicController
{

    /**
     * @param string $slug
     * @param Request $request
     * @param ProjectInterface $projectRepository
     * @param CategoryInterface $categoryRepository
     * @return \Response
     */
    public function getProjectsByCity(
        string $slug,
        Request $request,
        ProjectInterface $projectRepository,
        CategoryInterface $categoryRepository
    ) {
        SeoHelper::setTitle(__('Projects'));

        $filters = [
            'city' => $slug,
        ];

        $params = [
            'paginate' => [
                'per_page' => (int) theme_option('number_of_projects_per_page', 12),
                'current_paged' => (int) $request->input('page', 1),
            ],
            'order_by' => ['re_projects.created_at' => 'DESC'],
        ];

        $projects = $projectRepository->getProjects($filters, $params);

        $categories = $categoryRepository->pluck('re_categories.name', 're_categories.id');

        return Theme::scope('real-estate.projects', compact('projects', 'categories'))
            ->render();
    }

    /**
     * @param string $slug
     * @param Request $request
     * @param PropertyInterface $propertyRepository
     * @param CategoryInterface $categoryRepository
     * @return \Response
     */
    public function getPropertiesByCity(
        string $slug,
        Request $request,
        PropertyInterface $propertyRepository,
        CategoryInterface $categoryRepository
    ) {
        SeoHelper::setTitle(__('Properties'));

        $filters = [
            'city' => $slug,
        ];

        $params = [
            'paginate' => [
                'per_page' => (int) theme_option('number_of_properties_per_page', 12),
                'current_paged' => (int) $request->input('page', 1),
            ],
            'order_by' => ['re_properties.created_at' => 'DESC'],
        ];

        $chosenArr = [];
        $chosenFullArr = array();

        $cities = City::select('id', 'name')->where('status', 'published')->get();
        $parent_id = 0;

        if (!isset($chosenArr))
            $chosenArr = array();
        else {
            $chosenFullArr = $chosenArr;
            foreach ($chosenArr as $key => $val) {
                $chosenArr[$key] = substr($val, 0, 15);
            }

        }
        $properties = $propertyRepository->getProperties($filters, $params);

        $categories = $categoryRepository->pluck('re_categories.name', 're_categories.id');

        return Theme::scope('real-estate.properties', compact('properties', 'categories', 'chosenArr', 'parent_id', 'chosenFullArr', 'cities'))
            ->render();
    }

    /**
     * Featured/for-sale/for-rent carousels, scoped to the visitor's resolved
     * location: city first, falling back to country-wide, falling back to
     * the unscoped global list when neither has any matching properties.
     *
     * @param string $type
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    private function getLocationScopedFeaturedProperties(string $type, int $limit)
    {
        $baseConditions = [
            're_properties.is_featured' => true,
            're_properties.type' => $type,
            ['re_properties.status', 'NOT_IN', [PropertyStatusEnum::NOT_AVAILABLE]],
            're_properties.moderation_status' => ModerationStatusEnum::APPROVED,
        ];

        // 'default' means nothing was actually resolved (no IP match, no
        // browser grant) - session('visitor_location.country_id') still
        // carries the site's bare fallback (166) in that case, which isn't
        // a real signal and would otherwise scope this to "the whole
        // country" for every visitor everywhere nothing was detected.
        $location = session('visitor_location', []);
        $isResolved = ($location['source'] ?? 'default') !== 'default';
        $cityId = $isResolved ? ($location['city_id'] ?? null) : null;
        $countryId = $isResolved ? ($location['country_id'] ?? null) : null;

        if ($cityId) {
            $properties = app(PropertyInterface::class)->getPropertiesByConditions(
                array_merge($baseConditions, ['re_properties.city_id' => $cityId]),
                $limit,
                ['currency']
            );

            if ($properties->isNotEmpty()) {
                return $properties;
            }
        }

        if ($countryId) {
            $properties = app(PropertyInterface::class)->getPropertiesByConditions(
                array_merge($baseConditions, ['re_properties.country_id' => $countryId]),
                $limit,
                ['currency']
            );

            if ($properties->isNotEmpty()) {
                return $properties;
            }
        }

        return app(PropertyInterface::class)->getPropertiesByConditions($baseConditions, $limit, ['currency']);
    }

    /**
     * The properties search/map page's default (no explicit search yet)
     * results, scoped to the visitor's resolved location: city first,
     * falling back to country-wide, falling back to the full unscoped
     * result set when neither scope has anything. If the visitor has
     * already searched by city/location themselves (via the search bar),
     * that explicit choice always takes precedence and this default never
     * runs - it only fills in what an untouched search bar leaves blank.
     *
     * @param array $filters
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function getMapSearchPropertiesWithLocationDefault(array $filters, array $params)
    {
        $userSuppliedLocation = !empty($filters['city_id']) || !empty($filters['location']);

        if ($userSuppliedLocation) {
            return app(PropertyInterface::class)->getPropertiesByMap($filters, $params);
        }

        // 'default' means nothing was actually resolved (no IP match, no
        // browser grant) - see the identical guard/comment in
        // getLocationScopedFeaturedProperties() above.
        $location = session('visitor_location', []);
        $isResolved = ($location['source'] ?? 'default') !== 'default';
        $cityId = $isResolved ? ($location['city_id'] ?? null) : null;
        $countryId = $isResolved ? ($location['country_id'] ?? null) : null;

        if ($cityId) {
            $properties = app(PropertyInterface::class)->getPropertiesByMap(
                array_merge($filters, ['city_id' => $cityId]),
                $params
            );

            if ($properties->total() > 0) {
                return $properties;
            }
        }

        if ($countryId) {
            $properties = app(PropertyInterface::class)->getPropertiesByMap(
                array_merge($filters, ['country_id' => $countryId]),
                $params
            );

            if ($properties->total() > 0) {
                return $properties;
            }
        }

        return app(PropertyInterface::class)->getPropertiesByMap($filters, $params);
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetProperties(Request $request, BaseHttpResponse $response)
    {
        //This is the flex controller
        $properties = [];
        $links = [];
        switch ($request->input('type')) {
            case 'related':
                $properties = app(PropertyInterface::class)
                    ->getRelatedProperties(
                        $request,
                        (int) theme_option('number_of_related_properties', 8)
                    );
                break;
            case 'rent':
                $properties = $this->getLocationScopedFeaturedProperties(
                    PropertyTypeEnum::RENT,
                    (int) theme_option('number_of_properties_for_sale', 8)
                );
                break;
            case 'sale':
                $properties = $this->getLocationScopedFeaturedProperties(
                    PropertyTypeEnum::SALE,
                    (int) theme_option('number_of_properties_for_sale', 8)
                );
                break;
            case 'project-properties-for-sell':
                $properties = app(PropertyInterface::class)->getPropertiesByConditions(
                    [
                        're_properties.project_id' => $request->input('project_id'),
                        're_properties.type' => PropertyTypeEnum::SALE,
                        ['re_properties.status', 'NOT_IN', [PropertyStatusEnum::NOT_AVAILABLE]],
                        're_properties.moderation_status' => ModerationStatusEnum::APPROVED,
                    ],
                    (int) theme_option('number_of_properties_for_sale', 8),
                    ['currency']
                );
                break;
            case 'project-properties-for-rent':
                $properties = app(PropertyInterface::class)->getPropertiesByConditions(
                    [
                        're_properties.project_id' => $request->input('project_id'),
                        're_properties.type' => PropertyTypeEnum::RENT,
                        ['re_properties.status', 'NOT_IN', [PropertyStatusEnum::NOT_AVAILABLE]],
                        're_properties.moderation_status' => ModerationStatusEnum::APPROVED,
                    ],
                    (int) theme_option('number_of_properties_for_sale', 8),
                    ['currency']
                );
                break;
            case 'mapsearch':
                unset($request['type']);

                $request['type'] = $request['property_type'];
                unset($request['property_type']);
                $filters = $request->input();
                $params = [
                    'paginate' => [
                        'per_page' => $request->input('per_page') ? (int) $request->input('per_page') : (int) theme_option(
                            'number_of_properties_per_page',
                            10
                        ),
                        'current_paged' => $request->input('page', 1),
                    ],
                    'order_by' => ['re_properties.created_at' => 'DESC'],
                ];
                $properties = $this->getMapSearchPropertiesWithLocationDefault($filters, $params);
                break;
        }

        return $response
            ->setData(PropertyResource::collection($properties))
            ->toApiResponse();
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function ajaxGetPosts(Request $request, BaseHttpResponse $response)
    {
        if (!$request->ajax() || !$request->wantsJson()) {
            abort(404);
        }

        $posts = app(PostInterface::class)->getFeatured(3);

        return $response
            ->setData(PostResource::collection($posts))
            ->toApiResponse();
    }

    /**
     * @param string $slug
     * @param Request $request
     * @param PropertyInterface $propertyRepository
     * @param CategoryInterface $categoryRepository
     * @return \Response
     */
    public function getAgent(
        string $username,
        Request $request,
        AccountInterface $accountRepository,
        PropertyInterface $propertyRepository
    ) {
        $account = $accountRepository->getFirstBy(['username' => $username]);

        if (!$account) {
            abort(404);
        }

        SeoHelper::setTitle($account->getFullName());

        $properties = $propertyRepository->advancedGet([
            'condition' => [
                'author_id' => $account->id,
                'author_type' => Account::class,
            ],
            'paginate' => [
                'per_page' => 12,
                'current_paged' => (int) $request->input('page'),
            ],
        ]);

        return Theme::scope('real-estate.agent', compact('properties', 'account'))
            ->render();
    }
    public function getAgentDetial(
        string $username,
        Request $request,
        AccountInterface $accountRepository,
        PropertyInterface $propertyRepository
    ) {
        $account = $accountRepository->getFirstBy(['username' => $username]);

        if (!$account) {
            abort(404);
        }

        SeoHelper::setTitle($account->getFullName());

        $properties = $propertyRepository->advancedGet([
            'condition' => [
                'author_id' => $account->id,
                'author_type' => Account::class,
                'moderation_status' => ModerationStatusEnum::APPROVED
            ],
            'paginate' => [
                'per_page' => 12,
                'current_paged' => (int) $request->input('page'),
            ],
        ]);

        return Theme::scope('real-estate.agent-search-detail_page', compact('properties', 'account'))
            ->render();
    }
    /**
     * @param Request $request
     * @param CityInterface $cityRepository
     * @param BaseHttpResponse $response
     * @return mixed
     */
    public function ajaxGetCities(Request $request, CityInterface $cityRepository, BaseHttpResponse $response)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $keyword = $request->input('k');

        $cities = $cityRepository->getModel()
            ->join('states', 'states.id', '=', 'cities.state_id')
            ->join('countries', 'countries.id', '=', 'cities.country_id')
            ->where('cities.status', BaseStatusEnum::PUBLISHED)
            ->where('states.status', BaseStatusEnum::PUBLISHED)
            ->where('countries.status', BaseStatusEnum::PUBLISHED)
            ->where(function (Builder $query) use ($keyword) {
                return $query
                    ->where('cities.name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('states.name', 'LIKE', '%' . $keyword . '%');
            })
            ->get(['cities.*']);

        return $response->setData(Theme::partial('city-suggestion', ['items' => $cities]));
    }

    /**
     * @param Request $request
     * @return \Botble\Theme\Facades\Response|Response|\Response
     */
    public function getWishlist(Request $request, PropertyInterface $propertyRepository)
    {
        SeoHelper::setTitle(__('Wishlist'))
            ->setDescription(__('Wishlist'));

        $cookieName = App::getLocale() . '_wishlist';
        $jsonWishlist = null;
        if (isset($_COOKIE[$cookieName])) {
            $jsonWishlist = $_COOKIE[$cookieName];
        }

        $properties = collect([]);

        if (!empty($jsonWishlist)) {
            $arrValue = collect(json_decode($jsonWishlist, true))->flatten()->all();
            $properties = $propertyRepository->advancedGet([
                'condition' => [
                    ['re_properties.id', 'IN', $arrValue],
                ],
                'order_by' => [
                    're_properties.id' => 'DESC',
                ],
                'paginate' => [
                    'per_page' => (int) theme_option('number_of_properties_per_page', 12),
                    'current_paged' => (int) $request->input('page', 1),
                ],
            ]);
        }

        Theme::breadcrumb()
            ->add(__('Home'), url('/'))
            ->add(__('Wishlist'));

        return Theme::scope('real-estate.wishlist', compact('properties'))->render();
    }
    public function Welcome()
    {
        return view('welcom');
    }
    /**
     * @param AccountInterface $accountRepository
     * @return \Response
     */
    public function getAgents()
    {
        SeoHelper::setTitle(__('Agents'));

        $countries = Country::where('status', BaseStatusEnum::PUBLISHED)->orderBy('name')->get(['id', 'name']);
        $cities = City::select('id', 'name')
            ->where('status', 'published')
            ->where('country_id', session('visitor_location.country_id', 166))
            ->get();
        $languages = SpokenLanguage::where('status', BaseStatusEnum::PUBLISHED)->orderBy('order')->get(['id', 'name']);
        $categories = Category::where('status', BaseStatusEnum::PUBLISHED)->orderBy('name')->get(['id', 'name']);
        $defaultCountryId = session('visitor_location.country_id', 166);
        $defaultCityId = session('visitor_location.city_id');

        return Theme::scope('real-estate.agents', compact(
            'countries',
            'cities',
            'languages',
            'categories',
            'defaultCountryId',
            'defaultCityId'
        ))->render();
    }

    /**
     * @param Request $request
     * @param AccountInterface $accountRepository
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetAgents(Request $request, AccountInterface $accountRepository, BaseHttpResponse $response)
    {
        $agents = $accountRepository->searchAgents([
            'keyword' => $request->input('keyword'),
            'country_id' => $request->input('country_id'),
            'city_id' => $request->input('city_id'),
            'language_ids' => (array) $request->input('language_ids', []),
            'category_ids' => (array) $request->input('category_ids', []),
            'min_experience' => $request->input('min_experience'),
            'max_experience' => $request->input('max_experience'),
            'lat' => $request->input('lat'),
            'lng' => $request->input('lng'),
            'sort_by' => $request->input('sort_by'),
            'per_page' => (int) theme_option('number_of_agents_per_page', 10),
            'current_paged' => (int) $request->input('page', 1),
        ]);

        return $response->setData(AgentSearchResource::collection($agents));
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetCitiesByCountry(Request $request, BaseHttpResponse $response)
    {
        $cities = City::select('id', 'name')
            ->where('status', 'published')
            ->where('country_id', (int) $request->input('country_id'))
            ->orderBy('name')
            ->get();

        return $response->setData($cities);
    }
    public function excerpt($title, $cutOffLength)
    {

        $charAtPosition = "";
        $titleLength = strlen($title);

        do {
            $cutOffLength++;
            $charAtPosition = substr($title, $cutOffLength, 1);
        } while ($cutOffLength < $titleLength && $charAtPosition != " ");

        return substr($title, 0, $cutOffLength) . '...';

    }

    public function getCityAreaListByCity(Request $request, CityAreaInterface $cityAreaRepository, BaseHttpResponse $response)
    {

        if (!$request->ajax()) {
            abort(404);
        }

        $city_id = $request->input('city_id');

        if ($city_id == 0) {
            $cityAreas = $cityAreaRepository->getModel()->get(['city_area.*']);
        } else {
            $cityAreas = $cityAreaRepository->getModel()
                ->where(function (Builder $query) use ($city_id) {
                    return $query
                        ->where('city_id', '=', $city_id);
                })
                ->get(['city_area.*']);
        }

        foreach ($cityAreas as $key => &$value) {
            $value->city_area_name = strlen($value->city_area_name) > 25 ? substr($value->city_area_name, 0, 25) . " ..." : $value->city_area_name;
        }

        return $response->setData($cityAreas);
    }
    public function getStateListByCountry(Request $request)
    {
        $states = \Botble\Location\Models\State::where('country_id', $request->country_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'error' => false,
            'data' => $states,
        ]);
    }
    public function getCityListByState(Request $request)
    {
        $cities = \Botble\Location\Models\City::where('state_id', $request->state_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'error' => false,
            'data' => $cities,
        ]);
    }
    public function getSearchAreaList(Request $request, PropertyInterface $propertyRepository, ProjectInterface $projectRepository, BaseHttpResponse $response)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $city_id = $request->input('city_id');
        $location = $request->input('location');
        $keyword = $request->input('query');
        $category_id = $request->input('category_id');
        $type = $request->input('type'); //project,sale,rent
        /*$list=area::join('cities', 'cities.id', '=', 'city_id')->where('cities.status', BaseStatusEnum::PUBLISHED);
        if ($city_id) {
            $list= $list->where('city_id',$city_id);
        } elseif ($location) {
            $locationData = explode(',',$location);
            if (count($locationData) > 1) {
                $list= $list->where('cities.name', 'LIKE', '%' . trim($locationData[0]) . '%');
            } else {
                $list= $list->where('cities.name', 'LIKE', '%' .$location . '%');
            }
        }


        $list= $list->where(function (Builder $query) use ($keyword) {
            return $query
                ->where('areas.name', 'LIKE', '%' . $keyword . '%');

        });
        $list_data= $list->get(['areas.name as area_name','cities.name as city_name'])->toArray();*/
        if ($type == "project") {
            $list = $projectRepository->getModel()
                ->join('cities', 'cities.id', '=', 'city_id')
                ->where('cities.status', BaseStatusEnum::PUBLISHED);

            if ($city_id) {
                $list = $list->where('city_id', $city_id);
            } elseif ($location) {
                $locationData = explode(',', $location);
                if (count($locationData) > 1) {
                    $list = $list->where('cities.name', 'LIKE', '%' . trim($locationData[0]) . '%');
                } else {
                    $list = $list->where('cities.name', 'LIKE', '%' . $location . '%');
                }
            }
            $list = $list->where(function (Builder $query) use ($keyword) {
                return $query
                    ->where('location', 'LIKE', '%' . $keyword . '%');

            });
            $list = $list->where(function (Builder $query) use ($keyword) {
                return $query
                    ->where('location', 'LIKE', '%' . $keyword . '%');

            });
            if ($category_id !== null) {
                $list = $list->where('re_projects.category_id', $category_id);
            }


            $list_data = $list->get(['location', 'cities.name'])->toArray();
            /*

                    $query = DB::getQueryLog();
                    dd($query);*/
            //  $data = array_column($list_data, 'area_name');
            echo json_encode($list_data);
        } else {
            $list = $propertyRepository->getModel()
                ->join('cities', 'cities.id', '=', 'city_id')
                ->where('cities.status', BaseStatusEnum::PUBLISHED);
            $list = $list->where('type', $type);
            $list = $list->where('expire_date', '>', date('Y-m-d'))->orwhere('never_expired', 1);

            if ($city_id) {
                $list = $list->where('city_id', $city_id);
            } elseif ($location) {
                $locationData = explode(',', $location);
                if (count($locationData) > 1) {
                    $list = $list->where('cities.name', 'LIKE', '%' . trim($locationData[0]) . '%');
                } else {
                    $list = $list->where('cities.name', 'LIKE', '%' . $location . '%');
                }
            }
            $list = $list->where(function (Builder $query) use ($keyword) {
                return $query
                    ->where('location', 'LIKE', '%' . $keyword . '%');

            });
            if ($category_id !== null) {
                $list = $list->where('re_properties.category_id', $category_id);
            }

            $list_data = $list->get(['location', 'cities.name'])->toArray();
            /*

                    $query = DB::getQueryLog();
                    dd($query);*/
            //$data = array_column($list_data, 'area_name');
            echo json_encode($list_data);
        }
    }
    public function ajaxGetProjects(Request $request, BaseHttpResponse $response)
    {
        $filters = $request->input();
        $params = [
            'paginate' => [
                'per_page' => $request->input('per_page') ? (int) $request->input('per_page') : (int) theme_option(
                    'number_of_projects_per_page',
                    12
                ),
                'current_paged' => $request->input('page', 1),
            ],
            'order_by' => ['re_projects.created_at' => 'DESC'],
        ];

        $project = $this->getMapSearchProjectsWithLocationDefault($filters, $params);
        return $response
            ->setData(ProjectResource::collection($project))
            ->toApiResponse();
    }

    /**
     * The projects search/map page's default (no explicit search yet)
     * results, scoped to the visitor's resolved location: city first,
     * falling back to country-wide, falling back to the full unscoped
     * result set when neither scope has anything. Mirrors
     * getMapSearchPropertiesWithLocationDefault() above - see its docblock
     * for the precedence/fallback rationale, identical here.
     *
     * @param array $filters
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function getMapSearchProjectsWithLocationDefault(array $filters, array $params)
    {
        $userSuppliedLocation = !empty($filters['city_id']) || !empty($filters['location']);

        if ($userSuppliedLocation) {
            return app(ProjectInterface::class)->getProjectsMaps($filters, $params);
        }

        $location = session('visitor_location', []);
        $isResolved = ($location['source'] ?? 'default') !== 'default';
        $cityId = $isResolved ? ($location['city_id'] ?? null) : null;
        $countryId = $isResolved ? ($location['country_id'] ?? null) : null;

        if ($cityId) {
            $projects = app(ProjectInterface::class)->getProjectsMaps(
                array_merge($filters, ['city_id' => $cityId]),
                $params
            );

            if ($projects->total() > 0) {
                return $projects;
            }
        }

        if ($countryId) {
            $projects = app(ProjectInterface::class)->getProjectsMaps(
                array_merge($filters, ['country_id' => $countryId]),
                $params
            );

            if ($projects->total() > 0) {
                return $projects;
            }
        }

        return app(ProjectInterface::class)->getProjectsMaps($filters, $params);
    }

    public function ajaxGetParentCategories(Request $request, BaseHttpResponse $response)
    {
        return Category::where('parent_id', '0')->pluck('name', 'id');
    }

    public function ajaxGetChildCategories(Request $request, BaseHttpResponse $response)
    {
        $id = $request->get('id');
        return Category::where('parent_id', $id)->pluck('name', 'id');
    }

    /**
     * Typeahead for the "Search By Property Type" home page section. Matches
     * both parent categories (e.g. "COMMERCIAL") and subcategories (e.g.
     * "Office") by name, and includes each result's parent name (if any) so
     * the suggestion list can show it the same way the cards below do.
     */
    public function ajaxSearchCategories(Request $request, BaseHttpResponse $response)
    {
        $keyword = trim((string) $request->input('q'));

        $categories = Category::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where('name', 'LIKE', '%' . $keyword . '%');
            })
            ->select('id', 'name', 'parent_id')
            ->orderBy('name')
            ->limit(10)
            ->get();

        $parentNames = Category::query()
            ->whereIn('id', $categories->pluck('parent_id')->filter()->unique())
            ->pluck('name', 'id');

        $results = $categories->map(function ($category) use ($parentNames) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'parent_name' => $parentNames->get($category->parent_id),
            ];
        });

        return $response->setData($results);
    }

}
