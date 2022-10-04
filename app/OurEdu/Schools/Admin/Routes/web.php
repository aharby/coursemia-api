<?php
Route::group(['prefix'=>'schools','as'=>'schools.'], function () {

    Route::get('/', '\App\OurEdu\Schools\Admin\Controllers\SchoolsController@getIndex')->name('get.index');

    Route::get('create/', '\App\OurEdu\Schools\Admin\Controllers\SchoolsController@getCreate')->name('get.create');
    Route::post('create/', '\App\OurEdu\Schools\Admin\Controllers\SchoolsController@postCreate')->name('post.create');

    Route::get('edit/{id}', '\App\OurEdu\Schools\Admin\Controllers\SchoolsController@getEdit')->name('get.edit');
    Route::put('edit/{id}', '\App\OurEdu\Schools\Admin\Controllers\SchoolsController@putEdit')->name('put.edit');

    Route::get('view/{id}', '\App\OurEdu\Schools\Admin\Controllers\SchoolsController@getView')->name('get.view');

    Route::delete('delete/{id}', '\App\OurEdu\Schools\Admin\Controllers\SchoolsController@delete')->name('delete');

    Route::get('/get-educational-system','\App\OurEdu\Schools\Admin\Controllers\SchoolsController@getEducationalSystem')->name('get.educational.system');

    //log
    Route::get('/logs/{id}', '\App\OurEdu\Schools\Admin\Controllers\SchoolsLogsController@listSchoolsLogs')->name('get.logs');

});
