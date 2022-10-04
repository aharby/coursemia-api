<?php
Route::group(['prefix' => 'subjects', 'as' => 'subjects.'], function () {
    Route::get('/', '\App\OurEdu\Subjects\SME\Controllers\Api\SubjectApiController@getIndex')->name('get.index');
    Route::post('generate-resources-tasks', '\App\OurEdu\Subjects\SME\Controllers\Api\ResourceSubjectFormatApiController@generateTasks')->name('generate-tasks');

    Route::post('generate-subject-tasks/{subjectId}', '\App\OurEdu\Subjects\SME\Controllers\Api\ResourceSubjectFormatApiController@generateSubjectTasks')->name('generateSubjectTasks');

    Route::get('get-single-resource/{resourceId}', '\App\OurEdu\Subjects\SME\Controllers\Api\ResourceSubjectFormatApiController@getSingleResource')->name('get-single-resource');
    Route::delete('delete-single-resource/{resourceId}', '\App\OurEdu\Subjects\SME\Controllers\Api\ResourceSubjectFormatApiController@deleteSingleResource')->name('deleteSingleResource');
    Route::get('/{id}', '\App\OurEdu\Subjects\SME\Controllers\Api\SubjectApiController@getSubject')->name('get.subject')->where('id', '[0-9]+');
    Route::get('/{id}/minimal', '\App\OurEdu\Subjects\SME\Controllers\Api\SubjectApiController@getSubjectWithMinimalData')->name('get.subject-with-minimal-data');
    Route::put('/{id}/minimal', '\App\OurEdu\Subjects\SME\Controllers\Api\SubjectApiController@updateSubjectWithMinimalData')->name('put.updateSubjectWithMinimalData');

    Route::put('/{id}/public-library', '\App\OurEdu\Subjects\SME\Controllers\Api\SubjectApiController@updatePublicLibrary')->name('put.updatePublicLibrary');

    Route::get('/{id}/details', '\App\OurEdu\Subjects\SME\Controllers\Api\SubjectApiController@getSubjectDetails')->name('get.subjectDetails')->where('id', '[0-9]+');
    Route::put('/{id}/structural', '\App\OurEdu\Subjects\SME\Controllers\Api\SubjectApiController@updateSubjectStructural')->name('update-structural.subject');
    Route::put('/{id}/section-structural', '\App\OurEdu\Subjects\SME\Controllers\Api\SubjectFormatSubjectApiController@createOrUpdateSectionStructure')->name('update-section-structural.subject');
    Route::delete('/{id}/section-structural', '\App\OurEdu\Subjects\SME\Controllers\Api\SubjectFormatSubjectApiController@deleteSection')->name('delete-section-structural.subject');
    Route::put('sections/{section}/resource-structural', '\App\OurEdu\Subjects\SME\Controllers\Api\ResourceSubjectFormatApiController@postCreateResourceSubjectFormatStructure')->name('create-resource-structural');
    Route::post('/{id}/attach-media', '\App\OurEdu\Subjects\SME\Controllers\Api\SubjectApiController@attachMedia')->name('attach-media.subject');
    Route::post('/{id}/delete-media', '\App\OurEdu\Subjects\SME\Controllers\Api\SubjectApiController@deleteMedia')->name('delete-media.subject');

    Route::get('/tasks/{id}', '\App\OurEdu\Subjects\SME\Controllers\Api\TaskApiController@getView')->name('get.view_task');

    Route::get('/tasks-performance', '\App\OurEdu\Subjects\SME\Controllers\Api\TaskApiController@tasksPerformance')->name('get.performance');
    Route::get('/tasks-performance-list/{contentAuthor}', '\App\OurEdu\Subjects\SME\Controllers\Api\TaskApiController@tasksPerformanceList')->name('get.performance.list');

    Route::get('/{id}/tasks', '\App\OurEdu\Subjects\SME\Controllers\Api\TaskApiController@getSubjectTasks')->name('get.task.index');

    Route::get('/tasks', '\App\OurEdu\Subjects\SME\Controllers\Api\TaskApiController@getAllTasks')->name('get.subject.tasks');
    Route::get('/{id}/tasks', '\App\OurEdu\Subjects\SME\Controllers\Api\TaskApiController@getSubjectTasks')->name('get.task.index');

    Route::post('/{id}/pause-unpause', '\App\OurEdu\Subjects\SME\Controllers\Api\SubjectApiController@pauseUnPauseSubject')->name('pause-unpause.subject');



    Route::get('/clone-subject/{id}', '\App\OurEdu\Subjects\SME\Controllers\Api\SubjectApiController@getCloneSubject')->name('get.clone-subject');


    Route::post('/clone-subject/{id}', '\App\OurEdu\Subjects\SME\Controllers\Api\SubjectApiController@postCloneSubject')->name('post.clone-subject');

    Route::get('/view-subject-format-subject/{sectionId}', '\App\OurEdu\Subjects\SME\Controllers\Api\SubjectFormatSubjectApiController@viewSubjectFormatSubjectDetails')->name('viewSubjectFormatSubjectDetails');

    Route::get('/get-section-structure/{sectionId}', '\App\OurEdu\Subjects\SME\Controllers\Api\SubjectFormatSubjectApiController@getSectionStructure')->name('get.sectionStructure')->where('id', '[0-9]+');

    Route::post('/subject-format-pause-unpause/{subjectFormatId}', '\App\OurEdu\Subjects\SME\Controllers\Api\SubjectFormatSubjectApiController@pauseUnPauseSubjectFormat')
        ->name('pause-unpause.subjectFormat');

    Route::get('/edit-resource-subject-format/{resourceSubjectFormatId}', '\App\OurEdu\Subjects\SME\Controllers\Api\ResourceSubjectFormatApiController@getEditResourceSubjectFormat')
        ->name('get-edit-resource');

    Route::put('/edit-resource-subject-format/{resourceSubjectFormatId}', '\App\OurEdu\Subjects\SME\Controllers\Api\ResourceSubjectFormatApiController@postEditResourceSubjectFormat')
        ->name('put-edit-resource');

    Route::post('/resource-pause-unpause/{resourceId}', '\App\OurEdu\Subjects\SME\Controllers\Api\ResourceSubjectFormatApiController@pauseUnPauseResource')
        ->name('pause-unpause.resource');

    Route::get('/edit-resource-subject-format/mark-task-as-done/{task_id}', '\App\OurEdu\Subjects\SME\Controllers\Api\ResourceSubjectFormatApiController@markTaskAsDone')
        ->name('post.sme.markTaskAsDone');


});
