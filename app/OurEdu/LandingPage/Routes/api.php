<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'landingPage',
        'namespace' => '\App\OurEdu\LandingPage\Controllers'
    ],
    function () {
        Route::get("courses", "LandingPageController@courses")->name("landingPage.courses.index");
        Route::get("statistics", "LandingPageController@statistics")->name("landingPage.statistics.index");
    }
);
