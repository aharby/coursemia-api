<?php
Route::group(['prefix'=>'static-pages','as'=>'staticPages.'], function () {
    Route::get('/{pageSlug}/{blockSlug?}', '\App\Modules\StaticPages\Controllers\Api\StaticPagesApiController@getStaticPage')->name('get.static-page');
});

Route::get('/instructors', '\App\Modules\StaticPages\Controllers\Api\StaticPagesApiController@listAllInstructors')
    ->name('list.all-instructors');

Route::get('/instructors/{instructor}', '\App\Modules\StaticPages\Controllers\Api\StaticPagesApiController@showInstructor')
    ->name('show-instructor');
