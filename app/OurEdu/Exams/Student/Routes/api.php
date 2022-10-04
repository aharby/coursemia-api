<?php
Route::group(['prefix' => 'exams', 'as' => 'exams.'], function () {
    Route::get('list-exams', '\App\OurEdu\Exams\Student\Controllers\Api\ExamApiController@listExams')
        ->name('get.list-exams');

    Route::post('generate-exam', '\App\OurEdu\Exams\Student\Controllers\Api\ExamApiController@generateExam')
        ->name('post.generateExam');

    Route::post('post-answer/{examId}', '\App\OurEdu\Exams\Student\Controllers\Api\ExamApiController@postAnswer')
        ->name('post.answer');

    Route::post('start-exam/{examId}', '\App\OurEdu\Exams\Student\Controllers\Api\ExamApiController@startExam')
       ->name('post.startExam');

    Route::post('finish-exam/{examId}', '\App\OurEdu\Exams\Student\Controllers\Api\ExamApiController@finishExam')
        ->name('post.finishExam');

    Route::get('/retake-exam/{examId}', '\App\OurEdu\Exams\Student\Controllers\Api\ExamApiController@retakeExam')
        ->name('get.retakeExam');

    Route::get('{examId}/questions', '\App\OurEdu\Exams\Student\Controllers\Api\ExamApiController@getNextOrBackQuestion')
        ->name('get.next-back-questions');

    Route::post('post-answer/{examId}', '\App\OurEdu\Exams\Student\Controllers\Api\ExamApiController@postAnswer')
        ->name('post.answer');

    Route::get('dummy-question/{type}', '\App\OurEdu\Exams\Student\Controllers\Api\ExamApiController@dummyQuestion')
        ->name('get.dummy');

    Route::post('/{examId}/challenge', '\App\OurEdu\Exams\Student\Controllers\Api\ExamApiController@challenge')
        ->name('post.challenge');

    Route::get('/{examId}/exam-challenge', '\App\OurEdu\Exams\Student\Controllers\Api\ExamApiController@viewChallengeExam')
        ->name('get.viewChallengeExam');

    Route::get('/{examId}/take', '\App\OurEdu\Exams\Student\Controllers\Api\ExamApiController@take')
        ->name('get.take');

    Route::get('/{examId}', '\App\OurEdu\Exams\Student\Controllers\Api\ExamApiController@viewExam')
        ->name('get.viewExam');
});
