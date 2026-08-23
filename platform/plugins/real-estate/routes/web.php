<?php

use Botble\RealEstate\Http\Controllers\PropertyController;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;

Route::group(['namespace' => 'Botble\RealEstate\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::get('package/callback', 'GeneralPropertyController@genericPackageCallback')
        ->name('package.callback');

    Route::group([
        'prefix' => BaseHelper::getAdminPrefix() . '/real-estate',
        'middleware' => 'auth',
    ], function () {

        Route::get('settings', [
            'as' => 'real-estate.settings',
            'uses' => 'RealEstateController@getSettings',
        ]);

        Route::post('settings', [
            'as' => 'real-estate.settings.post',
            'uses' => 'RealEstateController@postSettings',
            'permission' => 'real-estate.settings',
        ]);

        Route::group(['prefix' => 'properties', 'as' => 'property.'], function () {
            Route::resource('', 'PropertyController')
                ->parameters(['' => 'property']);

            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'PropertyController@deletes',
                'permission' => 'property.destroy',
            ]);
        });

        Route::group(['prefix' => 'documents', 'as' => 'document.'], function () {
            Route::resource('', 'DocumentController')
                ->parameters(['' => 'document']);

            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'DocumentController@deletes',
                'permission' => 'document.destroy',
            ]);
        });

        Route::group(['prefix' => 'templates', 'as' => 'template.'], function () {
            Route::resource('', 'TemplatesController')
                ->parameters(['' => 'template']);

            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'TemplatesController@deletes',
                'permission' => 'template.destroy',
            ]);
        });

        Route::group(['prefix' => 'wanted', 'as' => 'wanted.'], function () {
            Route::resource('', 'WantedController')
                ->parameters(['' => 'wanted']);

            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'WantedController@deletes',
                'permission' => 'wanted.destroy',
            ]);
        });

        Route::group(['prefix' => 'category-documents', 'as' => 'category-document.'], function () {
            Route::resource('', 'CategoryDocumentController')
                ->parameters(['' => 'category-document']);

            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'CategoryDocumentController@deletes',
                'permission' => 'document.destroy',
            ]);
        });

        Route::group(['prefix' => 'currencies', 'as' => 'currencies.'], function () {
            Route::resource('', 'CurrenciesController')
                ->parameters(['' => 'currency']);

            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'CurrenciesController@deletes',
                'permission' => 'currencies.destroy',
            ]);
        });

        Route::group(['prefix' => 'projects', 'as' => 'project.'], function () {
            Route::resource('', 'ProjectController')
                ->parameters(['' => 'project']);

            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'ProjectController@deletes',
                'permission' => 'project.destroy',
            ]);
        });

        Route::group(['prefix' => 'property-features', 'as' => 'property_feature.'], function () {
            Route::resource('', 'FeatureController')
                ->parameters(['' => 'property_feature']);

            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'FeatureController@deletes',
                'permission' => 'property_feature.destroy',
            ]);
        });

        Route::group(['prefix' => 'investors', 'as' => 'investor.'], function () {
            Route::resource('', 'InvestorController')
                ->parameters(['' => 'investor']);
            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'InvestorController@deletes',
                'permission' => 'investor.destroy',
            ]);
        });

        Route::group(['prefix' => 'consults', 'as' => 'consult.'], function () {
            Route::resource('', 'ConsultController')
                ->parameters(['' => 'consult'])
                ->except(['create', 'store']);
            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'ConsultController@deletes',
                'permission' => 'consult.destroy',
            ]);
            Route::get('properties/{id}', [
                'as' => 'property.consults',
                'uses' => 'ConsultController@propertyConsults',

            ]);
        });

        Route::group(['prefix' => 'categories', 'as' => 'property_category.'], function () {
            Route::resource('', 'CategoryController')
                ->parameters(['' => 'category']);
            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'CategoryController@deletes',
                'permission' => 'property_category.destroy',
            ]);
        });
        /* Route::get('/', [
             'as'         => 'list',
             'uses'       => 'AccountController@getList',
             'permission' => 'account.index',
         ]);*/
        // Route::group(['prefix' => 'wanted', 'as' => 'wanted.'], function () {
        //     Route::get('/', [
        //         'as' => 'wanted.index',
        //         'uses' => 'WantedController@index',
        //         /*'permission' => 'account.index',*/
        //     ]);
        //     Route::get('/view/{id}', [
        //         'as' => 'view',
        //         'uses' => 'WantedController@view',
        //         /*'permission' => 'account.index',*/
        //     ]);
        //     Route::delete('/destroy/{id}', [
        //         'as' => 'destroy',
        //         'uses' => 'WantedController@destroy',
        //         /*'permission' => 'account.index',*/
        //     ]);

        // });
        //voucher manemnet
        Route::group(['prefix' => 'voucher', 'as' => 'voucher.'], function () {
            /*Route::get('/create/{id}', [
                'as'         => 'create',
                'uses'       => 'VoucherController@create',
                'permission' => 'voucher.create',
            ]);*/
            Route::get('/create', [
                'as' => 'create',
                'uses' => 'VoucherController@create',
                'permission' => 'voucher.create',
            ]);
            Route::post('/create', [
                'as' => 'save',
                'uses' => 'VoucherController@save',
                'permission' => 'voucher.create',
            ]);
            Route::get('/view/{id}', [
                'as' => 'view',
                'uses' => 'VoucherController@view',
                'permission' => 'voucher.view',
            ]);
            Route::get('/edit/{id}', [
                'as' => 'edit',
                'uses' => 'VoucherController@edit',
                'permission' => 'voucher.edit',
            ]);
            Route::post('/edit/{id}', [
                'as' => 'update',
                'uses' => 'VoucherController@update',
                'permission' => 'voucher.edit',
            ]);
            Route::get('/list', [
                'as' => 'list',
                'uses' => 'VoucherController@list',
                'permission' => 'voucher.list',
            ]);
            Route::delete('/destroy/{id}', [
                'as' => 'destroy',
                'uses' => 'VoucherController@destroy',
                'permission' => 'voucher.destroy',
            ]);
            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'VoucherController@deletes',
                'permission' => 'voucher.destroy',
            ]);

        });
        Route::group(['prefix' => 'facilities', 'as' => 'facility.'], function () {
            Route::resource('', 'FacilityController')
                ->parameters(['' => 'facility']);
            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'FacilityController@deletes',
                'permission' => 'facility.destroy',
            ]);
        });

        Route::group(['prefix' => 'accounts', 'as' => 'account.'], function () {

            Route::resource('', 'AccountController')
                ->parameters(['' => 'account']);

            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'AccountController@deletes',
                'permission' => 'account.destroy',
            ]);

            Route::get('list', [
                'as' => 'list',
                'uses' => 'AccountController@getList',
                'permission' => 'account.index',
            ]);

            Route::post('credits/{id}', [
                'as' => 'credits.add',
                'uses' => 'TransactionController@postCreate',
                'permission' => 'account.edit',
            ]);
            Route::get('agent_area', [
                'as' => 'agent_area',
                'uses' => 'AccountController@agent_area',
                'permission' => 'account.agent_area',
            ]);
            Route::get('getAgentAreaList', [
                'as' => 'agent_area_list',
                'uses' => 'AccountController@getAgentInAreas',
                'permission' => 'account.agent_area_list',
            ]);
        });

        Route::group(['prefix' => 'members', 'as' => 'member.'], function () {
            Route::resource('', 'MemberController')
                ->parameters(['' => 'member']);

            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'MemberController@deletes',
                'permission' => 'member.destroy',
            ]);
        });


        ////////////package management/////////
        Route::group(['prefix' => 'packages', 'as' => 'package.'], function () {
            Route::resource('', 'PackageController')
                ->parameters(['' => 'package']);
            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'PackageController@deletes',
                'permission' => 'package.destroy',
            ]);
        });
        /////////////////Voucer Manement/////////////////////
        Route::group(['prefix' => 'packages', 'as' => 'package.'], function () {
            /* Route::resource('', 'PackageController')
                 ->parameters(['' => 'package']);*/
            /* Route::delete('items/destroy', [
                 'as'         => 'deletes',
                 'uses'       => 'PackageController@deletes',
                 'permission' => 'package.destroy',
             ]);*/
        });

    });


    if (defined('THEME_MODULE_SCREEN_NAME')) {
        Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
            Route::get(SlugHelper::getPrefix(Project::class, 'projects'), 'PublicController@getProjects')
                ->name('public.projects');

            Route::get(SlugHelper::getPrefix(Project::class, 'projects') . '/{slug}', 'PublicController@getProject')->name('public.project.show');

            Route::get(SlugHelper::getPrefix(Property::class, 'properties'), 'PublicController@getProperties')
                ->name('public.properties');

            Route::get(
                SlugHelper::getPrefix(Category::class, 'property-category') . '/{slug}',
                'PublicController@getPropertyCategory'
            )
                ->name('public.property-category');

            Route::get(
                SlugHelper::getPrefix(Property::class, 'properties') . '/{slug}',
                'PublicController@getProperty'
            )->name('public.property.show');

            Route::post('send-consult', 'PublicController@postSendConsult')->name('public.send.consult');
            Route::post('send-post', [\Botble\Contact\Http\Controllers\PublicController::class, 'postSendWanted'])
                ->name('public.send.wanted');

            Route::get('currency/switch/{code?}', [
                'as' => 'public.change-currency',
                'uses' => 'PublicController@changeCurrency',
            ]);

            Route::group(['as' => 'public.account.'], function () {

                Route::group(['middleware' => ['account.guest']], function () {
                    Route::get('login', 'LoginController@showLoginForm')
                        ->name('login');
                    Route::post('login', 'LoginController@login')
                        ->name('login.post');

                    Route::get('register', 'RegisterController@showRegistrationForm')
                        ->name('register');
                    Route::post('register', 'RegisterController@register')
                        ->name('register.post');

                    Route::get('verify', 'RegisterController@getVerify')
                        ->name('verify');

                    Route::get(
                        'password/request',
                        'ForgotPasswordController@showLinkRequestForm'
                    )->name('password.request');
                    Route::post(
                        'password/email',
                        'ForgotPasswordController@sendResetLinkEmail'
                    )->name('password.email');

                    Route::post('password/reset', 'ResetPasswordController@reset')
                        ->name('password.update');

                    Route::get(
                        'password/reset/{token}',
                        'ResetPasswordController@showResetForm'
                    )->name('password.reset');
                });

                Route::group([
                    'middleware' => [
                        setting(
                            'verify_account_email',
                            config('plugins.real-estate.real-estate.verify_email')
                        ) ? 'account.guest' : 'account',
                    ],
                ], function () {
                    Route::get(
                        'register/confirm/resend',
                        'RegisterController@resendConfirmation'
                    )
                        ->name('resend_confirmation');
                    Route::get('register/confirm/{email}', 'RegisterController@confirm')
                        ->name('confirm');
                });
            });

            Route::get('feed/properties', [
                'as' => 'feeds.properties',
                'uses' => 'PublicController@getPropertyFeeds',
            ]);
        });

        Route::group(['middleware' => ['account', 'preventBackHistory'], 'as' => 'public.account.'], function () {
            Route::group(['prefix' => 'account'], function () {

                Route::post('logout', 'LoginController@logout')
                    ->name('logout');

                Route::get('dashboard', [
                    'as' => 'dashboard',
                    'uses' => 'PublicAccountController@getDashboard',
                ]);

                Route::get('settings', [
                    'as' => 'settings',
                    'uses' => 'PublicAccountController@getSettings',
                ]);

                Route::post('settings', [
                    'as' => 'post.settings',
                    'uses' => 'PublicAccountController@postSettings',
                ]);

                Route::get('security', [
                    'as' => 'security',
                    'uses' => 'PublicAccountController@getSecurity',
                ]);

                Route::put('security', [
                    'as' => 'post.security',
                    'uses' => 'PublicAccountController@postSecurity',
                ]);

                Route::post('avatar', [
                    'as' => 'avatar',
                    'uses' => 'PublicAccountController@postAvatar',
                ]);

                Route::get('packages', [
                    'as' => 'packages',
                    'uses' => 'PublicAccountController@getPackages',
                ]);

                Route::get('transactions', [
                    'as' => 'transactions',
                    'uses' => 'PublicAccountController@getTransactions',
                ]);

            });

            Route::group(['prefix' => 'ajax/accounts'], function () {
                Route::get('activity-logs', [
                    'as' => 'activity-logs',
                    'uses' => 'PublicAccountController@getActivityLogs',
                ]);

                Route::get('transactions', [
                    'as' => 'ajax.transactions',
                    'uses' => 'PublicAccountController@ajaxGetTransactions',
                ]);

                Route::post('upload', [
                    'as' => 'upload',
                    'uses' => 'PublicAccountController@postUpload',
                ]);

                Route::post('upload-from-editor', [
                    'as' => 'upload-from-editor',
                    'uses' => 'PublicAccountController@postUploadFromEditor',
                ]);
            });

            Route::group(['prefix' => 'account/properties', 'as' => 'properties.'], function () {
                Route::resource('', 'AccountPropertyController')
                    ->parameters(['' => 'property']);

                Route::post('renew/{id}', [
                    'as' => 'renew',
                    'uses' => 'AccountPropertyController@renew',
                ]);

                Route::get('verify/{id}', [
                    'as' => 'verify',
                    'uses' => 'AccountPropertyController@verify',
                ]);
            });
            //resource
            Route::group(['prefix' => 'account/consults', 'as' => 'consult.'], function () {
                Route::resource('', 'AccountConsultController')
                    ->parameters(['' => 'consult'])
                    ->except(['create', 'store']);
                Route::delete('items/destroy', [
                    'as' => 'deletes',
                    'uses' => 'AccountConsultController@deletes',
                    'permission' => 'account.consult.destroy',
                ]);
                Route::get('properties/{id}', [
                    'as' => 'property.consults',
                    'uses' => 'AccountConsultController@propertyConsults',

                ]);
            });
            Route::group(['prefix' => 'ajax/account'], function () {
                Route::get('packages', 'PublicAccountController@ajaxGetPackages')
                    ->name('ajax.packages');
                Route::put('packages', 'PublicAccountController@ajaxSubscribePackage')
                    ->name('ajax.package.subscribe');
            });

            Route::group(['prefix' => 'account'], function () {
                Route::get('packages/{id}/subscribe', 'PublicAccountController@getSubscribePackage')
                    ->name('package.subscribe');

                Route::get(
                    'packages/{id}/subscribe/callback',
                    'PublicAccountController@getPackageSubscribeCallback'
                )->name('package.subscribe.callback');

                Route::get('packages/callback', 'PublicAccountController@packageCallback')
                    ->name('package.callback');
            });
            Route::group(['prefix' => 'account'], function () {
                Route::get('getConsultCount', 'PublicAccountController@getConsultCount')
                    ->name('account.consult.count');

            });

        });

        Route::get('send-mail-for-payment', [PropertyController::class, 'mailForPayment'])
            ->name('mail-for-payment');

        Route::post('save-buyer-info', [PropertyController::class, 'saveBuyerInfo'])
            ->name('save-buyer-info');

        Route::get('Add-Property', [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'create'])
            ->name('general-add-property');
        Route::POST('member-property-save', [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'store'])
            ->name('general-save-property');

        Route::get('get-favourite-properties', [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'getFavouriteProperties'])
            ->name('get-favourite-properties');

        Route::get('ajax/states', [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'getStates'])
            ->name('ajax.states');

        Route::get('ajax/property-cities', [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'getCities'])
            ->name('ajax.property-cities');
        //////////////////////////////members////////////////////////
        /// middleware set here for member////////
        Route::get('member-login', [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'login'])
            ->name('member.login');
        Route::post('member-login', [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'attemptLogin'])
            ->name('login-save');

        Route::get('member-signup', [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'register'])
            ->name('member.register');
        Route::post('member-signup', [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'createMember'])
            ->name('member.register.save');
        Route::get('/member/verify/{token}', function ($token) {

            $member = \Botble\RealEstate\Models\Member::where('verification_token', $token)->first();

            if (!$member) {
                return redirect('/member-login')
                    ->with('error_msg', 'Invalid verification link.');
            }

            $member->email_verified = 1;
            $member->verification_token = null;
            $member->save();

            return redirect('/member-login')
                ->with('success_msg', 'Email verified successfully. You may now login.');
        })->name('member.verify');
        Route::get('wanted', [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'wanted'])
            ->name('wanted');
        Route::get('/property/{property:name}', 'PostController@show')->name('post.show');
        Route::post('/comment/store', 'CommentController@store')->name('comment.add');
        Route::post('/reply/store', 'CommentController@replyStore')->name('reply.add');

        Route::post('/comment/admin/store', 'CommentController@adminStore')->name('comment.add.admin');
        Route::post('/reply/admin/store', 'CommentController@adminReplyStore')->name('reply.add.admin');
        Route::get('member/agent', [
            'uses' => 'GeneralPropertyController@getAgent',
        ]);
        Route::get('ajax/area_unit_update', [

            'uses' => 'GeneralPropertyController@area_unit_update',
        ]);
        Route::get('ajax/currency_unit_update', [

            'uses' => 'GeneralPropertyController@currency_unit_update',
        ]);
        Route::group(['middleware' => ['preventBackHistory', 'member']], function () {


            Route::get('/member/dashboard', [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'dashboard'])
                ->name('member.dashboard');
            Route::get('member/properties', [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'properties'])
                ->name('public.member.properties.index');
            Route::get('/member/properties/create', [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'create_property'])->name('public.member.properties.create');
            Route::post('/member/properties/create', [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'save_property'])->name('public.member.properties.save');
            Route::get('/member/properties/edit/{id}', [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'edit_property'])->name('public.member.properties.edit');
            Route::post('/member/properties/edit/{id}', [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'update_property'])->name('public.member.properties.update');
            Route::delete('/member/properties/{id}', [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'delete_property'])->name('public.member.properties.destroy');
            Route::post('/member/logout', [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'logout'])->name('public.member.logout');

            Route::get('ajax/member/activity-logs', [
                'as' => 'activity-logs',
                'uses' => 'GeneralPropertyController@getActivityLogs',
            ]);

            Route::post(
                'checkout-discount-apply',
                [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'postcheckout']
            )
                ->name('public.member.package.postcheckout-discount');
            Route::get(
                '/term-conditions',
                [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'term_conditions'],
            )->name('gem.terms');
            Route::get('/term-conditions/{file}', [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'term_conditions'])->name('gem.terms.download');

            ///////////////member account settings/////

            Route::get('member/settings', [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'getSettings'])->name('member.settings');
            Route::post('member/settings', [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'postSettings'])->name('public.member.post.settings');
            Route::get('member/security', [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'getSecurity'])->name('public.member.security');
            Route::put('member/security', [\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class, 'postSecurity'])->name('public.member.post.security');
            ////////packages//////
            Route::get('member/packages', [

                'uses' => 'GeneralPropertyController@getPackages',
            ])->name('public.member.packages');
            Route::group(['prefix' => 'ajax/member'], function () {
                Route::get('packages', 'GeneralPropertyController@ajaxGetPackages')
                    ->name('public.member.ajax.packages');
                Route::put('packages', 'GeneralPropertyController@ajaxSubscribePackage')
                    ->name('public.member.ajax.package.subscribe');
            });
            Route::get('member/transactions', [
                'uses' => 'GeneralPropertyController@ajaxGetTransactions',
            ])->name('public.member.ajax.transaction');

            Route::get('member/packages/{id}/subscribe', 'GeneralPropertyController@getSubscribePackage')
                ->name('public.member.package.subscribe');

            Route::get('member/packages/callback', 'GeneralPropertyController@packageCallback')
                ->name('public.member.package.callback');

            Route::get('member/packages/notify', 'GeneralPropertyController@packageNotify')
                ->name('public.member.package.notify');

            Route::post('member/packages/discount', 'GeneralPropertyController@discountPackage')
                ->name('public.member.package.discount');

            Route::get(
                'packages/{id}/subscribe/callback',
                'GeneralPropertyController@getPackageSubscribeCallback'
            )
                ->name('public.member.package.subscribe.callback');
            Route::get(
                'checkout/{id}',
                'GeneralPropertyController@checkout'
            )
                ->name('public.member.package.checkout');
            Route::post(
                'checkout',
                'GeneralPropertyController@postcheckout'
            )
                ->name('public.member.package.postcheckout');

            Route::post('/member/rate', 'GeneralPropertyController@rateSave')->name('member.rate.save');


            /* Route::get('member/agent', 'GeneralPropertyController@getAgent')->name('member.agent.data');*/

        });
        Route::group(['prefix' => 'ajax/member'], function () {


            Route::post('upload', [
                'as' => 'public.member.upload',
                'uses' => 'GeneralPropertyController@postUpload',
            ]);
        });
    }

});

