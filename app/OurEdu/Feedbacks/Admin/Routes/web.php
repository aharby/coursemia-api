<?php

Route::group(['prefix' => 'feedbacks','as'=>'feedbacks.'], function () {
    Route::get('/', '\App\OurEdu\Feedbacks\Admin\Controllers\FeedbacksController@getIndex')->name('get.index');
    Route::get('/approve/{id}', '\App\OurEdu\Feedbacks\Admin\Controllers\FeedbacksController@approve')->name('approve');
    Route::delete('/delete/{id}', '\App\OurEdu\Feedbacks\Admin\Controllers\FeedbacksController@delete')->name('delete');
});
