<?php

Route::group(['prefix' => 'general-exams', 'as' => 'general_exams.', 'namespace' => '\App\OurEdu\GeneralExams\SME\Controllers'], function () {
    Route::get('/{exam}/subject-sections/{subject}', 'GeneralExamApiController@getSubjectSections')->name('getSubjectSections');

    Route::get('/{exam}/section-questions/{section}', 'GeneralExamApiController@getSectionQuestions')->name('getSectionQuestions');

    Route::get('/', 'GeneralExamApiController@index')->name('index');

    Route::post('/', 'GeneralExamApiController@storeGeneralExam')->name('storeGeneralExam');

    Route::get('/{exam}', 'GeneralExamApiController@view')->name('view');

    Route::post('/{exam}', 'GeneralExamApiController@update')->name('update');

    Route::get('/{exam}/publish', 'GeneralExamApiController@publish')->name('publish');

    Route::get('/{exam}/delete', 'GeneralExamApiController@delete')->name('delete');

    Route::post('/{exam}/update-questions', 'GeneralExamApiController@updateQuestions')->name('updateQuestions');

    Route::get('/exam-students/{examId}', 'GeneralExamApiController@getGeneralExamStudents')
        ->name('generalExamStudents');
});
