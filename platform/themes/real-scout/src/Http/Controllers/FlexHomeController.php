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
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Models\City;
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
                $properties = app(PropertyInterface::class)->getPropertiesByConditions(
                    [
                        're_properties.is_featured' => true,
                        're_properties.type' => PropertyTypeEnum::RENT,
                        ['re_properties.status', 'NOT_IN', [PropertyStatusEnum::NOT_AVAILABLE]],
                        're_properties.moderation_status' => ModerationStatusEnum::APPROVED,
                    ],
                    (int) theme_option('number_of_properties_for_sale', 8),
                    ['currency']
                );
                break;
            case 'sale':
                $properties = app(PropertyInterface::class)->getPropertiesByConditions(
                    [
                        're_properties.is_featured' => true,
                        're_properties.type' => PropertyTypeEnum::SALE,
                        ['re_properties.status', 'NOT_IN', [PropertyStatusEnum::NOT_AVAILABLE]],
                        're_properties.moderation_status' => ModerationStatusEnum::APPROVED,
                    ],
                    (int) theme_option('number_of_properties_for_sale', 8),
                    ['currency']
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
                $properties = app(PropertyInterface::class)->getPropertiesByMap($filters, $params);
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
    public function getAgentList(
        AccountInterface $accountRepository,
        PropertyInterface $propertyRepository
    ) {
        $agents = $accountRepository->agents();
        return Theme::scope('real-estate.agent_list', compact('agents'))->render();
    }
    public function agent_search(
        AccountInterface $accountRepository,
        PropertyInterface $propertyRepository
    ) {
        $agents = $accountRepository->agents();
        return Theme::scope('real-estate.agent-search', compact('agents'))->render();
    }
    function agent_search_post(Request $request, AccountInterface $accountRepository)
    {
        $list = Account::where('confirmed_at', '!=', null);
        $first_name = $request['first_name'];
        $last_name = $request['last_name'];
        $location = $request['location'];
        if ($first_name != "")
            $list = $list->where('first_name', 'LIKE', '%' . $first_name . '%');
        if ($last_name != "")
            $list = $list->where('last_name', 'LIKE', '%' . $last_name . '%');
        $agents = $list->get();
        return Theme::scope('real-estate.agent-search-detail', compact('agents'))->render();

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

        $project = app(ProjectInterface::class)->getProjectsMaps($filters, $params);
        return $response
            ->setData(ProjectResource::collection($project))
            ->toApiResponse();
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

}
