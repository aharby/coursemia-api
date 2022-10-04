<?php

Route::group(['prefix'=>'courses','as'=>'courses.'], function () {
    Route::get('/', '\App\OurEdu\Courses\Parent\Controllers\CourseApiController@getIndex')->name('get.index');
});
