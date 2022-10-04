<?php

Route::group(['prefix' => 'image'], function () {
    Route::post('/', '\App\OurEdu\GarbageMedia\Controllers\Api\GarbageMediaController@postImages');
});

Route::group(['prefix' => 'media'], function () {
    Route::post('/', '\App\OurEdu\GarbageMedia\Controllers\Api\GarbageMediaController@postMedia');

    // this endpoint for uploading media directly
    Route::post('/upload', '\App\OurEdu\GarbageMedia\Controllers\Api\MediaController@uploadMedia');
    Route::post('/file-manager', '\App\OurEdu\GarbageMedia\Controllers\Api\MediaController@fileManager');
    Route::get('/file-manager', '\App\OurEdu\GarbageMedia\Controllers\Api\MediaController@getFileManager');
});
