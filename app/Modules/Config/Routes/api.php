<?php

use App\Modules\Config\Controllers\ConfigsController;
use App\Modules\Config\Controllers\VersionsController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => '\App\Modules\Config\Controllers'], function (){
    Route::get('version', 'VersionsController@getVersions');
});
Route::group(['prefix' => 'configs', 'as' => 'configs.'], function () {
    Route::get('/', [ConfigsController::class, 'index']);
});
