<?php
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['userActive'],
    'namespace' => '\App\Modules\HomeScreen\Controllers',
    'prefix' => 'home', 'as' => 'home.'
], function (){
    Route::get('get-home-content', 'HomeScreenController@getHomeScreen');
});
