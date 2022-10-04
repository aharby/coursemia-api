<?php

Route::group(['prefix'=>'quizzes', 'as'=>'quizzes.',
    'namespace' => '\App\OurEdu\Quizzes\Controllers\Api'
], function () {

    // Quiz related routes
    Route::get('/', 'QuizApiController@listAllQuizzes')->name('get.list-quizzes');
    Route::get('/{quizId}', 'QuizApiController@getQuiz')->name('get.quiz');
    Route::get('/session-quizzes/{classroomSessionId}', 'QuizApiController@getSessionQuizzes')->name('get.list-session-quizzes');
    Route::post('/create', 'QuizApiController@createQuiz')->name('post.create');
    Route::get('/publish/{quizId}', 'QuizApiController@publishQuiz')->name('get.publish');
    Route::put('/edit/{quizId}', 'QuizApiController@editQuiz')->name('put.edit');
    Route::delete('/delete/{quizId}', 'QuizApiController@delete')->name('delete');

    // Questions related routes
    Route::get('/{quizId}/questions', 'QuizApiController@getQuizQuestions')->name('get.quiz.questions');
    Route::post('/{quizId}/update-questions', 'QuizApiController@updateQuizQuestions')->name('update.quiz.questions');

    // Quiz students related routes
    Route::get('/{quizId}/students', 'QuizApiController@listQuizStudents')->name('get.quiz.students');
    Route::get('/{quizId}/students/{studentId}', 'QuizApiController@getStudentQuiz')->name('get.student.quiz');

});

Route::group(['prefix'=>'all-quizzes', 'as'=>'all-quizzes.',
    'namespace' => '\App\OurEdu\Quizzes\Controllers\Api'
], function () {

    // All Quizzes types
    Route::get('/', 'QuizApiController@listAllQuizzesTypes')->name('get.list-all-quizzes');
});
