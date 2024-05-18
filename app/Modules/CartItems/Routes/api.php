<?php

use \App\Modules\CartItems\Controllers\CartItemAPIController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['userActive'],
    'prefix' => 'cart', 'as' => 'cart.'
], function (){
    Route::group(['middleware' => 'auth:api'], function (){
        Route::post('add-course', [CartItemAPIController::class, 'addCourse']);

        Route::delete('remove-course', [CartItemAPIController::class, 'removeCourse']);
    });
});
