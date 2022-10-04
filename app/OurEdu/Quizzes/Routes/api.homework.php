<?php

Route::group(['prefix'=>'homework', 'as'=>'homework.',
    'namespace' => '\App\OurEdu\Quizzes\Controllers\Api'
], function () {

    // HomeWork related routes
    Route::get('/', 'HomeWorkApiController@listAllHomeworks')
        ->name('get.list-homeworks');

    Route::get('/{homeworkId}', 'HomeWorkApiController@getHomework')
        ->name('get.homework');

    Route::get('/session-homework/{classroomSessionId}', 'HomeWorkApiController@getSessionHomework')
        ->name('get.list-session-homework');

    Route::post('/create', 'HomeWorkApiController@createHomeWork')
        ->name('post.create');

    Route::get('/publish/{homeworkId}', 'HomeWorkApiController@publishHomeWork')
        ->name('get.publish');

    Route::put('/edit/{homeworkId}', 'HomeWorkApiController@editHomeWork')
        ->name('put.edit');

    Route::delete('/delete/{homeworkId}', 'HomeWorkApiController@delete')
        ->name('delete');

    // Questions related routes
    Route::get('/{homeworkId}/questions', 'HomeWorkApiController@getQuizQuestions')
        ->name('get.homework.questions');

    Route::post('/{homeworkId}/update-questions', 'HomeWorkApiController@updateHomeworkQuestions')
        ->name('update.homework.questions');

   // Quiz students related routes
    Route::get('/{homeworkId}/students', 'HomeWorkApiController@listHomeworkStudents')
        ->name('get.homework.students');

    Route::get('/{homeworkId}/students/{studentId}', 'HomeWorkApiController@getStudentHomework')
        ->name('get.student.homework');

});
