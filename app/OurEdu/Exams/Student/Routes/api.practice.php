<?php

Route::group(['prefix' => 'practices', 'as' => 'practices.'], function () {
    Route::get('list-practices', '\App\OurEdu\Exams\Student\Controllers\Api\PracticeApiController@listPractices')
        ->name('get.list-practices');

    Route::get('/{practiceId}', '\App\OurEdu\Exams\Student\Controllers\Api\PracticeApiController@viewPractice')
        ->name('get.viewPractice')->where('practiceId', '[0-9]+');

    Route::post('generate-practice', '\App\OurEdu\Exams\Student\Controllers\Api\PracticeApiController@generatePractice')
        ->name('post.generateExam');

    Route::post(
        'post-answer/{practiceId}',
        '\App\OurEdu\Exams\Student\Controllers\Api\PracticeApiController@postAnswer'
    )
        ->name('post.answer');

    Route::post(
        'start-practice/{practiceId}',
        '\App\OurEdu\Exams\Student\Controllers\Api\PracticeApiController@startPractice'
    )
        ->name('post.startPractice');
    Route::post(
        'finish-practice/{practiceId}',
        '\App\OurEdu\Exams\Student\Controllers\Api\PracticeApiController@finishPractice'
    )
        ->name('post.finishPractice');
    Route::get(
        '{practiceId}/questions',
        '\App\OurEdu\Exams\Student\Controllers\Api\PracticeApiController@getNextOrBackQuestion'
    )
        ->name('get.next-back-questions');
});
