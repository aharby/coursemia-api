<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'sessions/preparations',
    'as' => 'session.preparation.',
    'namespace' => '\App\OurEdu\SchoolAdmin\MediaLibrary\Controllers'
], function () {
    Route::get('media/library', 'SessionPreparationController@getMediaLibrary')->name('get.media.library');
    Route::get('media/single/{media}', 'SessionPreparationController@getSingleMedia')->name('get.single.media');
});
