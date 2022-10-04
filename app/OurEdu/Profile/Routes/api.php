<?php
Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {

    Route::get('/', '\App\OurEdu\Profile\Controllers\Api\ProfileApiController@getProfile')
        ->middleware(['auth:api', 'throttle:40000,60']);

    Route::post('/update-profile', '\App\OurEdu\Profile\Controllers\Api\ProfileApiController@postUpdateProfile');
    Route::post('/update-language', '\App\OurEdu\Profile\Controllers\Api\ProfileApiController@postUpdateLanguage');

    Route::post('/update-password', '\App\OurEdu\Profile\Controllers\Api\ProfileApiController@postUpdatePassword');

    Route::get('/view-parent/{parentId}', '\App\OurEdu\Profile\Controllers\Api\ProfileApiController@viewParentProfile')
        ->name('view-parent-profile');

    Route::get('/view-child/{childId}', '\App\OurEdu\Profile\Controllers\Api\ProfileApiController@viewChildProfile')
        ->name('view-child-profile');

    Route::get('my-parents', '\App\OurEdu\Profile\Controllers\Api\ProfileApiController@listParents')
        ->name('listParents');

    Route::get('my-students', '\App\OurEdu\Profile\Controllers\Api\ProfileApiController@listStudents')
        ->name('listStudents');

    Route::get('{id}/remove-relation', '\App\OurEdu\Profile\Controllers\Api\ProfileApiController@removeRelation')
        ->name('removeRelation');
    Route::delete('/delete', '\App\OurEdu\Profile\Controllers\Api\ProfileApiController@deleteProfile');
});
