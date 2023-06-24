<?php
use App\Modules\Config\Controllers\ConfigsAdminController;
use App\Modules\Config\Controllers\ConfigsController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'config', 'as' => 'config.'], function () {
    Route::get('/', [ConfigsAdminController::class, 'show']);
    Route::put('/', [ConfigsAdminController::class, 'update']);
});
