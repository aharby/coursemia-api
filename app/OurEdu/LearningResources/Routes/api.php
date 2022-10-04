<?php
Route::group(['prefix'=>'learning-resources','as'=>'learningResources.', 'middleware' => ['auth:api']], function () {

    Route::get('/', '\App\OurEdu\LearningResources\Controllers\Api\LearningResourceController@getIndex')->name('get.index');
    Route::get('/{slug}', '\App\OurEdu\LearningResources\Controllers\Api\LearningResourceController@getResource')->name('get.resource');

});
