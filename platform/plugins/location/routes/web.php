<?php

Route::group(['namespace' => 'Botble\Location\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    // GET, not POST: the public theme layout has no CSRF meta tag (only the
    // admin layout does), so a POST here would always fail CSRF verification.
    // Matches this app's existing convention for other stateful public
    // actions, e.g. the `currency/switch/{code?}` route below.
    Route::get('geo/set-browser-location', 'GeoController@setBrowserLocation')
        ->name('geo.set-browser-location');

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'countries', 'as' => 'country.'], function () {
            Route::resource('', 'CountryController')->parameters(['' => 'country']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'CountryController@deletes',
                'permission' => 'country.destroy',
            ]);

            Route::get('list', [
                'as'         => 'list',
                'uses'       => 'CountryController@getList',
                'permission' => 'country.index',
            ]);
        });

        Route::group(['prefix' => 'states', 'as' => 'state.'], function () {
            Route::resource('', 'StateController')->parameters(['' => 'state']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'StateController@deletes',
                'permission' => 'state.destroy',
            ]);

            Route::get('list', [
                'as'         => 'list',
                'uses'       => 'StateController@getList',
                'permission' => 'state.index',
            ]);
        });

        Route::group(['prefix' => 'cities', 'as' => 'city.'], function () {
            Route::resource('', 'CityController')->parameters(['' => 'city']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'CityController@deletes',
                'permission' => 'city.destroy',
            ]);

            Route::get('list', [
                'as'         => 'list',
                'uses'       => 'CityController@getList',
                'permission' => 'city.index',
            ]);
        });

        Route::group(['prefix' => 'cityareas', 'as' => 'cityarea.'], function () {
            Route::resource('', 'CityAreaController')->parameters(['' => 'cityarea']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'CityAreaController@deletes',
                'permission' => 'cityarea.destroy',
            ]);
        });
    });

});
