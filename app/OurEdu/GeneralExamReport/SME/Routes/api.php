<?php

Route::group(['prefix' => 'general-exam-reports', 'as' => 'general_exams_reports.', 'namespace' => '\App\OurEdu\GeneralExamReport\SME\Controllers'], function () {

    Route::get('/subjects', 'GeneralExamReportApiController@listSubjects')->name('listSubjects');
    Route::get('/subject/{subject}', 'GeneralExamReportApiController@listSubjectReportedQuestions')->name('get.listSubjectReportedQuestions');
    Route::get('/general-exam/{generalExam}', 'GeneralExamReportApiController@listGeneralExamReportedQuestions')->name('get.listGeneralExamReportedQuestions');
    Route::get('section/{section}/', 'GeneralExamReportApiController@listSectionSections')->name('get.sections');
    Route::get('/{report}/details', 'GeneralExamReportApiController@subjectReportedQuestionDetails')->name('subjectReportedQuestionDetails');
    Route::post('/ignore/{question}', 'GeneralExamReportApiController@ignore')->name('ignore');
    Route::post('/report/{question}', 'GeneralExamReportApiController@report')->name('report');
});
