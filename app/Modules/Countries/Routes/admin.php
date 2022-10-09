<?php


use App\Modules\Countries\Controllers\CountriesAdminApiControllers;

Route::group(['prefix' => 'countries', 'as' => 'contries.'], function () {
    Route::get('/', [CountriesAdminApiControllers::class , 'index']);
    Route::get('/{id}', [CountriesAdminApiControllers::class , 'show']);
    Route::post('/', [CountriesAdminApiControllers::class , 'store']);
    Route::put('/{id}', [CountriesAdminApiControllers::class , 'update']);
    Route::delete('/{id}', [CountriesAdminApiControllers::class , 'destroy']);
});
