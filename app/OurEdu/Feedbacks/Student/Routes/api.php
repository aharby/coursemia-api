<?php
Route::group(['prefix'=>'feedbacks','as'=>'feedbacks.'], function () {

    Route::post('/send-feedback', '\App\OurEdu\Feedbacks\Student\Controllers\Api\FeedbacksApiController@postFeedback')
            ->name('post.feedback');
});
