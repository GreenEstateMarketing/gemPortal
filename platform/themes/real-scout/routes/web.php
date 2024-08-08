<?php

use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;

// Custom routes
Route::group(['namespace' => 'Theme\FlexHome\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::get(SlugHelper::getPrefix(Project::class, 'projects') . '?city_id={slug?}', 'FlexHomeController@getProjectsByCity')
            ->name('public.project-by-city');

        Route::get(SlugHelper::getPrefix(Property::class, 'properties') . '?city_id={slug?}', 'FlexHomeController@getPropertiesByCity')
            ->name('public.properties-by-city');

        Route::get('agents/{username}', 'FlexHomeController@getAgent')->name('public.agent');

        Route::get('wishlist', 'FlexHomeController@getWishlist')->name('public.wishlist');

        Route::get('ajax/cities', 'FlexHomeController@ajaxGetCities')->name('public.ajax.cities');
        Route::get('ajax/properties', 'FlexHomeController@ajaxGetProperties')->name('public.ajax.properties');
        Route::get('ajax/posts', 'FlexHomeController@ajaxGetPosts')->name('public.ajax.posts');
        Route::get('agents', 'FlexHomeController@getAgentList')->name('public.agent.list');
        Route::get('agent-search', 'FlexHomeController@agent_search')->name('public.agent.search');
        Route::post('agent-search', 'FlexHomeController@agent_search_post')->name('public.agent.search.post');
        Route::get('agent-detail/{username}', 'FlexHomeController@getAgentDetial')->name('public.agent.detail');
        Route::get('get-search-area','FlexHomeController@getSearchAreaList');
        Route::get('ajax/projects', 'FlexHomeController@ajaxGetProjects')->name('public.ajax.projects');
        Route::get('/ajax/get-city-areas', 'FlexHomeController@getCityAreaListByCity');
        Route::get('ajax/get-parent-categories', 'FlexHomeController@ajaxGetParentCategories')->name('public.ajax.parent-categories');
        Route::get('ajax/get-child-categories', 'FlexHomeController@ajaxGetChildCategories')->name('public.ajax.child-categories');
    });
});

Theme::routes();

Route::group(['namespace' => 'Theme\FlexHome\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {

        Route::get('/', 'FlexHomeController@getIndex')->name('public.index');

        Route::get('sitemap.xml', [
            'as'   => 'public.sitemap',
            'uses' => 'FlexHomeController@getSiteMap',
        ]);

        Route::get('{slug?}' . config('core.base.general.public_single_ending_url'), [
            'as'   => 'public.single',
            'uses' => 'FlexHomeController@getView',
        ]);

    });

});
