<?php
use Illuminate\Support\Facades\Route;
use \App\Modules\WantToLearn\Courses\Controllers\Api\WantToLearnApiController;

Route::group(['prefix' => 'want-to-learn/courses', 'as' => 'want-to-learn.'], function () {
    Route::get('/', [WantToLearnApiController::class , 'getMyWantToLearn']);
    Route::post('/{id}', [WantToLearnApiController::class , 'addWantToLearn']);
    Route::delete('/{id}', [WantToLearnApiController::class , 'deleteWantToLearn']);
});

