<?php
Route::group(['prefix' => 'quizzes', 'as' => 'quizzes.',
    'namespace' => '\App\OurEdu\Quizzes\Parent\Controller\Api'
], function () {

    Route::get('/{studentId}', 'QuizApiController@getStudentQuizzesPerformance')
        ->name('getStudentQuizzesPerformance');

    Route::get('/{studentId}/quizzes', 'QuizApiController@listStudentQuizzes')
        ->name('listStudentQuizzes');

});
