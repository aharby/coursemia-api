<?php
Route::group(['prefix' => 'questions-report', 'as' => 'question.report.'], function () {

    Route::get('/',
        '\App\OurEdu\QuestionReport\SME\Controllers\Api\QuestionReportController@getSubjectLists')->name('get.subjectList');

    Route::get('subject/{subject}/',
        '\App\OurEdu\QuestionReport\SME\Controllers\Api\QuestionReportController@listSubjectSections')->name('get.subject');

    Route::get('section/{section}/',
        '\App\OurEdu\QuestionReport\SME\Controllers\Api\QuestionReportController@listSectionSections')->name('get.sections');

    Route::get('/question/{questionId}',
        '\App\OurEdu\QuestionReport\SME\Controllers\Api\QuestionReportController@viewQuestion')->name('get.view.Question');

    Route::post('/ignore/{questionId}',
        '\App\OurEdu\QuestionReport\SME\Controllers\Api\QuestionReportController@ignoreQuestion')->name('post.ignore.Question');

    Route::post('/report/{questionId}',
        '\App\OurEdu\QuestionReport\SME\Controllers\Api\QuestionReportController@reportQuestion')->name('post.report.Question');


});
