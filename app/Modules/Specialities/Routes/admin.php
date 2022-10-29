<?php


use App\Modules\Specialities\Controllers\SpecialitiesAdminApiControllers;

Route::group(['prefix' => 'specialities', 'as' => 'specialities.'], function () {
    Route::get('/', [SpecialitiesAdminApiControllers::class , 'index']);
    Route::get('/{id}', [SpecialitiesAdminApiControllers::class , 'show']);
    Route::post('/', [SpecialitiesAdminApiControllers::class , 'store']);
    Route::put('/{id}', [SpecialitiesAdminApiControllers::class , 'update']);
    Route::delete('/{id}', [SpecialitiesAdminApiControllers::class , 'destroy']);
});
Route::group(['prefix' => 'all-specialities', 'as' => 'all-specialities.', 'namespace' => '\App\Modules\Specialities\Controllers\API'], function () {
    Route::get('/', [SpecialitiesAdminApiControllers::class, 'all']);
});
