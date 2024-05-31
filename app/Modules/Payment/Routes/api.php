<?php

use \App\Modules\Payment\Controllers\CartItemAPIController;
use \App\Modules\Payment\Controllers\PaymentAPIController;
 
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['userActive'],
    'prefix' => 'cart', 'as' => 'cart.'
], function (){
    Route::group(['middleware' => 'auth:api'], function (){
        Route::get('get-courses',[CartItemAPIController::class, 'getCourses']);
        
        Route::post('add-course/{course_id}', [CartItemAPIController::class, 'addCourse']);

        Route::delete('remove-course/{course_id}', [CartItemAPIController::class, 'removeCourse']);
    });
});

Route::group([
    'middleware' => ['userActive'],
    'prefix' => 'payment', 'as' => 'payment.'
], function (){
    Route::group(['middleware' => 'auth:api'], function (){        

        Route::post('checkout', [PaymentAPIController::class, 'createPaymentIntent']);

    });
});
