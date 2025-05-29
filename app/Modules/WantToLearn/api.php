<?php
use Illuminate\Support\Facades\Route;
use \App\Modules\WantToLearn\WantToLearnApiController;

Route::group(['prefix' => 'want-to-learn', 'as' => 'want-to-learn.'], function () {
    Route::get('/get-count', [WantToLearnApiController::class , 'getCount']);
});
