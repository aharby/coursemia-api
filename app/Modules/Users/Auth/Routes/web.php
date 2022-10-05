<?php

Route::get('/login', '\App\Modules\Users\Auth\Controllers\AuthController@getLogin')->name('get.login');
Route::get('/login-school-account', '\App\Modules\Users\Auth\Controllers\AuthController@getLoginSchoolAccount')->name('get.schoolLogin');

Route::get('/activate-school-account/{confirmToken}', '\App\Modules\Users\Auth\Controllers\AuthController@getActivateSchoolAccount')->name('get.activate-manager');
Route::post('/activate-school-account/{confirmToken}', '\App\Modules\Users\Auth\Controllers\AuthController@postActivateSchoolAccount')->name('post.activate-manager');

/* type is an optional parameter comes from the web/view */
Route::post('/login/{type?}', '\App\Modules\Users\Auth\Controllers\AuthController@postLogin')->name('post.login');

Route::get('/forgot-password', '\App\Modules\Users\Auth\Controllers\AuthController@getForgotPassword')->name('get.resetPassword');

Route::post('/forgot-password', '\App\Modules\Users\Auth\Controllers\AuthController@postForgotPassword')->name('post.resetPassword');

Route::get('/confirm', '\App\Modules\Users\Auth\Controllers\AuthController@getConfirm');

Route::get('/update-password/{token}', '\App\Modules\Users\Auth\Controllers\AuthController@getUpdatePassword')->name('get.updatePassword');
Route::post('/update-password/{token}', '\App\Modules\Users\Auth\Controllers\AuthController@postUpdatePassword')->name('post.updatePassword');
