<?php
Route::group(['prefix' => 'parent', 'as' => 'parent.',
    'namespace' => '\App\OurEdu\GeneralQuizzes\Parent\Controller\Api'
], function () {
    Route::get('/{studentId}/quizzes', 'GeneralQuizController@listStudentGeneralQuiz')
        ->name('listStudentGeneralQuiz');
    Route::get('/{general_quiz}/{student}/quizzes/answers', 'GeneralQuizController@getStudentGeneralQuizAnswers')
        ->name('listStudentGeneralQuizAnswers');
    Route::get('/{generalQuiz}/get-answers-paginate/{student}', 'GeneralQuizController@getStudentAnswersSolved')
        ->name('get.getStudentAnswersSolved');
});
