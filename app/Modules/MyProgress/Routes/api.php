<?php
use Illuminate\Support\Facades\Route;
use \App\Modules\MyProgress\Controllers\MyProgressApiController;

Route::group(['prefix' => 'my-progress', 'as' => 'my-progress.'], function () {
    Route::get('/', [MyProgressApiController::class , 'getMyProgress']);
});

