<?php
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => '\App\Modules\Config\Controllers'], function (){
    Route::get('version', 'VersionsController@getVersions');
});
