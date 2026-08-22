<?php

Route::group([
    'prefix'     => 'api/v1',
    'namespace'  => 'Botble\RealEstate\Http\Controllers\API',
    'middleware' => ['api'],
], function () {

    Route::post('register', 'AuthenticationController@register');
    Route::post('login', 'AuthenticationController@login');

    Route::post('password/forgot', 'ForgotPasswordController@sendResetLinkEmail');

    Route::post('verify-account', 'VerificationController@verify');
    Route::post('resend-verify-account-email', 'VerificationController@resend');
    Route::post('update-checklist','PropertyController@updateChecklist');
    Route::post('assign-checklist','PropertyController@assignChecklist');
    Route::get('get-checklist','PropertyController@getChecklist');
    Route::get('get-checklist-documents','PropertyController@getChecklistDocuments');
    Route::get('get-document-details','PropertyController@getDocumentDetails');
    Route::get('agent-list','AccountController@agent_list');
    Route::get('get_template','AccountController@getTemplate');
    Route::get('agent-data','AccountController@agent_data');
    Route::get('get-term-conditions',[\Botble\RealEstate\Http\Controllers\GeneralPropertyController::class,'term_conditions']);
    Route::group(['middleware' => ['auth:account-api']], function () {
        Route::get('logout', 'AuthenticationController@logout');
        Route::get('me', 'AccountController@getProfile');
        Route::put('me', 'AccountController@updateProfile');
        Route::post('update-avatar', 'AccountController@updateAvatar');
        Route::put('change-password', 'AccountController@updatePassword');
    });
    Route::get('area-units','AccountController@area_units');

});
