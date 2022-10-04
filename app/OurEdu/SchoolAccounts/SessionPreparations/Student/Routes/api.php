<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'=>'session-preparation',
    'as' => 'sessionPreparation.',
    'namespace' =>  '\App\OurEdu\SchoolAccounts\SessionPreparations\Student\Controllers'
], function () {
    Route::get("/{session}", "SessionPreparationsApiController@getSessions");
    Route::get("/single-media/{preparationMedia}", "SessionPreparationsApiController@getSingleMedia");
    Route::get("/download/{preparationMedia}", "SessionPreparationsApiController@downloadMedia");
});

Route::group([
    'prefix'=>'media-library',
    'as' => 'mediaLibrary.',
    'namespace' =>  '\App\OurEdu\SchoolAccounts\SessionPreparations\Student\Controllers'
], function () {
    Route::get("/", "SessionPreparationsApiController@mediaLibrary");
});
