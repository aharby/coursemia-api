<?php

Route::group(['prefix' => 'school-accounts', 'as' => 'school-accounts.', 'namespace' => '\App\OurEdu\SchoolAccounts\SchoolAccounts\Admin\Controllers'], function () {
    Route::get('/', 'SchoolAccountsController@getIndex')->name('get.index');

    Route::get('create/', 'SchoolAccountsController@getCreate')->name('get.create');

    Route::post('create/', 'SchoolAccountsController@postCreate')->name('post.create');

    Route::get('edit/{id}', 'SchoolAccountsController@getEdit')->name('get.edit');

    Route::put('edit/{id}', 'SchoolAccountsController@putEdit')->name('put.edit');
    Route::get('view/{id}', 'SchoolAccountsController@getView')->name('get.view');
    Route::delete('delete/{id}', 'SchoolAccountsController@delete')->name('delete');

});
