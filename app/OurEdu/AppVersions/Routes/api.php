<?php

Route::group(
    ['prefix' => 'app-version'],
    function () {
        Route::get('/', '\App\OurEdu\AppVersions\Controllers\Api\AppVersionApiController@getVersions');
    }
);
