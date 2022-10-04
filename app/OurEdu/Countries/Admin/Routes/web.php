<?php
Route::group(['prefix'=>'countries','as'=>'countries.'],function (){

    Route::get('/', '\App\OurEdu\Countries\Admin\Controllers\CountriesController@getIndex')->name('get.index');

    Route::get('create/', '\App\OurEdu\Countries\Admin\Controllers\CountriesController@getCreate')->name('get.create');
    Route::post('create/', '\App\OurEdu\Countries\Admin\Controllers\CountriesController@postCreate')->name('post.create');

    Route::get('edit/{id}', '\App\OurEdu\Countries\Admin\Controllers\CountriesController@getEdit')->name('get.edit');
    Route::put('edit/{id}', '\App\OurEdu\Countries\Admin\Controllers\CountriesController@putEdit')->name('put.edit');

    Route::get('view/{id}', '\App\OurEdu\Countries\Admin\Controllers\CountriesController@getView')->name('get.view');

    Route::delete('delete/{id}', '\App\OurEdu\Countries\Admin\Controllers\CountriesController@delete')->name('delete');

    //log
    Route::get('/logs/{id}', '\App\OurEdu\Countries\Admin\Controllers\CountriesLogsController@listCountriesLogs')->name('get.logs');


});

