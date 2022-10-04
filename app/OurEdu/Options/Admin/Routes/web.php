<?php
Route::group(['prefix'=>'options','as'=>'options.'], function () {

    Route::get('/', '\App\OurEdu\Options\Admin\Controllers\OptionsController@getIndex')->name('get.index');

    Route::get('create/', '\App\OurEdu\Options\Admin\Controllers\OptionsController@getCreate')->name('get.create');
    Route::post('create/', '\App\OurEdu\Options\Admin\Controllers\OptionsController@postCreate')->name('post.create');

    Route::get('edit/{id}', '\App\OurEdu\Options\Admin\Controllers\OptionsController@getEdit')->name('get.edit');
    Route::put('edit/{id}', '\App\OurEdu\Options\Admin\Controllers\OptionsController@putEdit')->name('put.edit');

    Route::get('view/{id}', '\App\OurEdu\Options\Admin\Controllers\OptionsController@getView')->name('get.view');

    Route::delete('delete/{id}', '\App\OurEdu\Options\Admin\Controllers\OptionsController@delete')->name('delete');

    //log
    Route::get('/logs/{id}', '\App\OurEdu\Options\Admin\Controllers\OptionsLogsController@listOptionsLogs')->name('get.logs');

});
