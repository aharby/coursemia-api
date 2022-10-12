<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Users\Admin\Controllers\AuthController;

Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
    Route::post('login', [AuthController::class, 'login']);
});
