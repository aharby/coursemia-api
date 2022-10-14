<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'specialities', 'as' => 'specialities.', 'namespace' => '\App\Modules\Specialities\Controllers\API'], function () {
    Route::get('specialities', 'SpecialitiesApiControllers@index');
});
