<?php
Route::group(['prefix' => 'exams', 'as' => 'exams.'], function () {
    Route::post('generate-exam', '\App\OurEdu\Exams\User\Controllers\Api\ExamApiController@generateExam')
        ->name('post.generateExam');

    Route::get('/{examId}', '\App\OurEdu\Exams\User\Controllers\Api\ExamApiController@viewExam')
        ->name('get.viewExam');
});
