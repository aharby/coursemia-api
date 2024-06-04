<?php

use \App\Modules\Payment\Controllers\CartAPIController;
use \App\Modules\Payment\Controllers\PaymentAPIController;
use \App\Modules\Payment\Controllers\StripeWebhookController;
 
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['userActive'],
    'prefix' => 'cart', 'as' => 'cart.'
], function (){
    Route::group(['middleware' => 'auth:api'], function (){
        Route::get('get-courses',[CartAPIController::class, 'getCourses']);
        
        Route::post('add-course/{course_id}', [CartAPIController::class, 'addCourse']);

        Route::delete('remove-course/{course_id}', [CartAPIController::class, 'removeCourse']);
    });
});

Route::group([
    'middleware' => ['userActive'],
    'prefix' => 'payment', 'as' => 'payment.'
], function (){
    Route::group(['middleware' => 'auth:api'], function (){        

        Route::post('checkout', [PaymentAPIController::class, 'createPaymentIntent']);

        ROUTE::get('check-promocode/{promo_code}', [PaymentAPIController::class, 'isPromoCodeValid']);

    });
});

Route::post('payment/update-status', [StripeWebhookController::class, 'updatePaymentStatus']);