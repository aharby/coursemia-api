<?php
Route::group(['prefix'=>'vcr_schedules','as'=>'vcr_schedules.'], function () {
    Route::get('/', '\App\OurEdu\VCRSchedules\Admin\Controllers\VCRSchedulesController@getIndex')->name('get.index');
    Route::get('create/', '\App\OurEdu\VCRSchedules\Admin\Controllers\VCRSchedulesController@getCreate')->name('get.create');
    Route::post('create/', '\App\OurEdu\VCRSchedules\Admin\Controllers\VCRSchedulesController@postCreate')->name('post.create');

    Route::get('edit/{id}', '\App\OurEdu\VCRSchedules\Admin\Controllers\VCRSchedulesController@getEdit')->name('get.edit');
    Route::put('edit/{id}', '\App\OurEdu\VCRSchedules\Admin\Controllers\VCRSchedulesController@putEdit')->name('put.edit');

    Route::get('view/{id}', '\App\OurEdu\VCRSchedules\Admin\Controllers\VCRSchedulesController@getView')->name('get.view');
    Route::delete('delete/{id}', '\App\OurEdu\VCRSchedules\Admin\Controllers\VCRSchedulesController@delete')->name('delete');

    //log
    Route::get('/logs/{id}', '\App\OurEdu\VCRSchedules\Admin\Controllers\VCRSchedulesLogsController@listVCRSchedulesLogs')->name('get.logs');

    Route::get('get/working_dayes/{from}/{to}', '\App\OurEdu\VCRSchedules\Admin\Controllers\VCRSchedulesController@getWorkingDayes')->name('get.names');

});
