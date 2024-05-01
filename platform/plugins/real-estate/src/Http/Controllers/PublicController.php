<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Http\Requests\SendConsultRequest;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Models\City;
use Botble\RealEstate\Models\Consult;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Repositories\Interfaces\CategoryInterface;
use Botble\RealEstate\Repositories\Interfaces\ConsultInterface;
use Botble\RealEstate\Repositories\Interfaces\CurrencyInterface;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\SeoHelper\SeoOpenGraph;
use Botble\Slug\Repositories\Interfaces\SlugInterface;
use EmailHandler;
use Exception;
use File;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Mimey\MimeTypes;
use RssFeed;
use RvMedia;
use SeoHelper;
use SlugHelper;
use Spatie\Feed\Feed;
use Spatie\Feed\FeedItem;
use Theme;
use Throwable;

class PublicController extends Controller
{

    /**
     * @param SendConsultRequest $request
     * @param BaseHttpResponse $response
     * @param ConsultInterface $consultRepository
     * @param PropertyInterface $propertyRepository
     * @param ProjectInterface $projectRepository
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function postSendConsult(
        SendConsultRequest $request,
        BaseHttpResponse $response,
        ConsultInterface $consultRepository,
        PropertyInterface $propertyRepository,
        ProjectInterface $projectRepository
    ) {
        try {
            /**
             * @var Consult $consult
             */
            $consult = $consultRepository->getModel();

            $sendTo = null;
            $link = null;
            $subject = null;

            if ($request->input('type') == 'project') {
                $request->merge(['project_id' => $request->input('data_id')]);
                $project = $projectRepository->findById($request->input('data_id'));
                if ($project) {
                    $link = $project->url;
                    $subject = $project->name;
                }
            } else {
                $request->merge(['property_id' => $request->input('data_id')]);
                $property = $propertyRepository->findById($request->input('data_id'), ['author']);
                if ($property && $property->author->email) {
                    $sendTo = $property->author->email;
                    $link = $property->url;
                    $subject = $property->name;
                }

            }
            $consultEntry = new Consult();
            $consultEntry->name = $request->input('name');
            if ($request->input('type') == 'project'){
                $consultEntry->project_id = $request->input('data_id');
            }else{
                $consultEntry->property_id = $request->input('data_id');
            }
            $consultEntry->phone = $request->input('phone');
            $consultEntry->email = $request->input('email');
            $consultEntry->content = $request->input('content');
            $consultEntry->interest = $request->input('interest');
            $consultEntry->save();

//            $consult->fill([
//                $request->input('name')
//            ]);
//            $consultRepository->createOrUpdate($consult);

//            EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
//                ->setVariableValues([
//                    'consult_name'    => $consult->name ?? 'N/A',
//                    'consult_email'   => $consult->email ?? 'N/A',
//                    'consult_phone'   => $consult->phone ?? 'N/A',
//                    'consult_content' => $consult->content ?? 'N/A',
//                    'consult_link'    => $link ?? 'N/A',
//                    'consult_subject' => $subject ?? 'N/A',
//                    'consult_interest' => $consult->interest ?? 'N/A',
//                ])
//                ->sendUsingTemplate('notice', $sendTo);

            return $response->setMessage(trans('plugins/real-estate::consult.email.success'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/real-estate::consult.email.failed'));
        }
    }

    /**
     * @param string $key
     * @param SlugInterface $slugRepository
     * @param ProjectInterface $projectRepository
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getProject(string $key, SlugInterface $slugRepository, ProjectInterface $projectRepository)
    {
        $slug = $slugRepository->getFirstBy([
            'slugs.key'      => $key,
            'reference_type' => Project::class,
            'prefix'         => SlugHelper::getPrefix(Project::class),
        ]);

        if (!$slug) {
            abort(404);
        }

        $project = $projectRepository->getFirstBy(
            ['id' => $slug->reference_id],
            ['*'],
            ['slugable', 'features', 'currency', 'category', 'facilities']
        );

        if (!$project) {
            abort(404);
        }

        if ($project->slugable->key !== $key) {
            return redirect()->to($project->url);
        }

        SeoHelper::setTitle($project->name)->setDescription(Str::words($project->description, 120));

        $meta = new SeoOpenGraph;
        if ($project->image) {
            $meta->setImage(RvMedia::getImageUrl($project->image));
        }
        $meta->setDescription($project->description);
        $meta->setUrl($project->url);
        $meta->setTitle($project->name);
        $meta->setType('article');

        SeoHelper::setSeoOpenGraph($meta);

        Theme::breadcrumb()
            ->add(__('Home'), url('/'))
            ->add($project->name, $project->url);

        $relatedProjects = $projectRepository->getRelatedProjects($project->id,
            theme_option('number_of_related_projects', 8));

        Theme::asset()->usePath()->add('validation-jquery-css',
            'libraries/jquery-validation/validationEngine.jquery.css');
        Theme::asset()->add('images-grid-css',
            'https://cdn.jsdelivr.net/gh/taras-d/images-grid/src/images-grid.min.css');
        Theme::asset()->container('header')->add('images-grid-js',
            'https://cdn.jsdelivr.net/gh/taras-d/images-grid/src/images-grid.min.js', ['jquery']);
        Theme::asset()->container('header')->usePath()->add('jquery-validationEngine-vi-js',
            'libraries/jquery-validation/jquery.validationEngine-vi.js', ['jquery']);
        Theme::asset()->container('header')->usePath()->add('jquery-validationEngine-js',
            'libraries/jquery-validation/jquery.validationEngine.js', ['jquery']);

        if (function_exists('admin_bar')) {
            admin_bar()->registerLink(__('Edit this project'), route('project.edit', $project->id));
        }

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, PROJECT_MODULE_SCREEN_NAME, $project);

        $images = [];
        foreach ($project->images as $image) {
            $images[] = RvMedia::getImageUrl($image, null, false, RvMedia::getDefaultImage());
        }

        return Theme::scope('real-estate.project', compact('project', 'images', 'relatedProjects'))->render();
    }

    /**
     * @param string $key
     * @param SlugInterface $slugRepository
     * @param PropertyInterface $propertyRepository
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getProperty(string $key, SlugInterface $slugRepository, PropertyInterface $propertyRepository)
    {
        $slug = $slugRepository->getFirstBy([
            'slugs.key'      => $key,
            'reference_type' => Property::class,
            'prefix'         => SlugHelper::getPrefix(Property::class),
        ]);

        if (!$slug) {
            abort(404);
        }

        $property = $propertyRepository->getProperty(
            $slug->reference_id,
            ['slugable', 'features', 'project', 'currency', 'author', 'category', 'facilities']
        );

        if (!$property) {
            abort(404);
        }

        if ($property->slugable->key !== $key) {
            return redirect()->to($property->url);
        }

        SeoHelper::setTitle($property->name)->setDescription(Str::words($property->description, 120));

        $meta = new SeoOpenGraph;
        if ($property->image) {
            $meta->setImage(RvMedia::getImageUrl($property->image));
        }
        $meta->setDescription($property->description);
        $meta->setUrl($property->url);
        $meta->setTitle($property->name);
        $meta->setType('article');

        SeoHelper::setSeoOpenGraph($meta);

        Theme::breadcrumb()
            ->add(__('Home'), url('/'))
            ->add($property->name, $property->url);

        Theme::asset()->usePath()->add('validation-jquery-css',
            'libraries/jquery-validation/validationEngine.jquery.css');
        Theme::asset()->add('images-grid-css',
            'https://cdn.jsdelivr.net/gh/taras-d/images-grid/src/images-grid.min.css');
        Theme::asset()->container('header')->add('images-grid-js',
            'https://cdn.jsdelivr.net/gh/taras-d/images-grid/src/images-grid.min.js', ['jquery']);
        Theme::asset()->container('header')->usePath()->add('jquery-validationEngine-vi-js',
            'libraries/jquery-validation/jquery.validationEngine-vi.js', ['jquery']);
        Theme::asset()->container('header')->usePath()->add('jquery-validationEngine-js',
            'libraries/jquery-validation/jquery.validationEngine.js', ['jquery']);

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, PROPERTY_MODULE_SCREEN_NAME, $property);

        if (function_exists('admin_bar')) {
            admin_bar()->registerLink(__('Edit this property'), route('property.edit', $property->id));
        }

        $images = [];
        foreach ($property->images as $image) {
            $images[] = RvMedia::getImageUrl($image, null, false, RvMedia::getDefaultImage());
        }

        return Theme::scope('real-estate.property', compact('property', 'images'))->render();
    }

    /**
     * @param Request $request
     * @param ProjectInterface $projectRepository
     * @param CategoryInterface $categoryRepository
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse|\Response
     */
    public function getProjects(
        Request $request,
        ProjectInterface $projectRepository,
        CategoryInterface $categoryRepository,
        BaseHttpResponse $response
    ) {

        SeoHelper::setTitle(__('Projects'));
        $chosenArr=$request->get('k');
        $chosenFullArr=array();

        if(!isset($chosenArr))
           $chosenArr=array();
        else
        {
            $chosenFullArr=$chosenArr;
            foreach ($chosenArr as $key => $val) {
                $chosenArr[$key] = substr($val, 0, 15);
            }

        }

        $category_id=$request->get('category_id');
        $parent_id=0;
        if($category_id!="")
            $parent_id=getParentCategory($category_id);

        /*//$chosenArr=array(0=>'C');
       // print_r($chosenFullArr);exit;
        $filters = [
            'keyword'     => $request->input('k'),
            'blocks'      => $request->input('blocks'),
            'min_floor'   => $request->input('min_floor'),
            'max_floor'   => $request->input('max_floor'),
            'min_flat'    => $request->input('min_flat'),
            'max_flat'    => $request->input('max_flat'),
            'category_id' => $request->input('category_id'),
            'city_id'     => $request->input('city_id'),
            'location'    => $request->input('location'),
            'sort_by'     => $request->input('sort_by'),
        ];

        $params = [
            'paginate' => [
                'per_page'      => $request->input('per_page') ? (int)$request->input('per_page') : (int)theme_option('number_of_projects_per_page',
                    12),
                'current_paged' => $request->input('page', 1),
            ],
            'order_by' => ['re_projects.created_at' => 'DESC'],
        ];


        $projects = $projectRepository->getProjects($filters, $params);

        Theme::breadcrumb()
            ->add(__('Home'), url('/'))
            ->add(__('Projects'), route('public.projects'));

        if ($request->ajax()) {
            return $response->setData(Theme::partial('search-suggestion', ['items' => $projects]));
        }*/

        $categories = $categoryRepository->pluck('re_categories.name', 're_categories.id');
        $cities = City::select('id','name')->where('status','published')->get();

        return Theme::scope('real-estate.projects', compact( 'categories','chosenArr','parent_id','chosenFullArr','cities'))->render();
    }

    /**
     * @param Request $request
     * @param PropertyInterface $propertyRepository
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse|\Response
     */
    public function getProperties(
        Request $request,
        PropertyInterface $propertyRepository,
        CategoryInterface $categoryRepository,
        BaseHttpResponse $response
    ) {
        SeoHelper::setTitle(__('Properties'));
/*
        $filters = [
            'keyword'     => $request->input('k'),
            'type'        => $request->input('type'),
            'bedroom'     => $request->input('bedroom'),
            'bathroom'    => $request->input('bathroom'),
            'floor'       => $request->input('floor'),
            'min_price'   => $request->input('min_price'),
            'max_price'   => $request->input('max_price'),
            'min_square'  => $request->input('min_square'),
            'max_square'  => $request->input('max_square'),
            'project'     => $request->input('project'),
            'category_id' => $request->input('category_id'),
            'city'        => $request->input('city'),
            'city_id'     => $request->input('city_id'),
            'location'    => $request->input('location'),
            'sort_by'     => $request->input('sort_by'),
            'latitude'    => $request->input('latitude'),
            'longitude'   => $request->input('longitude'),
            'latLngs-1'   => $request->input('latLngs-1'),
            'latLngs-2'   => $request->input('latLngs-2'),
            'latLngs-3'   => $request->input('latLngs-3'),
            'latLngs-4'   => $request->input('latLngs-4')
        ];*/
        $chosenArr=$request->get('k');
        $chosenFullArr=array();

        $cities = City::select('id','name')->where('status','published')->get();
        // echo '<pre>';
        // print_r($cities);
        // exit();
        if(!isset($chosenArr))
            $chosenArr=array();
        else
        {
            $chosenFullArr=$chosenArr;
            foreach ($chosenArr as $key => $val) {
                $chosenArr[$key] = substr($val, 0, 15);
            }

        }

        $category_id=$request->get('category_id');
        $parent_id=0;
        if($category_id!="")
            $parent_id=getParentCategory($category_id);
     /*   $params = [
            'paginate' => [
                'per_page'      => $request->input('per_page') ? (int)$request->input('per_page') : (int)theme_option('number_of_properties_per_page',
                    12),
                'current_paged' => $request->input('page', 1),
            ],
            'order_by' => ['re_properties.created_at' => 'DESC'],
        ];

        $properties = $propertyRepository->getProperties($filters, $params);
        Theme::breadcrumb()
            ->add(__('Home'), url('/'))
            ->add(__('Properties'), route('public.properties'));

        if ($request->ajax()) {
            return $response->setData(Theme::partial('search-suggestion', ['items' => $properties]));
        }*/

        $categories = $categoryRepository->pluck('re_categories.name', 're_categories.id');

        return Theme::scope('real-estate.properties', compact( 'categories','chosenArr','category_id','parent_id','chosenFullArr','cities'))->render();
    }

    /**
     * @param Request $request
     * @param PropertyInterface $propertyRepository
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse|\Response
     */
    public function getPropertyCategory(
        $key,
        Request $request,
        SlugInterface $slugRepository,
        PropertyInterface $propertyRepository,
        CategoryInterface $categoryRepository
    ) {
        $slug = $slugRepository->getFirstBy([
            'slugs.key'      => $key,
            'reference_type' => Category::class,
            'prefix'         => SlugHelper::getPrefix(Category::class),
        ]);

        if (!$slug) {
            abort(404);
        }

        $category = $categoryRepository->getFirstBy(
            ['id' => $slug->reference_id],
            ['*'],
            ['slugable']
        );

        if (!$category) {
            abort(404);
        }

        SeoHelper::setTitle($category->name)->setDescription(Str::words($category->description, 120));

        $meta = new SeoOpenGraph;
        $meta->setDescription($category->description);
        $meta->setUrl($category->url);
        $meta->setTitle($category->name);
        $meta->setType('article');

        SeoHelper::setSeoOpenGraph($meta);

        Theme::breadcrumb()
            ->add(__('Home'), url('/'))
            ->add($category->name, $category->url);

        $filters = [
            'category_id' => $category->id,
        ];

        $params = [
            'paginate' => [
                'per_page'      => theme_option('number_of_properties_per_page', 12),
                'current_paged' => $request->input('page', 1),
            ],
            'order_by' => ['re_properties.created_at' => 'DESC'],
        ];

        $properties = $propertyRepository->getProperties($filters, $params);

        return Theme::scope('real-estate.property-category', compact('category', 'properties'))->render();
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param null $title
     * @param CurrencyInterface $currencyRepository
     * @return BaseHttpResponse
     */
    public function changeCurrency(
        Request $request,
        BaseHttpResponse $response,
        CurrencyInterface $currencyRepository,
        $title = null
    ) {
        if (empty($title)) {
            $title = $request->input('currency');
        }

        if (!$title) {
            return $response;
        }

        $currency = $currencyRepository->getFirstBy(['title' => $title]);

        if ($currency) {
            cms_currency()->setApplicationCurrency($currency);
        }

        return $response;
    }

    /**
     * @param PropertyInterface $propertyRepository
     * @return Feed
     */
    public function getPropertyFeeds(PropertyInterface $propertyRepository)
    {
        if (!is_plugin_active('rss-feed')) {
            abort(404);
        }

        $data = $propertyRepository->getProperties([], [
            'take' => 20,
            'with' => ['slugable', 'category', 'author']
        ]);

        $feedItems = collect([]);

        foreach ($data as $item) {
            $imageURL = RvMedia::getImageUrl($item->image, null, false, RvMedia::getDefaultImage());

            $feedItems[] = FeedItem::create()
                ->id($item->id)
                ->title(clean($item->name))
                ->summary(clean($item->description))
                ->updated($item->updated_at)
                ->enclosure($imageURL)
                ->enclosureType((new MimeTypes)->getMimeType(File::extension($imageURL)))
                ->enclosureLength(RssFeed::remoteFilesize($imageURL))
                ->category($item->category->name)
                ->link((string)$item->url)
                ->author($item->author_id ? $item->author->getFullName() : '');
        }

        return RssFeed::renderFeedItems($feedItems, 'Properties feed', 'Latest properties from ' . theme_option('site_title'));
    }
}
