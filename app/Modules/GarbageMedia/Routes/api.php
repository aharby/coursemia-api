<?php

use App\Modules\GarbageMedia\Controllers\Api\GarbageMediaController;

Route::group(['prefix' => 'media'], function () {
    Route::post('/', [GarbageMediaController::class,'postMedia']);
    Route::post('/multi', [GarbageMediaController::class,'postMediaMulti']);
});
