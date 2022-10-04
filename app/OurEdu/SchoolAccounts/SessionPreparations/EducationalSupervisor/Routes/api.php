<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'=>'session-preparation',
    'as' => 'sessionPreparation.',
    'namespace' =>  '\App\OurEdu\SchoolAccounts\SessionPreparations\EducationalSupervisor\Controllers'
], function () {
    Route::get("/{session}", "SessionPreparationsApiController@getSessions");
    Route::get("/single-media/{preparationMedia}", "SessionPreparationsApiController@getSingleMedia");
});
