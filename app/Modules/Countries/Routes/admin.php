<?php


use App\Modules\Countries\Controllers\CountriesAdminApiControllers;

Route::group(['prefix' => 'countries', 'as' => 'contries.'], function () {
    Route::get('/', [CountriesAdminApiControllers::class , 'index']);
});
