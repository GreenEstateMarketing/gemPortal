<?php

use Botble\Table\Http\Controllers\TableController;

Route::group(['middleware' => ['web', 'core'], 'prefix' => 'tables'], function () {
    Route::group(['middleware' => 'auth', 'permission' => false], function () {
        Route::get('bulk-change/data', [TableController::class, 'getDataForBulkChanges'])->name('tables.bulk-change.data');
        Route::post('bulk-change/save', [TableController::class, 'postSaveBulkChange'])->name('tables.bulk-change.save');
    });

    // This table filter widget is shared by the admin panel (web guard) as well as the
    // member and agent dashboards (member/account guards), so it must accept any of them
    // instead of only the admin 'web' guard.
    Route::get('get-filter-input', [TableController::class, 'getFilterInput'])
        ->middleware('auth:web,member,account')
        ->name('tables.get-filter-input');
});
