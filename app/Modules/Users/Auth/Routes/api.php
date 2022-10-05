<?php

Route::post('/login', '\App\Modules\Users\Auth\Controllers\Api\AuthApiController@postLogin');

Route::post('/register', '\App\Modules\Users\Auth\Controllers\Api\AuthApiController@postRegister');

Route::post('/validate-basic', '\App\Modules\Users\Auth\Controllers\Api\AuthApiController@validateBasicData');

Route::post('/validate-type', '\App\Modules\Users\Auth\Controllers\Api\AuthApiController@validateTypeData');

Route::get('/activate/{token}', '\App\Modules\Users\Auth\Controllers\Api\AuthApiController@getActivate');

Route::post('/activate-otp', '\App\Modules\Users\Auth\Controllers\Api\AuthApiController@getactivateOtp');

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

    // facebook login/register
Route::post('/provider/facebook', '\App\Modules\Users\Auth\Controllers\Api\AuthApiController@loginAndRegisterUsingFacebook')
    ->name('socialLogin');

Route::post('/login-with-twitter', '\App\Modules\Users\Auth\Controllers\Api\TwitterAuthStatelessApiController@login')
    ->name('loginWithTwitter');

Route::post('/twitter/callback', '\App\Modules\Users\Auth\Controllers\Api\TwitterAuthStatelessApiController@callback')
    ->name('loginCallbackTwitter');

    // twitter
Route::post('/provider/twitter', '\App\Modules\Users\Auth\Controllers\Api\AuthApiController@twitterAuthentication')
    ->name('twitterLogin');


   //logout
Route::post('/logout', '\App\Modules\Users\Auth\Controllers\Api\AuthApiController@logout')->middleware('auth:api');

Route::post('/fcm-tokens', '\App\Modules\Users\Auth\Controllers\Api\AuthApiController@storeFCMToken')->middleware('auth:api');

Route::get('/confirm', '\App\Modules\Users\Auth\Controllers\Api\AuthApiController@getConfirm');

Route::post('/change-language', '\App\Modules\Users\Auth\Controllers\Api\AuthApiController@changeLanguage')->middleware('auth:api');
