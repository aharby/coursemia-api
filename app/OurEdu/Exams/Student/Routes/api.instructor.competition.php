<?php
Route::group(['prefix' => 'instructor-competitions', 'as' => 'instructorCompetitions.',
    'namespace' => '\App\OurEdu\Exams\Student\Controllers\Api'], function () {
    Route::get('join-competition/{competitionId}', 'InstructorCompetitionApiController@joinInstructorCompetition')
        ->name('joinInstructorCompetition');

    Route::get('/first-question/{competitionId}', 'InstructorCompetitionApiController@getFirstQuestion')
        ->name('get.first-question');

    Route::post('post-answer/{competitionId}', 'InstructorCompetitionApiController@postAnswer')
        ->name('post.answer');

    Route::get('{competitionId}/questions', 'InstructorCompetitionApiController@getNextOrBackQuestion')
        ->name('get.next-back-questions');
});
