<?php
use App\Modules\Config\Controllers\AdminConfigVersionsController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'config', 'as' => 'config.'], function () {
    Route::get('/', [AdminConfigVersionsController::class, 'show']);
    Route::put('/', [AdminConfigVersionsController::class, 'update']);
});
