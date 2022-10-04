<?php
Route::group(['prefix' => 'competitions', 'as' => 'competitions.'], function () {

    Route::get('list-competitions', '\App\OurEdu\Exams\Student\Controllers\Api\CompetitionApiController@listCompetitions')
        ->name('get.list-competitions');

    Route::get('/{competitionId}', '\App\OurEdu\Exams\Student\Controllers\Api\CompetitionApiController@viewCompetition')
        ->name('get.viewCompetition')->where('competitionId', '[0-9]+');

    Route::get('/{competitionId}/student/{studentId}', '\App\OurEdu\Exams\Student\Controllers\Api\CompetitionApiController@viewCompetitionStudentFeedBack')
        ->name('get.viewCompetitionStudentFeedBack')->where('competitionId', '[0-9]+');

   Route::get('join/{competitionId}', '\App\OurEdu\Exams\Student\Controllers\Api\CompetitionApiController@joinCompetition')
        ->name('get.joinCompetition')->where('competitionId', '[0-9]+');

    Route::post('generate-competition', '\App\OurEdu\Exams\Student\Controllers\Api\CompetitionApiController@generateCompetition')
        ->name('post.generateExam');

    Route::post('post-answer/{competitionId}', '\App\OurEdu\Exams\Student\Controllers\Api\CompetitionApiController@postAnswer')
        ->name('post.answer');

    Route::post('start-competition/{competitionId}', '\App\OurEdu\Exams\Student\Controllers\Api\CompetitionApiController@startCompetition')
       ->name('post.startCompetition');

    Route::post('finish-competition/{competitionId}', '\App\OurEdu\Exams\Student\Controllers\Api\CompetitionApiController@finishCompetition')
        ->name('post.finishCompetition');

    Route::get('{competitionId}/questions', '\App\OurEdu\Exams\Student\Controllers\Api\CompetitionApiController@getNextOrBackQuestion')
        ->name('get.next-back-questions');

//    Route::post('post-answer/{competitionId}', '\App\OurEdu\Exams\Student\Controllers\Api\CompetitionApiController@postAnswer')
//        ->name('post.answer');

    Route::get('/first-question/{competitionId}', '\App\OurEdu\Exams\Student\Controllers\Api\CompetitionApiController@getFirstQuestion')
        ->name('get.first-question');


});
