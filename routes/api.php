<?php
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::group(['as' => 'api.'], function () {
    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
        require base_path('app/Modules/Users/Auth/Routes/api.php');
    });

    Route::group(['prefix' => 'home', 'as' => 'home.'], function(){
        require base_path('app/Modules/HomeScreen/Routes/api.php');
    });

    Route::get('test-api',function (){
        return response()->json(
            [
                'code' => 200 ,
                'message' => "hi man"
            ]
        );
    });
});
