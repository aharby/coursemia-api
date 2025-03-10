<?php

use App\Enums\StatusCodesEnum;
use App\Modules\Users\Auth\Controllers\Api\AuthApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', '\App\Modules\Users\Auth\Controllers\Api\AuthApiController@postLogin');

Route::post('/register', '\App\Modules\Users\Auth\Controllers\Api\AuthApiController@postRegister');

Route::post('/validate-basic', '\App\Modules\Users\Auth\Controllers\Api\AuthApiController@validateBasicData');

Route::post('/validate-type', '\App\Modules\Users\Auth\Controllers\Api\AuthApiController@validateTypeData');

Route::Post('/login-otp', '\App\Modules\Users\Auth\Controllers\Api\AuthApiController@activateOtp');

Route::post('/refresh-token', '\App\Modules\Users\Auth\Controllers\Api\AuthApiController@refreshToken')
    ->name('refreshToken');

Route::post('/forget-password', '\App\Modules\Users\Auth\Controllers\Api\PasswordResetApiController@sendPasswordResetMail')
    ->name('sendPasswordResetMail');

Route::post('/reset-password/{token}', '\App\Modules\Users\Auth\Controllers\Api\PasswordResetApiController@resetUserPassword')
    ->name('resetUserPassword');

Route::post('/reset-password/send/code', '\App\Modules\Users\Auth\Controllers\Api\PasswordResetApiController@sendResetPasswordCode')
    ->name('sendResetPasswordCode');

Route::post('/reset-password/confirm/code', '\App\Modules\Users\Auth\Controllers\Api\PasswordResetApiController@confirmResetCode')
    ->name('confirmResetCode');

   //logout
Route::post('/logout', '\App\Modules\Users\Auth\Controllers\Api\AuthApiController@logout')->middleware('auth:api');

Route::get('/confirm', '\App\Modules\Users\Auth\Controllers\Api\AuthApiController@getConfirm');

Route::post('/change-language', '\App\Modules\Users\Auth\Controllers\Api\AuthApiController@changeLanguage')->middleware('auth:api');
Route::any('/no-auth', function(){
    return customResponse(null,"Authentication required", 401, StatusCodesEnum::DONE);
});

Route::group(['namespace' => '\App\Modules\Users\Auth\Controllers\Api'], function (){
    Route::post('create-account', 'AuthApiController@register');
    Route::post('delete-devices', 'AuthApiController@deleteDevices');
    Route::post('verify-phone-number', 'AuthApiController@verifyPhone');
    Route::get('verify-email', 'AuthApiController@verifyEmail')->name('verification.verify');
    Route::post('resend-verify-email', 'AuthApiController@resendVerifyEmail')->middleware('auth:api');
    Route::post('reset-password', 'AuthApiController@resetPassword');
    Route::post('send-verification-code', 'AuthApiController@sendVerificationCode');
    Route::post('login', 'AuthApiController@login');
    Route::group(['middleware' => 'userActive'], function (){
        Route::group(['middleware' => 'auth:api'], function (){
            Route::post('change-password', 'AuthApiController@changePassword');
            Route::any('delete-my-device', 'AuthApiController@deleteMyDevice');
            Route::get('get-profile', 'AuthApiController@getProfile');
            Route::post('update-profile', 'AuthApiController@editProfile');
            Route::post('push-device-token', 'AuthApiController@addDeviceToken');
            Route::post('logout', 'AuthApiController@logout');
            Route::get('delete-my-account', 'AuthApiController@deleteMyAccount');
            Route::get('my-devices', 'AuthApiController@myDevices');
            Route::get('get-configurations', 'AuthApiController@getUserConfig');
            Route::any('allow-push-notifications', 'AuthApiController@allowPushNotifications');
        });
    });
});

