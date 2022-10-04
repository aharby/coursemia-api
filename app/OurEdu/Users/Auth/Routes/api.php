<?php

Route::post('/login', '\App\OurEdu\Users\Auth\Controllers\Api\AuthApiController@postLogin');

Route::post('/register', '\App\OurEdu\Users\Auth\Controllers\Api\AuthApiController@postRegister');

Route::post('/validate-basic', '\App\OurEdu\Users\Auth\Controllers\Api\AuthApiController@validateBasicData');

Route::post('/validate-type', '\App\OurEdu\Users\Auth\Controllers\Api\AuthApiController@validateTypeData');

Route::get('/activate/{token}', '\App\OurEdu\Users\Auth\Controllers\Api\AuthApiController@getActivate');

Route::post('/activate-otp', '\App\OurEdu\Users\Auth\Controllers\Api\AuthApiController@getactivateOtp');

Route::Post('/login-otp', '\App\OurEdu\Users\Auth\Controllers\Api\AuthApiController@activateOtp');

Route::post('/refresh-token', '\App\OurEdu\Users\Auth\Controllers\Api\AuthApiController@refreshToken')
    ->name('refreshToken');

Route::post('/forget-password', '\App\OurEdu\Users\Auth\Controllers\Api\PasswordResetApiController@sendPasswordResetMail')
    ->name('sendPasswordResetMail');

Route::post('/reset-password/{token}', '\App\OurEdu\Users\Auth\Controllers\Api\PasswordResetApiController@resetUserPassword')
    ->name('resetUserPassword');

Route::post('/reset-password/send/code', '\App\OurEdu\Users\Auth\Controllers\Api\PasswordResetApiController@sendResetPasswordCode')
    ->name('sendResetPasswordCode');

Route::post('/reset-password/confirm/code', '\App\OurEdu\Users\Auth\Controllers\Api\PasswordResetApiController@confirmResetCode')
    ->name('confirmResetCode');

    // facebook login/register
Route::post('/provider/facebook', '\App\OurEdu\Users\Auth\Controllers\Api\AuthApiController@loginAndRegisterUsingFacebook')
    ->name('socialLogin');

Route::post('/login-with-twitter', '\App\OurEdu\Users\Auth\Controllers\Api\TwitterAuthStatelessApiController@login')
    ->name('loginWithTwitter');

Route::post('/twitter/callback', '\App\OurEdu\Users\Auth\Controllers\Api\TwitterAuthStatelessApiController@callback')
    ->name('loginCallbackTwitter');

    // twitter
Route::post('/provider/twitter', '\App\OurEdu\Users\Auth\Controllers\Api\AuthApiController@twitterAuthentication')
    ->name('twitterLogin');


   //logout
Route::post('/logout', '\App\OurEdu\Users\Auth\Controllers\Api\AuthApiController@logout')->middleware('auth:api');

Route::post('/fcm-tokens', '\App\OurEdu\Users\Auth\Controllers\Api\AuthApiController@storeFCMToken')->middleware('auth:api');

Route::get('/confirm', '\App\OurEdu\Users\Auth\Controllers\Api\AuthApiController@getConfirm');

Route::post('/change-language', '\App\OurEdu\Users\Auth\Controllers\Api\AuthApiController@changeLanguage')->middleware('auth:api');
