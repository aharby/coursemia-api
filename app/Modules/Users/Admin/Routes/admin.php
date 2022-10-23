<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Users\Admin\Controllers\AuthController;
use \App\Modules\Users\Admin\Controllers\UsersController;

Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
    Route::get('/', [UsersController::class, 'index']);
    Route::put('/{id}', [UsersController::class , 'update']);
    Route::get('/{id}', [UsersController::class , 'show']);
});
