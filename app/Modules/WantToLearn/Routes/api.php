<?php
use Illuminate\Support\Facades\Route;
use \App\Modules\WantToLearn\Controllers\Api\WantToLearnApiController;

Route::group(['prefix' => 'want-to-learn', 'as' => 'want-to-learn.'], function () {
    Route::get('/', [WantToLearnApiController::class , 'getMyWantToLearn']);
    Route::post('/', [WantToLearnApiController::class , 'addWantToLearn']);
    Route::delete('/{id}', [WantToLearnApiController::class , 'deleteWantToLearn']);
});

