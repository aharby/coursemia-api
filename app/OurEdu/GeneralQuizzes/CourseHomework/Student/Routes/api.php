<?php
Route::group([
    'prefix' => 'student', 'as' => 'student.',

    'namespace'=>'\App\OurEdu\GeneralQuizzes\CourseHomework\Student\Controllers\Api'
], function () {

    Route::get('list-homeworks/', 'CourseHomeworkController@listHomeworks')
        ->name('get.list-homeworks');

    Route::get('list-homeworks/report', 'CourseHomeworkController@listHomeworksReport')
        ->name('get.list-homeworks-report');

    Route::get('{courseHomework}/get-answers', 'CourseHomeworkController@getAnswersStudent')
        ->name('get.student.answers');
    Route::get('/{courseHomework}/get-answers-paginate', 'CourseHomeworkController@getStudentAnswersSolved')
        ->name('get.getStudentAnswersSolved');
});
