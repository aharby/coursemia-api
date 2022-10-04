<?php
Route::group(['prefix' => 'general-exams', 'as' => 'general_exams.'], function () {
    Route::get('{subjectId}/list-exams/', '\App\OurEdu\GeneralExams\Student\Controllers\Api\GeneralExamApiController@listExams')
        ->name('get.list-exams');

    Route::post('start-exam/{examId}', '\App\OurEdu\GeneralExams\Student\Controllers\Api\GeneralExamApiController@startExam')
        ->name('post.startExam');

    Route::post('post-answer/{examId}', '\App\OurEdu\GeneralExams\Student\Controllers\Api\GeneralExamApiController@postAnswer')
        ->name('post.answer');

    Route::post('finish-exam/{examId}', '\App\OurEdu\GeneralExams\Student\Controllers\Api\GeneralExamApiController@finishExam')
        ->name('post.finish');

    Route::get('{examId}/questions', '\App\OurEdu\GeneralExams\Student\Controllers\Api\GeneralExamApiController@getNextOrBackQuestion')
        ->name('get.next-back-questions');
});
