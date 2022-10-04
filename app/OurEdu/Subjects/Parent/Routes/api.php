<?php

Route::group(['prefix' => 'subjects', 'as' => 'subjects.'], function () {
    Route::get('/', '\App\OurEdu\Subjects\Parent\Controllers\SubjectApiController@getIndex')->name('get.index');
});
