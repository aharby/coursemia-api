<?php

Route::group([
        'prefix'=>'school-account-branches',
        'as'=>'school-account-branches.',
        'namespace'=>'\App\OurEdu\SchoolAccounts\SchoolAccountBranches\Admin\Controllers',
    ], function () {

    Route::get('/', 'SchoolAccountBranchesController@getIndex')->name('get.index');
    Route::get('create/', 'SchoolAccountBranchesController@getCreate')->name('get.create');
    Route::post('create/', 'SchoolAccountBranchesController@postCreate')->name('post.create');
    Route::get('edit/{id}', 'SchoolAccountBranchesController@getEdit')->name('get.edit');
    Route::put('edit/{id}', 'SchoolAccountBranchesController@putEdit')->name('put.edit');
    Route::get('view/{id}', 'SchoolAccountBranchesController@getView')->name('get.view');
    Route::delete('delete/{id}', 'SchoolAccountBranchesController@delete')->name('delete');

});
