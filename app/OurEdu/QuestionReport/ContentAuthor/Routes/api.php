<?php
Route::group(['prefix' => 'questions-report-task', 'as' => 'question.report.tasks.'], function () {

    Route::get('/',
        '\App\OurEdu\QuestionReport\ContentAuthor\Controllers\Api\QuestionReportTaskController@getAllTasks')->name('index');

    Route::get('/subject/{subject}',
        '\App\OurEdu\QuestionReport\ContentAuthor\Controllers\Api\QuestionReportTaskController@getSubjectTasks')->name('getSubjectTasks');

    Route::post('/pull/{task}',
        '\App\OurEdu\QuestionReport\ContentAuthor\Controllers\Api\QuestionReportTaskController@pullTask')->name('pullTask');

    Route::get('/done/{id}', '\App\OurEdu\QuestionReport\ContentAuthor\Controllers\Api\QuestionReportTaskController@markTaskAsDone')->name('markTaskAsDone');

    Route::post('/fill/{task}',
        '\App\OurEdu\QuestionReport\ContentAuthor\Controllers\Api\QuestionReportTaskController@fillResource')->name('fillResource');

    Route::get('/fill/{task}',
            '\App\OurEdu\QuestionReport\ContentAuthor\Controllers\Api\QuestionReportTaskController@getFillResource')->name('get.fillResource');

});
