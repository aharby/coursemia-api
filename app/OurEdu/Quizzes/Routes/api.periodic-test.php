<?php

Route::group(['prefix'=>'periodic-test', 'as'=>'periodic-test.',
    'namespace' => '\App\OurEdu\Quizzes\Controllers\Api'
], function () {

    // periodic-test related routes
    Route::get('/', 'PeriodicTestApiController@listAllPeriodicTest')->name('get.list-periodic-tests');
    Route::get('/{periodicTestId}', 'PeriodicTestApiController@getPeriodicTest')->name('get.periodic-test');
//    Route::get('/session-quizzes/{classroomSessionId}', 'PeriodicTestApiController@getSessionHomework')->name('get.list-session-homework');
    Route::post('/create', 'PeriodicTestApiController@createPeriodicTest')->name('post.create');
    Route::get('/publish/{periodicTestId}', 'PeriodicTestApiController@publishPeriodicTest')->name('get.publish');
    Route::put('/edit/{periodicTestId}', 'PeriodicTestApiController@editPeriodicTest')->name('put.edit');
    Route::delete('/delete/{periodicTestId}', 'PeriodicTestApiController@delete')->name('delete');

//    // Questions related routes
    Route::get('/{periodicTestId}/questions', 'PeriodicTestApiController@getPeriodicTestQuestions')->name('get.periodic-test.questions');
    Route::post('/{periodicTestId}/update-questions', 'PeriodicTestApiController@updatePeriodicTestQuestions')->name('update.periodic-test.questions');
//
//    // periodic-test students related routes
    Route::get('/{PeriodicTestId}/students', 'PeriodicTestApiController@listPeriodicTestStudents')->name('get.periodic-test.students');
    Route::get('/{PeriodicTestId}/students/{studentId}', 'PeriodicTestApiController@getStudentPeriodicTest')->name('get.student.periodic-test');

});
