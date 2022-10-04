<?php

Route::group(['prefix' => 'quizzes', 'as' => 'quizzes.',
    'namespace' => '\App\OurEdu\Quizzes\Student\Controllers\Api'
], function () {

    Route::get('/{quizId}', 'QuizApiController@getQuiz')->name('get.quiz');
    Route::get('/start-quiz/{quizId}/', 'QuizApiController@startQuiz')->name('get.start-quiz');
    Route::get('{quizId}/questions', 'QuizApiController@getNextOrBackQuestion')->name('get.next-back-questions');
    Route::get('/finish-quiz/{quizId}/', 'QuizApiController@finishQuiz')->name('get.finish-quiz');

    Route::post('post-answer/{quizId}', 'QuizApiController@postAnswer')
        ->name('post.answer');
});
