<?php
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:api', 'namespace' => '\App\Modules\HomeScreen\Controllers'], function (){
    Route::get('get-home-content', 'HomeScreenController@getHomeScreen');
});
