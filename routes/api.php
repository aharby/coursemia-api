<?php


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

    Route::group(['prefix' => 'courses', 'as' => 'courses.'], function(){
        require base_path('app/Modules/Courses/Routes/api.php');
    });

    require base_path('app/Modules/Countries/Routes/api.php');
    require base_path('app/Modules/GarbageMedia/Routes/api.php');
});
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    require base_path('app/Modules/GarbageMedia/Routes/api.php');
    require base_path('app/Modules/Countries/Routes/admin.php');
    require base_path('app/Modules/Specialities/Routes/admin.php');
});
