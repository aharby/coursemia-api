<?php


use App\Modules\Country\Controllers\FAQApiController;

Route::group(['prefix' => 'countries', 'as' => 'contries.'], function () {
    Route::get('/', [FAQApiController::class , 'index']);
});
