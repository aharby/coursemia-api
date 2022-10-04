<?php
Route::group(['prefix'=>'subjects','as'=>'subjects.'], function () {
    Route::get('/', '\App\OurEdu\Subjects\Admin\Controllers\SubjectController@getIndex')->name('get.index');
    Route::get('/export', '\App\OurEdu\Subjects\Admin\Controllers\SubjectController@exportSubjects')->name('get.export');
    Route::get('/export-subjects-names-images', '\App\OurEdu\Subjects\Admin\Controllers\SubjectController@exportSubjectsNamesAndImages');

    Route::get('create/', '\App\OurEdu\Subjects\Admin\Controllers\SubjectController@getCreate')->name('get.create');
    Route::post('create/', '\App\OurEdu\Subjects\Admin\Controllers\SubjectController@postCreate')->name('post.create');

    Route::get('edit/{id}', '\App\OurEdu\Subjects\Admin\Controllers\SubjectController@getEdit')->name('get.edit');
    Route::put('edit/{id}', '\App\OurEdu\Subjects\Admin\Controllers\SubjectController@putEdit')->name('put.edit');

    Route::get('view/{id}', '\App\OurEdu\Subjects\Admin\Controllers\SubjectController@getView')->name('get.view');

    Route::delete('delete/{id}', '\App\OurEdu\Subjects\Admin\Controllers\SubjectController@delete')->name('delete');

    Route::get('/get-educational-system', '\App\OurEdu\Subjects\Admin\Controllers\SubjectController@getEducationalSystem')->name('get.educational.system');

    //logs
    Route::get('/logs/{id}', '\App\OurEdu\Subjects\Admin\Controllers\SubjectLogsController@listSubjectLogs')->name('get.logs');

    Route::get('/logs/view/{id}', '\App\OurEdu\Subjects\Admin\Controllers\SubjectLogsController@viewSubjectLog')->name('get.viewLog');


    #student-grades-Exam-rate-success
    Route::get('/student-grades/', '\App\OurEdu\Subjects\Admin\Controllers\SubjectController@getSubjectWithCountExamsAndRateResult')->name('get.student-grades');
    Route::get('/student-grades/export/', '\App\OurEdu\Subjects\Admin\Controllers\SubjectController@exportRateResult')->name('get.export.student-grades');


    //Tasks routes
    Route::get('/tasks', '\App\OurEdu\Subjects\Admin\Controllers\TasksController@getIndex')->name('get.index.tasks');
    Route::get('/tasks/{id}', '\App\OurEdu\Subjects\Admin\Controllers\SubjectController@getSubjectTasks')->name('get.subject.tasks');

    //Tasks logs
    Route::get('/tasks/logs/{id}', '\App\OurEdu\Subjects\Admin\Controllers\TaskLogsController@listTaskLogs')->name('get.task.logs');


    //Pause/unpause a Subject should work as a toggle
    Route::post('/pause/{id}', '\App\OurEdu\Subjects\Admin\Controllers\SubjectController@pauseAndUnpause')->name('pause.subject');

    // AJAX Routes
    Route::get('/get-subjects', '\App\OurEdu\Subjects\Admin\Controllers\AjaxSubjectController@getSubjects')->name('get.subjects');
});

Route::group(['prefix'=>'tasks','as'=>'tasks.'], function () {
    Route::get('/content-author', '\App\OurEdu\Subjects\Admin\Controllers\TasksController@contentAuthorTask')->name('get.content.author.tasks');
    Route::get('/content-author/{contentAuthor}', '\App\OurEdu\Subjects\Admin\Controllers\TasksController@contentAuthorTaskDetails')->name('get.content.author.tasks.details');
});

Route::group(['prefix'=>'subject-structure','as'=>'subject-structure.'], function () {
    Route::get('/log-index', '\App\OurEdu\Subjects\Admin\Controllers\SubjectLogsController@listSubjectStructreLogs')->name('get.SubjectStructureLogs');
});

