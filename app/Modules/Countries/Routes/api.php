<?php


use App\Modules\Country\Controllers\CountriesApiControllers;

Route::group(['prefix' => 'countries', 'as' => 'contries.'], function () {
    Route::get('/', [CountriesApiControllers::class , 'index']);
});
