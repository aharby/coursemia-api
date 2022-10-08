<?php
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api','userActive'], 'namespace' => '\App\Modules\HomeScreen\Controllers'], function (){
    Route::get('get-home-content', 'HomeScreenController@getHomeScreen');
});
