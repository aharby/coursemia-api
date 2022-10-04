<?php
Route::group(['prefix'=>'instructors','as'=>'instructors.'], function () {

    Route::get('/', '\App\OurEdu\Instructors\Admin\Controllers\InstructorsController@getIndex')->name('get.index');
    Route::get('/view/{id}', '\App\OurEdu\Instructors\Admin\Controllers\InstructorsController@getView');
    Route::get('/details/{id}', '\App\OurEdu\Instructors\Admin\Controllers\InstructorsController@getDetails');
    Route::get('/export', '\App\OurEdu\Instructors\Admin\Controllers\InstructorsController@export')->name('admin.get.export');

});
