<?php

Route::group(['prefix'=>'educational-systems','as'=>'educationalSystems.'],function (){

    Route::get('/', '\App\OurEdu\EducationalSystems\Admin\Controllers\EducationalSystemsController@getIndex')->name('get.index');

    Route::get('create/', '\App\OurEdu\EducationalSystems\Admin\Controllers\EducationalSystemsController@getCreate')->name('get.create');
    Route::post('create/', '\App\OurEdu\EducationalSystems\Admin\Controllers\EducationalSystemsController@postCreate')->name('post.create');

    Route::get('edit/{educationalSystem}', '\App\OurEdu\EducationalSystems\Admin\Controllers\EducationalSystemsController@getEdit')->name('get.edit');
    Route::put('edit/{educationalSystem}', '\App\OurEdu\EducationalSystems\Admin\Controllers\EducationalSystemsController@putEdit')->name('put.edit');

    Route::get('view/{id}', '\App\OurEdu\EducationalSystems\Admin\Controllers\EducationalSystemsController@getView')->name('get.view');

    Route::delete('delete/{id}', '\App\OurEdu\EducationalSystems\Admin\Controllers\EducationalSystemsController@delete')->name('delete');

    // AJAX Routes
    Route::get('/get-educational-system','\App\OurEdu\EducationalSystems\Admin\Controllers\AjaxEducationalSystemsController@getEducationalSystem')->name('get.educational.system');

    //log
    Route::get('/logs/{id}', '\App\OurEdu\EducationalSystems\Admin\Controllers\EducationalSystemsLogsController@listEducationalSystemsLogs')->name('get.logs');

});
